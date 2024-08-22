<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

final class ForceRedirectException extends \Exception
{
    /**
     * @param string $url
     * @param int $statusCode
     */
    public function __construct(string $url, int $statusCode = 301)
    {
        parent::__construct($url, $statusCode, null);
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $statusCode
     *
     * @return \App\Core\Exceptions\ForceRedirectException
     */
    public function setStatusCode(int $statusCode): self
    {
        $this->code = $statusCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->message;
    }

    /**
     * @param string $url
     *
     * @return \App\Core\Exceptions\ForceRedirectException
     */
    public function setUrl(string $url): self
    {
        $this->message = $url;
        return $this;
    }
}
