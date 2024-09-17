<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Middlewares\ThrottleRequestsMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpFoundation\Request;

/**
 * The application's route middleware groups.
 *
 * @var array<string, array<class-string|string>>
 */
$middlewareGroups = [
    'web' => [
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],

    'api' => [
        'throttle:1000,1',
    ],
];

/**
 * Previous $routeMiddleware
 */
$middlewareAliases = [
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'throttle' => ThrottleRequestsMiddleware::class,
];

$eventListeners = [
];

$app = Application::configure(basePath: dirname(__DIR__))
    ->withEvents($eventListeners)
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api-v0.php',
        health: '/up',
    )
    ->withMiddleware(static function (Middleware $middleware) use (&$middlewareAliases, &$middlewareGroups): void {
        $middleware->use([
            \App\Core\Middlewares\ResponseHeadersMiddleware::class,
        ]);
        $middleware->preventRequestsDuringMaintenance();
        $middleware->use([
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        ]);
        $middleware->trimStrings([
            'password',
            'password_confirmation',
        ]);
        $middleware->convertEmptyStringsToNull();
        $middleware->trustProxies(headers: Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO);
        $middleware->use([\App\Core\Middlewares\ContentLanguageMiddleware::class]);

        $middleware->api($middlewareGroups['api']);
        $middleware->web($middlewareGroups['web']);

        $middleware->alias($middlewareAliases);
    })
    ->withExceptions(static function (Exceptions $exceptions): void {
        Integration::handles($exceptions);

        $exceptions->dontReport([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Session\TokenMismatchException::class,
            \Illuminate\Validation\ValidationException::class,
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
            \App\Core\Exceptions\ForceRedirectException::class,
            \App\Core\Exceptions\ValidationException::class,
        ]);

        $exceptions->renderable((new \App\Core\Exceptions\ExceptionHandler())->handle(...));
    })
    ->create();


$app->useAppPath($app->basePath('App'));

return $app;
