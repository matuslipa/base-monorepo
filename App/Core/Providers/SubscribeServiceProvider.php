<?php

declare(strict_types=1);

namespace App\Core\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

final class SubscribeServiceProvider extends ServiceProvider
{
    /**
     * @var array<array-key, class-string<\App\Core\Contracts\EventSubscriberInterface>>
     */
    protected array $subscribe = [
    ];

    public function register(): void
    {
        $this->booting(function (): void {
            foreach ($this->subscribe as $subscriber) {
                Event::subscribe($subscriber);
            }
        });
    }
}
