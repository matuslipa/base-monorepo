<?php

declare(strict_types=1);

namespace App\Core\Services;

/**
 * Class Logger
 *
 * @package App\Core\Services
 */
final class Logger extends \Illuminate\Log\Logger
{
    /**
     * @param string $message
     */
    public function reportMessageToSentry(string $message): void
    {
        \Sentry\captureMessage($message);
    }

    /**
     * @param \Throwable $throwable
     */
    public function reportExceptionToSentry(\Throwable $throwable): void
    {
        \Sentry\captureException($throwable);
    }
}
