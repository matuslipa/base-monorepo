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
        $this->registerUsersContainer();

        $this->registerPatientsContainer();
    }

    private function registerUsersContainer(): void
    {
        $this->app->bind(
            \App\Containers\Users\Contracts\UsersRepositoryInterface::class,
            \App\Containers\Users\Repositories\UsersRepository::class
        );
    }

    private function registerPatientsContainer(): void
    {
        $this->app->bind(
            \App\Containers\Patients\Contracts\PatientsRepositoryInterface::class,
            \App\Containers\Patients\Repositories\PatientsRepository::class
        );
    }
}
