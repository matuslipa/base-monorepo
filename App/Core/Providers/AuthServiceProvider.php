<?php

declare(strict_types=1);

namespace App\Core\Providers;

use App\Containers\Authentication\Providers\AppUserProvider;

use App\Containers\Users\Contracts\UsersRepositoryInterface;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

final class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Auth::provider(
            AppUserProvider::class,
            static fn (Application $app): AppUserProvider => new AppUserProvider(
                $app['hash'],
                $app->make(UsersRepositoryInterface::class),
            )
        );
    }
}
