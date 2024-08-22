<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use App\Core\Parents\Exceptions\Exception;
use App\Core\Values\Enums\ResponseTypeEnum;

class TooManyRequestsException extends Exception
{
    /**
     * @var int
     */
    private int $retryAfter;

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 429;
    }

    /**
     * @return \App\Core\Values\Enums\ResponseTypeEnum
     */
    public function getResponseType(): ResponseTypeEnum
    {
        return ResponseTypeEnum::TOO_MANY_REQUESTS();
    }

    /**
     * Set retry Retry-After value in seconds.
     *
     * @param int $seconds
     *
     * @return \App\Core\Exceptions\TooManyRequestsException
     */
    public function retryAfter(int $seconds): self
    {
        $this->retryAfter = $seconds;
        return $this;
    }

    /**
     * Get response headers.
     *
     * @return mixed[]
     */
    public function getResponseHeaders(): array
    {
        return \array_merge(parent::getResponseHeaders(), [
            'Retry-After' => $this->retryAfter,
        ]);
    }
}
