<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Helpers\Utils;

final class Application extends \Illuminate\Foundation\Application
{
    /**
     * @var string
     */
    protected $appPath = __DIR__;

    /**
     * Register a service provider with the application.
     *
     * @param \Illuminate\Support\ServiceProvider|string $provider
     * @param bool $force
     */
    public function unregister(\Illuminate\Support\ServiceProvider | string $provider, bool $force = false): void
    {
        $registered = $this->getProvider($provider);

        if (! $registered && ! $force) {
            return;
        }

        if (\is_string($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        if (\property_exists($provider, 'bindings')) {
            foreach (\array_keys($provider->bindings) as $key) {
                $this->offsetUnset((string) $key);
            }
        }

        if (\property_exists($provider, 'singletons')) {
            foreach (\array_keys($provider->singletons) as $key) {
                $this->offsetUnset((string) $key);
            }
        }

        $index = \array_search($provider, $this->serviceProviders, true);
        if ($index !== false) {
            unset($this->serviceProviders[$index]);
        }

        unset($this->loadedProviders[$provider::class]);
    }

    /**
     * Get absolute path to the specified container.
     *
     * @param string $container
     *
     * @return string
     */
    public function getContainerPath(string $container): string
    {
        return Utils::joinPaths($this->basePath('App/Containers'), $container);
    }

    protected function registerBaseBindings(): void
    {
        $this->instance(self::class, $this);

        parent::registerBaseBindings();
    }
}
