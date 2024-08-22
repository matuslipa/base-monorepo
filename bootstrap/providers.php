<?php

declare(strict_types=1);

return [
    /*
     * Application Service Providers...
     */
    \App\Core\Providers\AppServiceProvider::class,
    \App\Core\Providers\ContainersServiceProvider::class,
    \App\Core\Providers\AuthServiceProvider::class,
    \App\Core\Providers\SubscribeServiceProvider::class,
    \App\Core\Providers\CommandServiceProvider::class,
];
