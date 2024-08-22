<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use App\Core\Parents\Exceptions\Exception;
use App\Core\Values\Enums\ResponseTypeEnum;

final class GeneralException extends Exception
{
    /**
     * @var int
     */
    private int $statusCode = 500;

    /**
     * @var null|\App\Core\Values\Enums\ResponseTypeEnum
     */
    private ?ResponseTypeEnum $responseType = null;

    /**
     * @var array<string, string>
     */
    private array $responseHeaders = [];

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return \App\Core\Values\Enums\ResponseTypeEnum
     */
    public function getResponseType(): ResponseTypeEnum
    {
        return $this->responseType ?? ResponseTypeEnum::GENERAL();
    }

    /**
     * @param int $statusCode
     *
     * @return \App\Core\Exceptions\GeneralException
     */
    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param \App\Core\Values\Enums\ResponseTypeEnum $responseType
     *
     * @return \App\Core\Exceptions\GeneralException
     */
    public function setResponseType(ResponseTypeEnum $responseType): self
    {
        $this->responseType = $responseType;
        return $this;
    }

    /**
     * Set message.
     *
     * @param string $message
     *
     * @return \App\Core\Exceptions\GeneralException
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getResponseHeaders(): array
    {
        return $this->responseHeaders;
    }

    /**
     * @param array<string, string> $responseHeaders
     */
    public function setResponseHeaders(array $responseHeaders): void
    {
        $this->responseHeaders = $responseHeaders;
    }
}
