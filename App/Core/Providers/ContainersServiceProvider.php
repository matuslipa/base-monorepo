<?php

declare(strict_types=1);

namespace App\Core\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class ContainersServiceProvider
 *
 * @package App\Core\Providers
 */
final class ContainersServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerAuthContainer();

        $this->registerUsersContainer();
    }

    private function registerUsersContainer(): void
    {
        $this->app->bind(
            \App\Containers\Users\Contracts\UsersRepositoryInterface::class,
            \App\Containers\Users\Repositories\UsersRepository::class
        );
    }

    private function registerAuthContainer(): void
    {
        $this->app->bind(
            \App\Containers\Authentication\Contracts\UserTokensRepositoryInterface::class,
            \App\Containers\Authentication\Repositories\UserTokensRepository::class
        );
        $this->app->bind(
            \App\Containers\Authentication\Contracts\UserSessionsRepositoryInterface::class,
            \App\Containers\Authentication\Repositories\UserSessionsRepository::class
        );
    }
}
