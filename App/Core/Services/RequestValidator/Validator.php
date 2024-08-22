<?php

declare(strict_types=1);

namespace App\Core\Services\RequestValidator;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator as IlluminateValidator;

/**
 * @method \App\Core\Services\RequestValidator\ErrorMessageBag messages
 * @method \App\Core\Services\RequestValidator\ErrorMessageBag errors
 * @method \App\Core\Services\RequestValidator\ErrorMessageBag getMessageBag
 */
final class Validator extends IlluminateValidator
{
    use ExtractsAttributesTrait;

    /**
     * The message bag instance.
     *
     * @var \App\Core\Services\RequestValidator\ErrorMessageBag
     */
    protected $messages;

    /**
     * Determine if the data passes the validation rules.
     *
     * @return bool
     */
    public function passes(): bool
    {
        $this->messages = new ErrorMessageBag();
        $this->distinctValues = [];
        $this->failedRules = [];

        // We'll spin through each rule, validating the attributes attached to that
        // rule. Any error messages will be added to the containers with each of
        // the other error messages, returning true if we don't have messages.
        foreach ($this->rules as $attribute => $rules) {
            $attribute = \str_replace('\.', '->', $attribute);

            foreach ($rules as $rule) {
                $this->validateAttribute($attribute, $rule);

                if ($this->shouldStopValidating($attribute)) {
                    break;
                }
            }
        }

        // Here we will spin through all of the "after" hooks on this validator and
        // fire them off. This gives the callbacks a chance to perform all kinds
        // of other validation that needs to get wrapped up in this operation.
        foreach ($this->after as $after) {
            $after();
        }

        return $this->messages->isEmpty();
    }

    /**
     * Replace all error message place-holders with actual values.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array $parameters
     *
     * @return array{message: string, params: mixed}
     */
    public function makeReplacements($message, $attribute, $rule, $parameters): array
    {
        $message = $this->replaceAttributePlaceholder(
            $message,
            $this->getDisplayableAttribute($attribute)
        );

        $message = $this->replaceInputPlaceholder($message, $attribute);

        $extractor = "extract{$rule}";
        if (\method_exists($this, $extractor)) {
            $parameters = $this->{$extractor}($attribute, $parameters);
        }

        foreach ($parameters as $key => $value) {
            $message = \str_replace((string) $key, $value, $message);
        }

        return [
            'message' => $message,
            'params' => $parameters,
        ];
    }

    /**
     * Check if attribute is valid.
     *
     * @param string $attribute
     *
     * @return bool
     */
    public function isValid(string $attribute): bool
    {
        return ! $this->messages->has($attribute);
    }

    /**
     * Validate an attribute using a custom rule object.
     *
     * @param string $attribute
     * @param mixed $value
     * @param \Illuminate\Contracts\Validation\Rule $rule
     */
    protected function validateUsingCustomRule($attribute, $value, $rule): void
    {
        if ($rule instanceof ValidatorAwareRule) {
            $rule->setValidator($this);
        }

        if ($rule instanceof DataAwareRule) {
            $rule->setData($this->data);
        }

        if (! $rule->passes($attribute, $value)) {
            $this->failedRules[$attribute][$rule::class] = [];

            $messages = $this->getFromLocalArray($attribute, $rule::class) ?? $rule->message();

            $messages = $messages ? (array) $messages : [$rule::class];

            foreach ($messages as $key => $message) {
                $key = \is_string($key) ? $key : $attribute;

                if (\is_string($message)) {
                    $this->messages->add($key, $this->makeReplacements(
                        $message,
                        $key,
                        $rule::class,
                        []
                    ));
                } elseif (\is_array($message) && Arr::exists($message, 'message')) {
                    $this->messages->add($key, $this->makeReplacements(
                        $message['message'],
                        $key,
                        $rule::class,
                        $message['params'] ?? []
                    ));
                }
            }
        }
    }

    /**
     * Check if we should stop further validations on a given attribute.
     *
     * @param string $attribute
     *
     * @return bool
     */
    protected function shouldStopValidating($attribute): bool
    {
        if ($this->messages->has($attribute)) {
            return true;
        }

        return parent::shouldStopValidating($attribute);
    }
}
