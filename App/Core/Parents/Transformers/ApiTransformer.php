<?php

declare(strict_types=1);

namespace App\Core\Parents\Transformers;

use App\Core\Helpers\ApiDataTypesTransformer;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

abstract class ApiTransformer
{
    final public const string DATE_FORMAT = 'Y-m-d';

    final public const string DATETIME_FORMAT = 'Y-m-d\TH:i:sO';

    /**
     * @var array<string,mixed>
     */
    private array $state = [];

    /**
     * @param mixed $item
     *
     * @return mixed[]
     */
    public function runTransformation(mixed $item): array
    {
        if (! \method_exists($this, 'transform')) {
            throw new \RuntimeException(
                \sprintf(
                    'Transformer "%s" does not contain "%s" method!',
                    static::class,
                    'transform'
                )
            );
        }

        if ($item instanceof Collection) {
            return $this->transformFinalCollection($item, $this->transform(...));
        }

        return $this->transform($item);
    }

    /**
     * Transform final collection.
     *
     * @param \Illuminate\Support\Collection $data
     * @param callable $transform
     *
     * @return mixed[]
     */
    public function transformFinalCollection(Collection $data, callable $transform): array
    {
        $dataArray = [];

        foreach ($data as $item) {
            $dataArray[] = $transform($item);
        }

        return $dataArray;
    }

    /**
     * Format date.
     *
     * @param null|\Carbon\CarbonImmutable $date
     *
     * @return null|string
     */
    protected function formatDate(?CarbonImmutable $date): ?string
    {
        return ApiDataTypesTransformer::transformDate($date, self::DATE_FORMAT);
    }

    /**
     * Format date.
     *
     * @param null|\Carbon\CarbonImmutable $datetime
     *
     * @return null|string
     */
    protected function formatDateTime(?CarbonImmutable $datetime): ?string
    {
        return $datetime?->format(self::DATETIME_FORMAT);
    }

    /**
     * @param string $key
     *
     * @return null|mixed
     */
    protected function getState(string $key): mixed
    {
        return $this->state[$key] ?? null;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    protected function setState(string $key, mixed $value): void
    {
        $this->state[$key] = $value;
    }
}
