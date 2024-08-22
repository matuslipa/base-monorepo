<?php

declare(strict_types=1);

namespace App\Core\Values;

use Illuminate\Container\Container;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Grammars\Grammar;

final class RelationSumDefinition
{
    /**
     * @var null|\Illuminate\Database\Query\Grammars\Grammar
     */
    private ?Grammar $grammar = null;

    /**
     * @param string $relationName
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param string $alias
     * @param null|\Closure $constraints
     */
    public function __construct(
        private readonly string $relationName,
        private readonly string | Expression $column,
        private readonly string $alias,
        private readonly ?\Closure $constraints = null
    ) {
    }

    /**
     * @return string
     */
    public function getRelationName(): string
    {
        return $this->relationName;
    }

    /**
     * @return \Illuminate\Database\Query\Expression|string
     */
    public function getColumn(): string | Expression
    {
        return $this->column;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return null|\Closure
     */
    public function getConstraints(): ?\Closure
    {
        return $this->constraints;
    }

    /**
     * @return bool
     */
    public function hasConstraints(): bool
    {
        return $this->constraints !== null;
    }

    /**
     * Add the constraints for a relationship count query.
     *
     * @param \Illuminate\Database\Eloquent\Relations\Relation $relation
     * @param \Illuminate\Database\Eloquent\Builder $parentQuery
     * @param null|string $prefix
     *
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getRelationExistenceCountQuery(
        Relation $relation,
        Builder $parentQuery,
        ?string $prefix = null
    ): Builder {
        $column = $this->getColumn() instanceof Expression
            ? $this->getColumn()->getValue($this->getGrammar())
            : $this->getColumn();

        if ($prefix !== null) {
            $column = $prefix . '.' . $column;
        }

        /** @phpstan-ignore-next-line */
        return $relation
            ->getRelationExistenceQuery(
                $relation->getRelated()->newQuery(),
                $parentQuery,
                new Expression("COALESCE(SUM({$column}), 0)"),
            )
            ->setBindings([], 'select');
    }

    /**
     * @return \Illuminate\Database\Query\Grammars\Grammar
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function getGrammar(): Grammar
    {
        if ($this->grammar !== null) {
            return $this->grammar;
        }

        /** @var \Illuminate\Database\DatabaseManager $databaseManager */
        $databaseManager = Container::getInstance()->make(DatabaseManager::class);
        return $this->grammar = $databaseManager->getQueryGrammar();
    }
}
