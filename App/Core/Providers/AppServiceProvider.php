<?php

declare(strict_types=1);

namespace App\Core\Providers;

use App\Core\Parents\Models\Model;
use App\Core\Services\RequestValidator\Validator;
use Carbon\CarbonImmutable;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\DateFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Factory as ValidationFactory;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot(): void
    {
        // temporary because of old mariadb version on plesk
        Schema::defaultStringLength(191);

        // Log DB queries
        if (config('database.logging')) {
            $this->app->make(DatabaseManager::class)->listen(static function (QueryExecuted $query): void {
                Log::debug("Query \"{$query->sql}\" executed in {$query->time}ms.", $query->bindings);
            });
        }

        $this->app->make(ValidationFactory::class)->resolver(
            static fn ($translator, $data, $rules, $messages): Validator => new Validator($translator, $data, $rules, $messages)
        );
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerApplicationEnvironment();

        if (! $this->app->environment('production')) {
            $this->registerNonProductionEnvironment();

            if ($this->app->environment('local')) {
                $this->registerLocalEnvironment();
            }
        }

        // register factory name resolver
        Factory::guessFactoryNamesUsing(static function (string $modelName): string {
            $container = Str::between($modelName, 'Containers\\', '\\Models');
            $modelName = Str::after($modelName, 'Models\\');

            // check factory exists just for section (to avoid many nested directories)
            $parts = \explode('\\', $container);
            $class = "Database\\Factories\\{$parts[0]}\\{$modelName}Factory";
            if (\class_exists($class)) {
                return $class;
            }

            return "Database\\Factories\\{$container}\\{$modelName}Factory";
        });
    }

    /**
     * Register application environment.
     */
    private function registerApplicationEnvironment(): void
    {
        // Use CarbonImmutable for casting dates
        DateFactory::use(CarbonImmutable::class);

        //        $this->app->singleton(ResponseManager::class, static fn (Application $app): \App\Core\Services\ResponseManager\ResponseManager => new ResponseManager($app->make(ConfigRepository::class)));
        //
        //        $this->app->bind(DatabaseManagerInterface::class, static fn (Application $app): \App\Core\Services\DatabaseManager\MysqlDatabaseManager => new MysqlDatabaseManager($app->make(DatabaseManager::class)->connection('root')));
        //
        //        $this->app->bind(
        //            DatabaseProcessorInterface::class,
        //            static fn (Application $app): \App\Core\Services\ClosureTable\DbProcessors\MysqlProcessor => new MysqlProcessor($app->make(DatabaseManager::class))
        //        );
    }

    private function registerLocalEnvironment(): void
    {
        Model::shouldBeStrict(true);
    }

    /**
     * Register non-production environment.
     */
    private function registerNonProductionEnvironment(): void
    {
        $this->app->register(\Laravel\Tinker\TinkerServiceProvider::class);
    }
}
