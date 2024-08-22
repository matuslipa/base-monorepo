<?php

declare(strict_types=1);

namespace App\Core\Casts;

use App\Core\Parents\Enums\InstantiableEnum;
use App\Core\Parents\Models\Model;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Stringable;

final class InstantiableEnumCast implements CastsAttributes
{
    /**
     * InstantiableEnumCast constructor.
     *
     * @param mixed[] $params
     */
    public function __construct(
        private readonly array $params = [
        ]
    ) {
    }

    /**
     * @inheritDoc
     *
     * @param \App\Core\Parents\Models\Model $model
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        $class = $this->getClassName($model, $key);
        return new $class($value, ...$this->params);
    }

    /**
     * @inheritDoc
     *
     * @param \App\Core\Parents\Models\Model $model
     * @param mixed $value
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if ($value !== null && ! $value instanceof InstantiableEnum) {
            $class = $this->getClassName($model, $key);

            /** @var \App\Core\Parents\Enums\InstantiableEnum $value */
            $value = new $class($value, ...$this->params);
        }

        return [
            $key => $value?->getValue(),
        ];
    }

    /**
     * @param \App\Core\Parents\Models\Model $model
     * @param string $key
     *
     * @return string
     */
    private function getClassName(Model $model, string $key): string
    {
        return (string) (new Stringable($model->getCasts()[$key]))->before(':');
    }
}
