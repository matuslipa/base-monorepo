<?php

declare(strict_types=1);

namespace App\Core\Contracts;

use App\Core\Parents\Models\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

/**
 * @future-template TModel of \App\Core\Parents\Models\Model|\App\Core\Parents\Models\Pivot
 */
interface QueryBuilderInterface extends QueryInterface
{
    /**
     * @param int|string $id
     *
     * @return \App\Core\Parents\Models\Model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int | string $id): Model;

    /**
     * @param int[]|string[] $ids
     *
     * @return \Illuminate\Support\Collection
     */
    public function findByIds(array $ids): Collection;

    /**
     * @param int[]|string[] $ids
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByIds(array $ids): Collection;

    /**
     * @return null|\App\Core\Parents\Models\Model
     */
    public function getFirst(): ?ModelInterface;

    /**
     * @return \App\Core\Parents\Models\Model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getFirstOrFail(): Model;

    /**
     * @param int|string $id
     *
     * @return null|\App\Core\Parents\Models\Model
     */
    public function findById(int | string $id): ?Model;

    /**
     * Determine if any rows exist for the current query.
     *
     * @return bool
     */
    public function someExists(): bool;

    /**
     * Determine if any rows don't exist for the current query.
     *
     * @return bool
     */
    public function anyDoesntExist(): bool;

    /**
     * Count distinct rows.
     *
     * @return int
     */
    public function countDistinct(): int;

    /**
     * Count rows.
     *
     * @return int
     */
    public function countRows(): int;

    /**
     * Add a where clause on the primary key to the query.
     *
     * @param int|int[]|string|string[] $id
     *
     * @return $this
     */
    public function wherePrimaryKey(array | int | string | Collection $id): self;

    /**
     * Add a where clause on the bigger primary key to the query.
     *
     * @param int $id
     *
     * @return $this
     */
    public function wherePrimaryKeyGt(int $id): self;

    /**
     * Add a where clause on the bigger or equivalent primary key to the query.
     *
     * @param int $id
     *
     * @return $this
     */
    public function wherePrimaryKeyGte(int $id): self;

    /**
     * Add a where clause on the smaller primary key to the query.
     *
     * @param int $id
     *
     * @return $this
     */
    public function wherePrimaryKeyLt(int $id): self;

    /**
     * Add a where clause on the smaller or equivalent primary key to the query.
     *
     * @param int $id
     *
     * @return $this
     */
    public function wherePrimaryKeyLte(int $id): self;

    /**
     * Add a where clause on the primary key to the query.
     *
     * @param int|int[]|string|string[] $id
     *
     * @return $this
     */
    public function wherePrimaryKeyNot(array | int | string | Collection $id): self;

    /**
     * Process results by chunks of specified size.
     *
     * @param int $size
     * @param callable $callback
     *
     * @return bool
     */
    public function processChunks(int $size, callable $callback): bool;

    /**
     * Pluck keys.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluckKeys(): Collection;

    /**
     * Set limit number of rows to fetch.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function setLimit(int $limit): self;

    /**
     * Set offset number of rows to fetch.
     *
     * @param int $offset
     *
     * @return $this
     */
    public function setOffset(int $offset): self;

    /**
     * Get only specified column data.
     *
     * @param string $column
     *
     * @return \Illuminate\Support\Collection
     */
    public function getOnly(string $column): Collection;

    /**
     * Get only primary keys.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getOnlyKeys(): Collection;

    /**
     * Ensure that query result will return no results.
     *
     * @return \App\Core\Contracts\QueryBuilderInterface
     */
    public function ensureNoResults(): self;

    /**
     * Get instance of relative model.
     *
     * @return \App\Core\Parents\Models\Model
     */
    public function getModelInstance(): Model;

    /**
     * Add a "group by" clause to the query.
     *
     * @param array<\Illuminate\Database\Query\Expression|string>|\Illuminate\Database\Query\Expression|string ...$groups
     *
     * @return $this
     */
    public function groupBy(...$groups): self;

    /**
     * Get new query instance from current query.
     *
     * @return static
     */
    public function newQueryClone(): self;

    /**
     * Get a base query builder instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function toBaseBuilder(): Builder;

    /**
     * @return static
     */
    public function makeClone(): static;
}
