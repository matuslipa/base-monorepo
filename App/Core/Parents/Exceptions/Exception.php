<?php

declare(strict_types=1);

namespace App\Core\Parents\Exceptions;

use App\Core\Values\Enums\ResponseTypeEnum;
use App\Core\Values\Translation;
use Illuminate\Contracts\Translation\Translator;

abstract class Exception extends \Exception
{
    /**
     * @var \App\Core\Values\Translation
     */
    protected Translation $translation;

    /**
     * @var mixed[]
     */
    private array $errors = [];

    /**
     * ApiResponseException constructor.
     *
     * @param \App\Core\Values\Translation|string $message
     * @param int|mixed $code
     * @param null|\Throwable $previous
     */
    public function __construct(string | Translation $message, ?\Throwable $previous = null, $code = 0)
    {
        if ($message instanceof Translation) {
            $this->translation = $message;
            $message = $message->toString();
        }

        parent::__construct($message, (int) $code, $previous);
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 500;
    }

    /**
     * @return \App\Core\Values\Enums\ResponseTypeEnum
     */
    public function getResponseType(): ResponseTypeEnum
    {
        return ResponseTypeEnum::GENERAL();
    }

    /**
     * Get response headers.
     *
     * @return mixed[]
     */
    public function getResponseHeaders(): array
    {
        return [];
    }

    /**
     * @return \App\Core\Values\Translation
     */
    public function getTranslation(): Translation
    {
        return $this->translation ?? ($this->translation = Translation::make($this->message));
    }

    /**
     * @param null|\Illuminate\Contracts\Translation\Translator $translator
     *
     * @return string
     */
    public function toLocalizedString(?Translator $translator = null): string
    {
        return $this->getTranslation()->toString($translator);
    }

    /**
     * Set errors.
     *
     * @param mixed[] $errors
     *
     * @return \App\Core\Exceptions\ValidationException
     */
    public function setErrors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Has some errors?
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return (bool) $this->errors;
    }
}
