<?php

declare(strict_types=1);

namespace App\Core\Parents\Queries;

use App\Core\Contracts\HasMonetaryAttributesInterface;
use App\Core\Contracts\HasMultilingualAttributesInterface;
use App\Core\Contracts\ModelInterface;
use App\Core\Contracts\QueryBuilderInterface;
use App\Core\Helpers\QueryUtils;
use App\Core\Models\AmountsModel;
use App\Core\Models\TranslationModel;
use App\Core\Parents\Models\Model;
use App\Core\Services\DataHandler\Contracts\FilterMutatorWithCustomHandlingInterface;
use App\Core\Services\DataHandler\Filtering\FilterOperatorEnum;
use App\Core\Services\DataHandler\Filtering\FilterRule;
use App\Core\Values\Enums\CastTypesEnum;
use App\Core\Values\Enums\SortDirectionEnum;
use App\Core\Values\Enums\SortNullBehaviourEnum;
use App\Core\Values\RelationSumDefinition;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as FrameworkQueryBuilder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @future-template TModel of \App\Core\Parents\Models\Model|\App\Core\Parents\Models\Pivot
 *
 * @future-implements \App\Core\Contracts\QueryBuilderInterface<TModel>
 */
class QueryBuilder extends Builder implements QueryBuilderInterface
{
    /**
     * @var string[]
     */
    private array $uniqueJoins = [];

    /**
     * @var int
     */
    private static int $joinCounter = 0;

    /**
     * @var bool
     */
    private bool $allSelected = false;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    final public function __construct(\Illuminate\Database\Eloquent\Model $model)
    {
        $connection = $model->getConnection();

        $query = new FrameworkQueryBuilder(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor()
        );

        parent::__construct($query);

        $model->registerGlobalScopes($this);
        $this->setModel($model);
    }

    /**
     * @inheritDoc
     */
    public function makeClone(): static
    {
        return clone $this;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): Collection
    {
        return $this->get()->toBase();
    }

    /**
     * @inheritDoc
     */
    public function getById(int | string $id): Model
    {
        /** @var \App\Core\Parents\Models\Model $model */
        $model = $this->findOrFail($id);
        return $model;
    }

    /**
     * @inheritDoc
     */
    public function findByIds(array $ids): Collection
    {
        return $this->findMany($ids);
    }

