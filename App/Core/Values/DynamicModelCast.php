<?php

declare(strict_types=1);

namespace App\Core\Values;

use App\Core\Parents\Enums\InstantiableEnum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

final class DynamicModelCast implements CastsAttributes
{
    /**
     * @var mixed[]
     */
    private readonly array $params;

    /**
     * @param class-string $class
     * @param mixed ...$params
     */
    public function __construct(
        private readonly string $class,
        ...$params
    ) {
        $this->params = $params;
    }

    /**
     * @param mixed[] $config
     *
     * @return self
     */
    public static function fromArray(array $config): self
    {
        $class = \array_shift($config);
        return new self($class, ...$config);
    }

    public function cast(mixed $value): mixed
    {
        $value = $value === '' ? null : $value;

        if ($value === null) {
            try {
                return $this->createInstance($value);
            } catch (\Throwable) {
                return null;
            }
        }

        return $this->createInstance($value);
    }

    /**
     * @return class-string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return mixed[]
     */
    public function getParams(): array
    {
        return $this->params ?? [];
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array<string,mixed> $attributes
     *
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes): mixed
    {
        return $this->cast($value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array<string,mixed> $attributes
     *
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes): mixed
    {
        $instance = $this->cast($value);

        if ($instance === null) {
            return null;
        }

        if ($instance instanceof InstantiableEnum) {
            return $instance->getValue();
        }

        if ($instance instanceof \BackedEnum) {
            return $instance->value;
        }

        return (string) $instance;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function createInstance(mixed $value): mixed
    {
        if ($this->params) {
            return new $this->class($value, ...$this->params);
        }

        if (\is_subclass_of($this->class, \BackedEnum::class)) {
            if ($value instanceof \BackedEnum) {
                return $value;
            }

            return $this->class::from($value);
        }

        return new $this->class($value);
    }
}
