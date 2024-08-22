<?php

declare(strict_types=1);

namespace App\Core\Values\Collections;

use App\Core\Parents\Models\Model;
use App\Core\Parents\Queries\QueryBuilder;
use App\Core\Values\RelationSumDefinition;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

final class ModelCollection extends Collection
{
    /**
     * Load a set of relationship counts onto the collection.
     *
     * @param \App\Core\Values\RelationSumDefinition|array<array-key, \App\Core\Values\RelationSumDefinition> $relations
     *
     * @return $this
     */
    public function loadSumFromDefinitions(array|RelationSumDefinition $relations): self
    {
        if ($this->isEmpty()) {
            return $this;
        }

        /** @var \App\Core\Parents\Queries\QueryBuilder $queryBuilder */
        $queryBuilder = $this->getModelQuery()
            ->wherePrimaryKey($this->modelKeys())
            ->select($this->getFirstModel()->getKeyName());

        $models = self::make(
            $queryBuilder->withSumFromDefinitions(...\func_get_args())->getAll()
        );

        /** @var self $models because of phpstan */

        $attributes = \array_keys(
            Arr::except($models->getFirstModel()->getAttributes(), $models->getFirstModel()->getKeyName())
        );

        $models->each(function (Model $model) use ($attributes): void {
            /** @var null|\App\Core\Parents\Models\Model $originalModel */
            $originalModel = $this->find($model->getKey());

            if (! $originalModel) {
                return;
            }

            foreach (Arr::only($model->getAttributes(), $attributes) as $attribute => $value) {
                $originalModel->setAttribute($attribute, $value ?? 0);
            }

            $originalModel->syncOriginalAttributes($attributes);
        });

        return $this;
    }

    /**
     * @param int $size
     * @param callable $callback
     *
     * @return $this
     */
    public function processChunks(int $size, callable $callback): self
    {
        $this->chunk($size)->each($callback);
        return $this;
    }

    /**
     * @return \App\Core\Parents\Queries\QueryBuilder
     */
    private function getModelQuery(): QueryBuilder
    {
        return $this->getFirstModel()->newModelQuery();
    }

    /**
     * @return \App\Core\Parents\Models\Model
     */
    private function getFirstModel(): Model
    {
        /** @var null|\App\Core\Parents\Models\Model $model */
        $model = $this->first();

        if (! $model) {
            throw new \RuntimeException('No model is defined on ModelCollection');
        }

        return $model;
    }
}
