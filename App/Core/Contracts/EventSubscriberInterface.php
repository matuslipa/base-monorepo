<?php

declare(strict_types=1);

namespace App\Core\Contracts;

use Illuminate\Contracts\Events\Dispatcher;

interface EventSubscriberInterface
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $dispatcher
     */
    public function subscribe(Dispatcher $dispatcher): void;
}
