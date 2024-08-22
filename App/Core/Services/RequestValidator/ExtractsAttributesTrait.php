<?php

declare(strict_types=1);

namespace App\Core\Services\RequestValidator;

use Illuminate\Support\Arr;

trait ExtractsAttributesTrait
{
    /**
     * Get the size of an attribute.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return mixed
     */
    abstract protected function getSize($attribute, $value);

    /**
     * Get the value of a given attribute.
     *
     * @param string $attribute
     *
     * @return mixed
     */
    abstract protected function getValue($attribute);

    /**
     * Parse named parameters to $key => $value items.
     *
     * @param array $parameters
     *
     * @return array
     */
    abstract protected function parseNamedParameters($parameters);

    /**
     * Replace all place-holders for the between rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractBetween(string $attribute, array $parameters): array
    {
        return [
            'min' => $parameters[0],
            'max' => $parameters[1],
        ];
    }

    /**
     * Replace all place-holders for the date_format rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractDateFormat(string $attribute, array $parameters): array
    {
        return [
            'format' => $parameters[0],
        ];
    }

    /**
     * Replace all place-holders for the different rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractDifferent(string $attribute, array $parameters): array
    {
        return $this->extractSame($attribute, $parameters);
    }

    /**
     * Replace all place-holders for the digits rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractDigits(string $attribute, array $parameters): array
    {
        return [
            'digits' => $parameters[0],
        ];
    }

    /**
     * Replace all place-holders for the digits (between) rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractDigitsBetween(string $attribute, array $parameters): array
    {
        return $this->extractBetween($attribute, $parameters);
    }

    /**
     * Replace all place-holders for the min rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractMin(string $attribute, array $parameters): array
    {
        return [
            'min' => $parameters[0],
        ];
    }

    /**
     * Replace all place-holders for the max rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractMax(string $attribute, array $parameters): array
    {
        return [
            'max' => $parameters[0],
        ];
    }

    /**
     * Replace all place-holders for the in rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractIn(string $attribute, array $parameters): array
    {
        return [
            'values' => \implode(', ', $parameters),
        ];
    }

    /**
     * Replace all place-holders for the not_in rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractNotIn(string $attribute, array $parameters): array
    {
        return $this->extractIn($attribute, $parameters);
    }

    /**
     * Replace all place-holders for the in_array rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractInArray(string $attribute, array $parameters): array
    {
        return [
            'other' => $parameters[0],
        ];
    }

    /**
     * Replace all place-holders for the mime types rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractMimetypes(string $attribute, array $parameters): array
    {
        return [
            'values' => \implode(', ', $parameters),
        ];
    }

    /**
     * Replace all place-holders for the mimes rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractMimes(string $attribute, array $parameters): array
    {
        return [
            'values' => \implode(', ', $parameters),
        ];
    }

    /**
     * Replace all place-holders for the required_with rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractRequiredWith(string $attribute, array $parameters): array
    {
        return ['values', \implode(' / ', $parameters)];
    }

    /**
     * Replace all place-holders for the required_with_all rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractRequiredWithAll(string $attribute, array $parameters): array
    {
        return $this->extractRequiredWith($attribute, $parameters);
    }

    /**
     * Replace all place-holders for the required_without rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractRequiredWithout(string $attribute, array $parameters): array
    {
        return $this->extractRequiredWith($attribute, $parameters);
    }

    /**
     * Replace all place-holders for the required_without_all rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractRequiredWithoutAll(string $attribute, array $parameters): array
    {
        return $this->extractRequiredWith($attribute, $parameters);
    }

    /**
     * Replace all place-holders for the size rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractSize(string $attribute, array $parameters): array
    {
        return [
            'size' => $parameters[0],
        ];
    }

    /**
     * Replace all place-holders for the gt rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractGt(string $attribute, array $parameters): array
    {
        $value = $this->getValue($parameters[0]);
        if (($value) === null) {
            return [
                'value' => $parameters[0],
            ];
        }

        return ['value', $this->getSize($attribute, $value)];
    }

    /**
     * Replace all place-holders for the lt rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractLt(string $attribute, array $parameters): array
    {
        $value = $this->getValue($parameters[0]);
        if (($value) === null) {
            return [
                'value' => $parameters[0],
            ];
        }

        return ['value', $this->getSize($attribute, $value)];
    }

    /**
     * Replace all place-holders for the gte rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractGte(string $attribute, array $parameters): array
    {
        $value = $this->getValue($parameters[0]);
        if (($value) === null) {
            return [
                'value' => $parameters[0],
            ];
        }

        return ['value', $this->getSize($attribute, $value)];
    }

    /**
     * Replace all place-holders for the lte rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractLte(string $attribute, array $parameters): array
    {
        $value = $this->getValue($parameters[0]);
        if (($value) === null) {
            return [
                'value' => $parameters[0],
            ];
        }

        return ['value', $this->getSize($attribute, $value)];
    }

    /**
     * Replace all place-holders for the required_if rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractRequiredIf(string $attribute, array $parameters): array
    {
        return [
            'other' => $parameters[0],
            'value' => Arr::get($this->data, $parameters[0]),
        ];
    }

    /**
     * Replace all place-holders for the required_unless rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractRequiredUnless(string $attribute, array $parameters): array
    {
        $values = \array_slice($parameters, 1);

        return [
            'other' => $parameters[0],
            'values' => \implode(', ', $values),
        ];
    }

    /**
     * Replace all place-holders for the same rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractSame(string $attribute, array $parameters): array
    {
        return [
            'other' => $parameters[0],
        ];
    }

    /**
     * Replace all place-holders for the before rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractBefore(string $attribute, array $parameters): array
    {
        return [
            'date' => $parameters[0],
        ];
    }

    /**
     * Replace all place-holders for the before_or_equal rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractBeforeOrEqual(string $attribute, array $parameters): array
    {
        return $this->extractBefore($attribute, $parameters);
    }

    /**
     * Replace all place-holders for the after rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractAfter(string $attribute, array $parameters): array
    {
        return $this->extractBefore($attribute, $parameters);
    }

    /**
     * Replace all place-holders for the after_or_equal rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractAfterOrEqual(string $attribute, array $parameters): array
    {
        return $this->extractBefore($attribute, $parameters);
    }

    /**
     * Replace all place-holders for the date_equals rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractDateEquals(string $attribute, array $parameters): array
    {
        return $this->extractBefore($attribute, $parameters);
    }

    /**
     * Replace all place-holders for the dimensions rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractDimensions(string $attribute, array $parameters): array
    {
        return $this->parseNamedParameters($parameters);
    }

    /**
     * Replace all place-holders for the starts_with rule.
     *
     * @param string $attribute
     * @param array $parameters
     *
     * @return mixed[]
     */
    protected function extractStartsWith(string $attribute, array $parameters): array
    {
        return [
            'values' => \implode(', ', $parameters),
        ];
    }
}
