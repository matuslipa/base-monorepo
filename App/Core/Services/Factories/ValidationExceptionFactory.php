<?php

declare(strict_types=1);

namespace App\Core\Services\Factories;

use App\Core\Exceptions\ValidationException;
use App\Core\Values\Translation;

final class ValidationExceptionFactory
{
    /**
     * @param \Illuminate\Validation\ValidationException $exception
     *
     * @return \App\Core\Exceptions\ValidationException
     */
    public function createFromIlluminateException(
        \Illuminate\Validation\ValidationException $exception
    ): ValidationException {
        $errors = [];

        foreach ($exception->validator->errors()->getMessages() as $field => $error) {
            $params = [];

            if (\is_string($error) || $error instanceof Translation) {
                $message = $error;
            } else {
                $message = $error[0]['message'] ?? $error['message'];
                $params = $error[0]['params'] ?? [];
            }

            $errors[$field] = Translation::make(
                $message,
                \array_merge([
                    'attribute' => $field,
                ], $params)
            );
        }

        return (new ValidationException($exception->getMessage(), $exception, $exception->getCode()))
            ->setErrors($errors);
    }
}
