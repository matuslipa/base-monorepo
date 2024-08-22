<?php

declare(strict_types=1);

namespace App\Core\Services\RequestThrottleService;

use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiter;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class FrontRateLimiter
{
    /**
     * Create a new request throttler.
     *
     * @param \Illuminate\Cache\RateLimiter $limiter
     */
    public function __construct(
        private readonly RateLimiter $limiter
    ) {
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
    public function resolveRequestSignature(\Illuminate\Http\Request $request): string
    {
        $user = $request->user();
        if ($user) {
            return \sha1((string) $user->getAuthIdentifier());
        }

        /** @var mixed $route */
        $route = $request->route();
        if ($route) {
            return \sha1($route->getDomain() . '|' . $request->ip());
        }

        throw new RuntimeException('Unable to generate the request signature. Route unavailable.');
    }

    /**
     * Get the number of seconds until the next retry.
     *
     * @param string $key
     *
     * @return int
     */
    public function getTimeUntilNextRetry(string $key): int
    {
        return $this->limiter->availableIn($key);
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
    public function calculateRemainingAttempts(string $key, int $maxAttempts, ?int $retryAfter = null): int
    {
        if ($retryAfter === null) {
            return $this->limiter->retriesLeft($key, $maxAttempts);
        }

        return 0;
    }

    /**
     * @param string $key
     * @param int $maxAttempts
     *
     * @return bool
     */
    public function reachedTooManyAttempts(string $key, int $maxAttempts): bool
    {
        return $this->limiter->tooManyAttempts($key, $maxAttempts);
    }

    /**
     * @param string $key
     * @param int $decaySeconds
     */
    public function hit(string $key, int $decaySeconds): void
    {
        $this->limiter->hit($key, $decaySeconds);
    }

    /**
     * @param string $key
     */
    public function reset(string $key): void
    {
        $this->limiter->resetAttempts($key);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param int $maxAttempts
     * @param int $remainingAttempts
     */
    public function setResponseHeaders(
        Response $response,
        int $maxAttempts,
        int $remainingAttempts,
        $retryAfter = null
    ): void {
        $response->headers->add(
            $this->getHeaders($maxAttempts, $remainingAttempts, $retryAfter)
        );
    }

    /**
     * Get the limit headers information.
     *
     * @param int $maxAttempts
     * @param int $remainingAttempts
     * @param null|int $retryAfter
     *
     * @return mixed[]
     */
    public function getHeaders(int $maxAttempts, int $remainingAttempts, ?int $retryAfter = null): array
    {
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
     * Get the "available at" UNIX timestamp.
     *
     * @param \DateInterval|\DateTimeInterface|int $delay
     *
     * @return int
     */
    private function availableAt(\DateTimeInterface | \DateInterval | int $delay = 0): int
    {
        if ($delay instanceof \DateInterval) {
            $delay = CarbonImmutable::now()->add($delay);
        }

        return $delay instanceof \DateTimeInterface
            ? $delay->getTimestamp()
            : CarbonImmutable::now()->addSeconds($delay)->getTimestamp();
    }
}
