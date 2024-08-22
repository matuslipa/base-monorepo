<?php

declare(strict_types=1);

namespace App\Core\Providers;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\ServiceProvider;

final class CommandServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bypassing Laravel's command registration
        $this->app->singleton(\Illuminate\Database\Migrations\Migrator::class, static fn ($app) => $app['migrator']);

        $this->app->singleton(ConnectionResolverInterface::class, static fn ($app) => $app['db']);
    }
}
