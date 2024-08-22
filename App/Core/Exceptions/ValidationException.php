<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use App\Core\Parents\Exceptions\Exception;
use App\Core\Values\Enums\ResponseTypeEnum;

final class ValidationException extends Exception
{
    /**
     * @param string $message
     * @param mixed[] $errors
     *
     * @return \App\Core\Exceptions\ValidationException
     */
    public static function makeWithErrors(string $message, array $errors): self
    {
        $exception = new self($message);
        $exception->setErrors($errors);
        return $exception;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 422;
    }

    /**
     * @return \App\Core\Values\Enums\ResponseTypeEnum
     */
    public function getResponseType(): ResponseTypeEnum
    {
        return ResponseTypeEnum::VALIDATION_ERROR();
    }
}
