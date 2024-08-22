<?php

declare(strict_types=1);

namespace App\Core\Kernels;

use App\Core\Middlewares\ThrottleRequestsMiddleware;
use Illuminate\Foundation\Http\Kernel as FrameworkHttpKernel;

/**
 * @deprecated Everything should be registered in bootstrap/app.php
 */
final class HttpKernel extends FrameworkHttpKernel
{
    /**
     * @inheritDoc
     */
    protected $middleware = [
        \App\Core\Middlewares\ResponseHeadersMiddleware::class,
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Core\Middlewares\TrustProxiesMiddleware::class,
        \App\Core\Middlewares\ContentLanguageMiddleware::class,
    ];

    /**
     * @inheritDoc
     */
    protected $middlewareGroups = [
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
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string>
     */
    protected $routeMiddleware = [
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'throttle' => ThrottleRequestsMiddleware::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array<array-key, class-string>
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,
    ];
}
