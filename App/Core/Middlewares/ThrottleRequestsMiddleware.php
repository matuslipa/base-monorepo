<?php

declare(strict_types=1);

namespace App\Core\Middlewares;

use App\Core\Exceptions\TooManyRequestsException;
use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Unlimited;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\InteractsWithTime;
use RuntimeException;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

final class ThrottleRequestsMiddleware
{
    use InteractsWithTime;

    /**
     * Create a new request throttler.
     *
     * @param  \Illuminate\Cache\RateLimiter  $limiter
     */
    public function __construct(
        protected readonly RateLimiter $limiter,
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param int|string $maxAttempts
     * @param float|int $decayMinutes
     * @param string $prefix
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Http\Exceptions\ThrottleRequestsException
     * @throws \App\Core\Exceptions\TooManyRequestsException
     */
    public function handle(Request $request, Closure $next, int|string $maxAttempts = 60, float|int $decayMinutes = 1, string $prefix = ''): Response
    {
        if (
            \is_string($maxAttempts)
            && \func_num_args() === 3
        ) {
            $limiter = $this->limiter->limiter($maxAttempts);

            return $this->handleRequestUsingNamedLimiter($request, $next, $maxAttempts, $limiter);
        }

        return $this->handleRequest(
            $request,
            $next,
            [
                (object) [
                    'key' => $prefix . $this->resolveRequestSignature($request),
                    'maxAttempts' => $this->resolveMaxAttempts($request, $maxAttempts),
                    'decayMinutes' => $decayMinutes,
                    'responseCallback' => null,
                ],
            ]
        );
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \Closure  $next
     * @param string $limiterName
     * @param  \Closure  $limiter
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Http\Exceptions\ThrottleRequestsException
     */
    protected function handleRequestUsingNamedLimiter(Request $request, Closure $next, string $limiterName, Closure $limiter): Response
    {
        $limiterResponse = $limiter($request);

        if ($limiterResponse instanceof Response) {
            return $limiterResponse;
        } elseif ($limiterResponse instanceof Unlimited) {
            return $next($request);
        }

        return $this->handleRequest(
            $request,
            $next,
            collect(Arr::wrap($limiterResponse))->map(static function ($limit) use ($limiterName): \stdClass {
                return (object) [
                    'key' => \md5($limiterName . $limit->key),
                    'maxAttempts' => $limit->maxAttempts,
                    'decayMinutes' => $limit->decayMinutes,
                    'responseCallback' => $limit->responseCallback,
                ];
            })->all()
        );
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param array<int, stdClass> $limits
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Http\Exceptions\ThrottleRequestsException
     * @throws \App\Core\Exceptions\TooManyRequestsException
     */
    protected function handleRequest(Request $request, Closure $next, array $limits): Response
    {
        foreach ($limits as $limit) {
            if ($this->limiter->tooManyAttempts($limit->key, $limit->maxAttempts)) {
                throw $this->buildException($request, $limit->key, $limit->maxAttempts, $limit->responseCallback);
            }

            $this->limiter->hit($limit->key, $limit->decayMinutes * 60);
        }

        $response = $next($request);

        foreach ($limits as $limit) {
            $response = $this->addHeaders(
                $response,
                $limit->maxAttempts,
                $this->calculateRemainingAttempts($limit->key, $limit->maxAttempts)
            );
        }

        return $response;
    }

    /**
     * Resolve the number of attempts if the user is authenticated or not.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|string $maxAttempts
     *
     * @return int
     */
    protected function resolveMaxAttempts(Request $request, int|string $maxAttempts): int
    {
        if (\str_contains((string) $maxAttempts, '|')) {
            $maxAttempts = \explode('|', (string) $maxAttempts, 2)[$request->user() ? 1 : 0];
        }

        if (! \is_numeric($maxAttempts) && $request->user()) {
            $maxAttempts = $request->user()->{$maxAttempts};
        }

        return (int) $maxAttempts;
    }

    /**
     * Resolve request signature.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $user = $request->user();
        $route = $request->route();

        if ($user instanceof Authenticatable) {
            return \sha1((string) $user->getAuthIdentifier());
        } elseif ($route instanceof Route) {
            return \sha1($route->getDomain() . '|' . $request->ip());
        }

        throw new RuntimeException('Unable to generate the request signature. Route unavailable.');
    }

    /**
     * Create a 'too many attempts' exception.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $key
     * @param int $maxAttempts
     *
     * @return \App\Core\Exceptions\TooManyRequestsException
     */
    protected function buildException(Request $request, string $key, int $maxAttempts, $responseCallback = null): TooManyRequestsException
    {
        $retryAfter = $this->getTimeUntilNextRetry($key);

        $this->getHeaders(
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );

        $exception = new TooManyRequestsException('exceptions.too_many_requests');
        $exception->retryAfter($retryAfter);

        return $exception;
    }

    /**
     * Get the number of seconds until the next retry.
     *
     * @param  string  $key
     *
     * @return int
     */
    protected function getTimeUntilNextRetry(string $key): int
    {
        return $this->limiter->availableIn($key);
    }

    /**
     * Add the limit header information to the given response.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @param int $maxAttempts
     * @param int $remainingAttempts
     * @param null|int $retryAfter
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addHeaders(Response $response, int $maxAttempts, int $remainingAttempts, int $retryAfter = null): Response
    {
        $response->headers->add(
            $this->getHeaders($maxAttempts, $remainingAttempts, $retryAfter, $response)
        );

        return $response;
    }

    /**
     * Get the limit headers information.
     *
     * @param int $maxAttempts
     * @param int $remainingAttempts
     * @param null|int $retryAfter
     * @param  null|\Symfony\Component\HttpFoundation\Response  $response
     *
     * @return array<string, mixed>
     */
    protected function getHeaders(
        int $maxAttempts,
        int $remainingAttempts,
        int $retryAfter = null,
        ?Response $response = null
    ): array {
        if ($response &&
            $response->headers->get('X-RateLimit-Remaining') !== null &&
            (int) $response->headers->get('X-RateLimit-Remaining') <= $remainingAttempts) {
            return [];
        }

        $headers = [
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ];

        if ($retryAfter !== null) {
            $headers['Retry-After'] = $retryAfter;
            $headers['X-RateLimit-Reset'] = $this->availableAt($retryAfter);
        }

        return $headers;
    }

    /**
     * Calculate the number of remaining attempts.
     *
     * @param string $key
     * @param int $maxAttempts
     * @param null|int $retryAfter
     *
     * @return int
     */
    protected function calculateRemainingAttempts(string $key, int $maxAttempts, int $retryAfter = null): int
    {
        return $retryAfter === null ? $this->limiter->retriesLeft($key, $maxAttempts) : 0;
    }
}