    /**
     * @inheritDoc
     */
    public function getByIds(array $ids): Collection
    {
        /** @var \Illuminate\Support\Collection $collection */
        $collection = $this->findOrFail($ids);
        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function getFirst(): ?ModelInterface
    {
        /** @var null|\App\Core\Parents\Models\Model $model */
        $model = $this->first();
        return $model;
    }

    /**
     * @inheritDoc
     */
    public function getFirstOrFail(): Model
    {
        /** @var \App\Core\Parents\Models\Model $model */
        $model = $this->firstOrFail();
        return $model;
    }

    /**
     * @inheritDoc
     */
    public function findById(int | string $id): ?Model
    {
        /** @var null|\App\Core\Parents\Models\Model $model */
        $model = $this->find($id);
        return $model;
    }

    /**
     * @inheritDoc
     */
    public function someExists(): bool
    {
        return $this->exists();
    }

    /**
     * @inheritDoc
     */
    public function anyDoesntExist(): bool
    {
        return $this->doesntExist();
    }

    /**
     * @return int
     */
    public function countDistinct(): int
    {
        return $this->distinct()->count();
    }

    /**
     * @return int
     */
    public function countRows(): int
    {
        return $this->count();
    }

    /**
     * @inheritDoc
     */
    public function wherePrimaryKey(array | int | string | Collection $id): QueryBuilderInterface
    {
        /** @var \App\Core\Parents\Models\Model $model */
        $model = $this->getModel();

        if ($model->getKeyType() === CastTypesEnum::INT) {
            $keyColumn = $this->prefixColumn($model->getKeyName());
            \is_array($id)
                ? $this->whereIntegerInRaw($keyColumn, $id)
                : $this->where($keyColumn, $id);

            return $this;
        }

        return $this->whereKey($id);
    }

    /**
     * Add a where clause on the bigger primary key to the query.
     *
     * @param int $id
     *
     * @return $this
     */
    public function wherePrimaryKeyGt(int $id): QueryBuilderInterface
    {
        return $this->where($this->model->getQualifiedKeyName(), '>', $id);
    }

    /**
     * Add a where clause on the bigger or equivalent primary key to the query.
     *
     * @param int $id
     *
     * @return $this
     */
    public function wherePrimaryKeyGte(int $id): QueryBuilderInterface
    {
        return $this->where($this->model->getQualifiedKeyName(), '>=', $id);
    }

    /**
     * Add a where clause on the smaller primary key to the query.
     *
     * @param int $id
     *
     * @return $this
     */
    public function wherePrimaryKeyLt(int $id): QueryBuilderInterface
    {
        return $this->where($this->model->getQualifiedKeyName(), '<', $id);
    }

    /**
     * Add a where clause on the smaller or equivalent primary key to the query.
     *
     * @param int $id
     *
     * @return $this
     */
    public function wherePrimaryKeyLte(int $id): QueryBuilderInterface
    {
        return $this->where($this->model->getQualifiedKeyName(), '<=', $id);
    }

    /**
     * @inheritDoc
     */
    public function wherePrimaryKeyNot(array | int | string | Collection $id): QueryBuilderInterface
    {
        return $this->whereKeyNot($id);
    }

    /**
     * @inheritDoc
     */
    public function processChunks(int $size, callable $callback): bool
    {
        return $this->chunk($size, $callback);
    }

    /**
     * @inheritDoc
     */
    public function pluckKeys(): Collection
    {
        return $this->pluck($this->model->getQualifiedKeyName());
    }

    /**
     * @inheritDoc
     */
    final public function applyFilterRule(
        FilterRule $filterRule,
        Closure | string | Expression $column = null,
        ?string $languageId = null,
        ?string $currencyId = null
    ): void {
        $mutator = $filterRule->getMutator();

        if ($mutator instanceof FilterMutatorWithCustomHandlingInterface) {
            $mutator->applyQueryFilters($this, $filterRule);
            return;
        }

        $column ??= $this->resolveColumn($filterRule);
        $model = $this->getModel();

        $applyCallback = static function (Builder $query, FilterRule $filterRule, $column): void {
            switch ($filterRule->getOperator()->getValue()) {
                case FilterOperatorEnum::STARTS_WITH:
                    $query->where($column, 'LIKE', $filterRule->getValue() . '%');
                    break;
                case FilterOperatorEnum::ENDS_WITH:
                    $query->where($column, 'LIKE', '%' . $filterRule->getValue());
                    break;
                case FilterOperatorEnum::CONTAINS:
                    $query->where($column, 'LIKE', '%' . $filterRule->getValue() . '%');
                    break;
                case FilterOperatorEnum::EQUAL:
                    $query->where($column, '=', $filterRule->getValue());
                    break;
                case FilterOperatorEnum::NOT_EQUAL:
                    $query->where(static function (self $query) use ($column, $filterRule): void {
                        $value = $filterRule->getValue();

                        $query->where($column, '!=', $filterRule->getValue());

                        if ($value !== null && ! $filterRule->getMutator()->isValueRequired()) {
                            $query->orWhereNull($column);
                        }
                    });
                    break;
                case FilterOperatorEnum::GREATER:
                    $query->where($column, '>', $filterRule->getValue());
                    break;
                case FilterOperatorEnum::GREATER_OR_EQUAL:
                    $query->where($column, '>=', $filterRule->getValue());
                    break;
                case FilterOperatorEnum::LESS:
                    $query->where($column, '<', $filterRule->getValue());
                    break;
                case FilterOperatorEnum::LESS_OR_EQUAL:
                    $query->where($column, '<=', $filterRule->getValue());
                    break;
                case FilterOperatorEnum::ONE_OF:
                    $query->whereIn($column, $filterRule->getValue());
                    break;
                case FilterOperatorEnum::NONE_OF:
                    $query->whereNotIn($column, $filterRule->getValue());
                    break;
            }
        };

        if (\is_string($column)) {
            if ($model instanceof HasMultilingualAttributesInterface && $model->isAttributeMultilingual($column)) {
                $this->whereHas(
                    HasMultilingualAttributesInterface::RELATION_TRANSLATIONS,
                    static function (Builder $builder) use ($applyCallback, $filterRule, $column, $languageId): void {
                        if ($languageId !== null) {
                            $builder->where(TranslationModel::ATTR_LANGUAGE_ID, $languageId);
                        }

                        $applyCallback($builder, $filterRule, $column);
                    }
                );
            } elseif ($model instanceof HasMonetaryAttributesInterface && $model->isMonetaryAttribute($column)) {
                $this->whereHas(
                    HasMonetaryAttributesInterface::RELATION_AMOUNTS,
                    static function (Builder $builder) use ($applyCallback, $filterRule, $column, $currencyId): void {
                        if ($currencyId !== null) {
                            $builder->where(AmountsModel::ATTR_CURRENCY_ID, $currencyId);
                        }

                        $applyCallback($builder, $filterRule, $column);
                    }
                );
            } else {
                $applyCallback($this, $filterRule, $column);
            }
        } else {
            $applyCallback($this, $filterRule, $column);
        }
    }

    /**
     * @inheritDoc
     */
    final public function applyFilterRulesOr(
        array $filterRules,
        ?string $languageId = null,
        ?string $currencyId = null
    ): void {
        $this->where(static function (self $query) use ($filterRules, $languageId, $currencyId): void {
            foreach ($filterRules as $filterRule) {
                $query->orWhere(static function (self $query) use ($filterRule, $languageId, $currencyId): void {
                    $query->applyFilterRule($filterRule, null, $languageId, $currencyId);
                });
            }
        });
    }

    /**
     * @inheritDoc
     */
    final public function applyPagination(int $offset, int $limit): self
    {
        $this->offset($offset)->limit($limit);
        return $this;
    }

    /**
     * @inheritDoc
     */
    final public function getPaginationCount(): int
    {
        return $this->count();
    }

    /**
     * @inheritDoc
     */
    final public function applySorting(
        string $column,
        SortDirectionEnum $direction,
        ?SortNullBehaviourEnum $nullBehaviour = null
    ): self {
        $nullBehaviour ??= SortNullBehaviourEnum::DEFAULT();

        if ($nullBehaviour->equals(SortNullBehaviourEnum::PRIORITIZE())) {
            $column = $this->quoteColumn($column);
            $this->orderByRaw("{$column} IS NOT NULL, {$column} {$direction->getValue()}");
        } elseif ($nullBehaviour->equals(SortNullBehaviourEnum::DEPRIORITIZE())) {
            $column = $this->quoteColumn($column);
            $nullComparer = $direction->equals(SortDirectionEnum::ASC()) ? 'IS' : 'IS NOT';
            $this->orderByRaw("{$column} {$nullComparer} NULL, {$column} {$direction->getValue()}");
        } else {
            $this->orderBy($column, $direction->getValue());
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function applyDeductiveSorting(
        string $column,
        SortDirectionEnum $direction,
        ?SortNullBehaviourEnum $nullBehaviour = null,
        ?string $languageId = null,
        ?string $currencyId = null
    ): self {
        if ($this->model instanceof HasMultilingualAttributesInterface && $this->model->isAttributeMultilingual($column)) {
            return $this->applyMultilingualSorting($column, $direction, $languageId, $nullBehaviour);
        }

        if ($this->model instanceof HasMonetaryAttributesInterface && $this->model->isMonetaryAttribute($column)) {
            return $this->applyMonetarySorting($column, $direction, $currencyId, $nullBehaviour);
        }

        return $this->applySorting($column, $direction, $nullBehaviour);
    }

    /**
     * @param string $column
     *
     * @return string
     */
    public function quoteColumn(string $column): string
    {
        if ($column !== '*') {
            return \implode('.', \array_map(
                static fn (string $part): string => '`' . \str_replace('`', '\`', $part) . '`',
                \explode('.', $column)
            ));
        }

        return $column;
    }

    /**
     * @inheritDoc
     */
    final public function applySortingByValues(string $column, SortDirectionEnum $direction, array $values): void
    {
        /** @phpstan-ignore-next-line */
        $this->orderByRaw(new Expression("FIELD(${column}, " . \implode(',', $values) . ') ' . $direction->getValue()));
    }

    /**
     * @inheritDoc
     */
    public function applyMultilingualSorting(
        string $column,
        SortDirectionEnum $direction,
        ?string $languageId = null,
        ?SortNullBehaviourEnum $nullBehaviour = null
    ): self {
        if ($this->model instanceof HasMultilingualAttributesInterface) {
            $translationTable = $this->model->getTranslationTable();
            $prefixedColumn = $this->prefixColumn($column, $translationTable);

            /** @var self $query */
            $query = (clone $this->newQueryBuilder())
                ->from($translationTable)
                ->select($prefixedColumn)
                ->whereColumn(
                    $this->prefixColumn($this->model->getTranslationForeignKey(), $translationTable),
                    $this->prefixColumn($this->model->getKeyName(), $this->model->getTable())
                )
                ->limit(1);

            if ($languageId !== null) {
                $query->where(
                    $this->prefixColumn(TranslationModel::ATTR_LANGUAGE_ID, $translationTable),
                    '=',
                    $languageId
                );
            }

            $this->orderBy(
                $query->applySorting($prefixedColumn, $direction, $nullBehaviour)->getQuery(),
                $direction->getValue()
            );
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function applyMonetarySorting(
        string $column,
        SortDirectionEnum $direction,
        ?string $currencyId = null,
        ?SortNullBehaviourEnum $nullBehaviour = null
    ): self {
        if ($this->model instanceof HasMonetaryAttributesInterface) {
            $amountsTable = $this->model->getAmountsTable();
            $prefixedColumn = $this->prefixColumn($column, $amountsTable);

            $query = (clone $this->newQueryBuilder())
                ->from($amountsTable)
                ->select($prefixedColumn)
                ->whereColumn(
                    $this->prefixColumn($this->model->getAmountsForeignKey(), $amountsTable),
                    $this->prefixColumn($this->model->getKeyName(), $this->model->getTable()),
                )
                ->limit(1);

            if ($currencyId !== null) {
                $query->where(
                    $this->prefixColumn(AmountsModel::ATTR_CURRENCY_ID, $amountsTable),
                    '=',
                    $currencyId
                );
            }

            /**
             * For phpstan
             *
             * @var self $query
             *
             * @noinspection PhpRedundantVariableDocTypeInspection
             */

            $this->orderBy(
                $query->applySorting($prefixedColumn, $direction, $nullBehaviour)->getQuery(),
                $direction->getValue()
            );
        }

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @param \Closure|\Illuminate\Database\Query\Expression|string|string[] $column
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and'): Builder | self | static
    {
        if ($column instanceof Closure) {
            /** @var \Illuminate\Database\Eloquent\Model $model */
            $model = $this->getModel();

            $query = new static($model);
            $query->withoutGlobalScopes();

            $column($query);

            $this->query->addNestedWhereQuery($query->getQuery(), $boolean);
        } else {
            $this->query->where(...\func_get_args());
        }

        return $this;
    }

    /**
     * Select only rows matching regular expression.
     *
     * @param string $column
     * @param string $regexp
     *
     * @return \App\Core\Parents\Queries\QueryBuilder
     */
    public function whereRegexpMatches(string $column, string $regexp): self
    {
        return $this->where($column, 'REGEXP', $regexp);
    }

    /**
     * @inheritDoc
     */
    public function setLimit(int $limit): QueryBuilderInterface
    {
        $this->limit($limit);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOffset(int $offset): QueryBuilderInterface
    {
        $this->offset($offset);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOnly(string $column): Collection
    {
        return $this->pluck($column);
    }

    /**
     * @inheritDoc
     */
    public function getOnlyKeys(): Collection
    {
        /** @var \App\Core\Parents\Models\Model $model */
        $model = $this->getModel();

        return $this->pluck($model->getQualifiedKeyName());
    }

    /**
     * @inheritDoc
     */
    public function ensureNoResults(): self
    {
        $this->whereRaw('0=1');
        return $this;
    }

    /**
     * @param string $relation
     * @param null|\Closure $constrains
     *
     * @return $this
     */
    public function withPossiblyConstrained(string $relation, ?Closure $constrains): self
    {
        if ($constrains !== null) {
            return $this->with([
                $relation => static function ($relation) use ($constrains): void {
                    $constrains($relation instanceof Relation ? $relation->getQuery() : $relation);
                },
            ]);
        }

        return $this->with($relation);
    }

    /**
     * @param string $relation
     * @param null|\Closure $constrains
     * @param string $operator
     * @param int $count
     *
     * @return $this
     */
    public function whereHasPossiblyConstrained(
        string $relation,
        ?Closure $constrains,
        string $operator = '>=',
        int $count = 1
    ): self {
        if ($constrains !== null) {
            $this->whereHas(
                $relation,
                static function ($relation) use ($constrains): void {
                    $constrains($relation instanceof Relation ? $relation->getQuery() : $relation);
                },
                $operator,
                $count
            );
        } else {
            $this->whereHas($relation, null, $operator, $count);
        }

        return $this;
    }

    /**
     * Add subselect queries to sum on the relations.
     *
     * @param \App\Core\Values\RelationSumDefinition|\App\Core\Values\RelationSumDefinition[] $relations
     *
     * @return $this
     */
    public function withSumFromDefinitions(RelationSumDefinition | array $relations): self
    {
        if (empty($relations)) {
            return $this;
        }

        if ($this->query->columns === null) {
            $this->query->select([$this->query->from . '.*']);
        }

        $relations = \is_array($relations) ? $relations : \func_get_args();

        foreach ($relations as $relationSum) {
            if (! $relationSum instanceof RelationSumDefinition) {
                continue;
            }

            $relation = $this->getRelationWithoutConstraints($relationSum->getRelationName());

            // Here we will get the relationship count query and prepare to add it to the main query
            // as a sub-select. First, we'll get the "has" query and use that to get the relation
            // count query. We will normalize the relation name then append _count as the name.
            $query = $relationSum->getRelationExistenceCountQuery($relation, $this, $relation->getModel()->getTable());

            if ($constraints = $relationSum->getConstraints()) {
                $query->callScope($constraints);
            }

            $query = $query->mergeConstraintsFrom($relation->getQuery())->toBase();

            if (\count((array) $query->columns) > 1) {
                $query->columns = \array_filter($query->columns, static fn ($column): bool => ! \is_string($column) || ! Str::endsWith($column, '.*'));
                $query->bindings['select'] = [];
            }

            $this->selectSub($query, $relationSum->getAlias());
        }

        return $this;
    }

    /**
     * Add a "group by" clause to the query.
     *
     * @param array<\Illuminate\Database\Query\Expression|string>|\Illuminate\Database\Query\Expression|string ...$groups
     *
     * @return $this
     */
    public function groupBy(...$groups): self
    {
        $this->getQuery()->groupBy($groups);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function newQueryClone(): self
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $this->getModel();

        return new static($model);
    }

    /**
     * @inheritDoc
     */
    public function toBaseBuilder(): FrameworkQueryBuilder
    {
        return $this->toBase();
    }

    /**
     * @inheritDoc
     */
    public function getModelInstance(): Model
    {
        /** @var \App\Core\Parents\Models\Model $model */
        $model = $this->getModel();
        return $model;
    }

    /**
     * Get a new instance of the query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function newQuery(): FrameworkQueryBuilder
    {
        return new FrameworkQueryBuilder(
            $this->query->connection,
            $this->query->grammar,
            $this->query->processor
        );
    }

    /**
     * Get a new instance of the current query.
     *
     * @return self
     */
    public function newQueryBuilder(): self
    {
        return new self($this->model);
    }

    /**
     * If join is already used, returns true.
     *
     * @param string $name
     * @param string $joinType
     *
     * @return bool
     */
    protected function checkAndRegisterUniqueJoin(string $name, string $joinType = 'inner'): bool
    {
        $joinId = $joinType . '-' . $name;

        if (isset($this->uniqueJoins[$joinId])) {
            return true;
        }

        $this->uniqueJoins[$joinId] = $this->getUniqueJoinAlias($name, $joinType);
        return false;
    }

    /**
     * Join relation table and make sure it is unique join.
     *
     * @param string $relation
     * @param string $joinType - inner/left/right
     * @param null|\Closure $clause
     *
     * @return string - joined table alias
     */
    protected function uniqueJoinRelation(
        string $relation,
        string $joinType = 'inner',
        ?Closure $clause = null
    ): string {
        /** @var \App\Core\Parents\Models\Model $model */
        $model = $this->getModel();
        $table = $model->getRelationInstance($relation)->getRelated()->getTable();

        if ($this->checkAndRegisterUniqueJoin($table)) {
            return $this->getUniqueJoinAlias($table);
        }

        return $this->joinRelation($relation, $joinType, $clause);
    }

    /**
     * Join relation table.
     *
     * @param string $relation
     * @param string $joinType - inner/left/right
     * @param null|\Closure $clause
     *
     * @return string - joined table alias
     */
    protected function joinRelation(
        string $relation,
        string $joinType = 'inner',
        ?Closure $clause = null
    ): string {
        /** @var \App\Core\Parents\Models\Model $model */
        $model = $this->getModel();
        $relationInstance = $model->getRelationInstance($relation);

        $table = $relationInstance->getRelated()->getTable();

        $alias = $this->getUniqueJoinAlias($table);

        $aliasedTable = $table . ' as ' . $alias;

        $localColumn = null;
        $foreignColumn = null;
        if ($relationInstance instanceof HasOne || $relationInstance instanceof HasMany) {
            $localColumn = $relationInstance->getQualifiedParentKeyName();
            $foreignColumn = $this->prefixColumn($relationInstance->getForeignKeyName(), $alias);
        } elseif ($relationInstance instanceof BelongsTo) {
            $localColumn = $relationInstance->getQualifiedForeignKeyName();
            $foreignColumn = $this->prefixColumn($relationInstance->getOwnerKeyName(), $alias);
        }

        $this->selectAllColumns()
            ->join(
                $aliasedTable,
                static function (
                    JoinClause $join
                ) use ($foreignColumn, $localColumn, $alias, $clause, $table): void {
                    if ($localColumn && $foreignColumn) {
                        $join->on($localColumn, '=', $foreignColumn);

                        if ($clause) {
                            $clause($join, $table, $alias);
                        }
                    }
                },
                $joinType
            );

        return $alias;
    }

    /**
     * If join is already used, returns true.
     *
     * @param string $name
     * @param string $joinType
     *
     * @return string
     */
    protected function getUniqueJoinAlias(string $name, string $joinType = 'inner'): string
    {
        $joinId = $joinType . '-' . $name;
        ++self::$joinCounter;
        return $this->uniqueJoins[$joinId] ?? $name . '_join' . self::$joinCounter;
    }

    /**
     * Select all columns and make sure this select is unique.
     *
     * @return $this
     */
    protected function selectAllColumns(): self
    {
        if ($this->allSelected) {
            return $this;
        }

        $this->allSelected = true;
        $this->addSelect($this->prefixColumn('*'));
        return $this;
    }

    /**
     * Prefix column.
     *
     * @param string $column
     * @param null|string $table
     *
     * @return string
     */
    protected function prefixColumn(string $column, ?string $table = null): string
    {
        return QueryUtils::prefixColumn($column, $table ?? $this->model->getTable());
    }

    /**
     * @inheritDoc
     *
     * @param class-string[] $models
     *
     * @return \App\Core\Parents\Models\Model[]
     *
     * @throws \ReflectionException
     */
    protected function eagerLoadRelation(array $models, $name, Closure $constraints): array
    {
        // First we will "back up" the existing where conditions on the query so we can
        // add our eager constraints. Then we will merge the wheres that were on the
        // query back to it in order that any where conditions might be specified.
        $relation = $this->getRelation($name);

        $relation->addEagerConstraints($models);

        $reflection = new \ReflectionFunction($constraints);
        $reflectionParameter = $reflection->getParameters()[0] ?? null;

        if ($reflectionParameter
            && $reflectionParameter->getType() instanceof \ReflectionNamedType
            && \is_subclass_of($reflectionParameter->getType()->getName(), QueryBuilderInterface::class)
        ) {
            $constraints($relation->getQuery());
        } else {
            $constraints($relation);
        }

        // Once we have the results, we just match those back up to their parent models
        // using the relationship instance. Then we just return the finished arrays
        // of models which have been eagerly hydrated and are readied for return.
        return $relation->match(
            $relation->initRelation($models, $name),
            $relation->getEager(),
            $name
        );
    }

    /**
     * Join same table.
     *
     * @param \Closure $joinClause
     *
     * @return string - table alias
     */
    protected function leftJoinSelf(Closure $joinClause): string
    {
        /** @var \App\Core\Parents\Models\Model $model */
        $model = $this->getModel();

        $table = $model->getTable();

        if ($this->checkAndRegisterUniqueJoin($table, 'left')) {
            return $this->getUniqueJoinAlias($table, 'left');
        }

        $alias = $this->getUniqueJoinAlias($table, 'left');

        $aliasedTable = $table . ' as ' . $alias;

        $this->selectAllColumns()
            ->leftJoin($aliasedTable, static function (JoinClause $clause) use ($alias, $joinClause, $table): void {
                $joinClause($clause, $table, $alias);
            });

        return $alias;
    }

    /**
     * @param string $table
     * @param null|string $as
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function makeSubForTable(string $table, ?string $as = null): FrameworkQueryBuilder
    {
        return $this->newQuery()->from($table, $as);
    }

    /**
     * @param \App\Core\Services\DataHandler\Filtering\FilterRule $filterRule
     *
     * @return string
     */
    private function resolveColumn(FilterRule $filterRule): string
    {
        $column = $filterRule->getAttribute();

        if ($this->model instanceof HasMultilingualAttributesInterface && $this->model->isAttributeMultilingual($column)) {
            return $column;
        }

        if ($this->model instanceof HasMonetaryAttributesInterface && $this->model->isMonetaryAttribute($column)) {
            return $column;
        }

        if (! Str::contains($column, '.')) {
            $column = $this->prefixColumn($column);
        }

        return $column;
    }
}
