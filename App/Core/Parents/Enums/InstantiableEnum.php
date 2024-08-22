<?php

declare(strict_types=1);

namespace App\Core\Parents\Enums;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class InstantiableEnum implements \JsonSerializable, \Stringable
{
    /**
     * The enum constants.
     *
     * @var mixed[]
     */
    protected static array $constants = [];

    /**
     * Creates a new value of some type
     *
     * @param mixed $value
     *
     * @throws \UnexpectedValueException if incompatible type is given.
     */
    public function __construct(
        protected mixed $value
    ) {
        if ($value instanceof static) {
            $this->value = $value->getValue();
            return;
        }

        if (! self::isValid($value)) {
            throw new \UnexpectedValueException("Value '{$value}' is not part of the enum " . static::class);
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Returns a value when called statically like so: MyEnum::SOME_VALUE() given SOME_VALUE is a class constant
     *
     * @param string $name
     * @param array $arguments
     *
     * @return static
     *
     * @throws \BadMethodCallException
     */
    public static function __callStatic(string $name, array $arguments)
    {
        $constants = static::constants();

        if ($constants->has($name)) {
            return new static($constants->get($name));
        }

        throw new \BadMethodCallException(
            "No static method or enum constant '{$name}' in class " . static::class
        );
    }

    /**
     * Fluent constructor.
     *
     * @param mixed $value
     *
     * @return static
     */
    public static function make(mixed $value): self
    {
        return new static($value);
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Check if is valid enum value
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isValid(mixed $value): bool
    {
        return $value instanceof static || \in_array($value, static::values(), true);
    }

    /**
     * Compares one Enum with another.
     *
     * @param null|\App\Core\Parents\Enums\InstantiableEnum $enum
     *
     * @return bool True if Enums are equal, false if not equal
     */
    final public function equals(?self $enum = null): bool
    {
        return $enum !== null && $this->getValue() === $enum->getValue() && $enum instanceof static;
    }

    /**
     * Compares one Enum with another.
     *
     * @param \App\Core\Parents\Enums\InstantiableEnum[] $values
     *
     * @return bool True if value equals to at least one from given, false if not
     */
    final public function in(array $values): bool
    {
        return Arr::first($values, fn (self $value): bool => $this->equals($value)) !== null;
    }

    /**
     * Get the enum keys.
     *
     * @return array
     */
    public static function keys(): array
    {
        return static::constants()->keys()->all();
    }

    /**
     * Get the enum values.
     *
     * @return array
     */
    public static function values(): array
    {
        return static::constants()->values()->toArray();
    }

    /**
     * Get the enum constants.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function constants(): Collection
    {
        if (! isset(static::$constants[static::class])) {
            try {
                static::$constants[static::class] = collect(
                    (new \ReflectionClass(static::class))->getConstants()
                );
            } catch (\ReflectionException) {
                // do nothing
            }
        }

        return static::$constants[static::class];
    }

    /**
     * Convert enum to array.
     *
     * @return mixed
     */
    public static function toArray(): array
    {
        $result = [];
        $hasLabels = \method_exists(static::class, 'labels');
        $labels = $hasLabels ? \call_user_func([static::class, 'labels']) : [];

        foreach (static::constants() as $const => $value) {
            $result[] = [
                'value' => $value,
                'label' => $labels[$value] ?? $const,
            ];
        }

        return $result;
    }

    /**
     * Convert enum to JSON object string.
     *
     * @return string
     *
     * @throws \JsonException
     */
    public static function toJson(): string
    {
        return \json_encode(static::toArray(), JSON_THROW_ON_ERROR);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return string data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     *
     * @since 5.4.0
     */
    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}
