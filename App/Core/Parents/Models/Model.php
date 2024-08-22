<?php

declare(strict_types=1);

namespace App\Core\Parents\Models;

use App\Core\Contracts\ModelInterface;
use App\Core\Contracts\QueryBuilderInterface;
use App\Core\Helpers\Utils;
use App\Core\Parents\Queries\QueryBuilder;
use App\Core\Values\Collections\ModelCollection;
use App\Core\Values\Diff\Diff;
use App\Core\Values\Diff\ModelAttributesDiff;
use App\Core\Values\DynamicModelCast;
use App\Core\Values\Enums\CastTypesEnum;
use App\Core\Values\Enums\ModelEventEnum;
use App\Core\Values\RelationSumDefinition;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

abstract class Model extends \Illuminate\Database\Eloquent\Model implements ModelInterface
{
    /**
     * Mutators for setting the attribute.
     *
     * @var array<class-string,callable[]>
     */
    protected static array $setAttributeMutators = [];

    /**
     * Mutators for getting the attribute.
     *
     * @var array<class-string,callable[]>
     */
    protected static array $getAttributeMutators = [];

    /**
     * Resolvers for attributes.
     *
     * @var array<class-string,callable[]>
     */
    protected static array $attributesDiffResolvers = [];

    /**
     * is model in process of saving?
     *
     * @var bool
     */
    protected bool $isBeingSaved = false;

    /**
     * @var null|array<string,mixed>
     */
    protected ?array $previous = null;

    /**
     * Was model recently updated?
     *
     * @var bool
     */
    private bool $wasRecentlyUpdated = false;

    /**
     * @return string
     */
    public function getTable(): string
    {
        // using custom automatic table names in singular...
        return $this->table ?: Str::snake(Str::studly(class_basename($this)));
    }

    /**
     * Get the connection and table name for the model.
     *
     * @return string
     */
    public function getTableConnection(): string
    {
        return $this->getConnectionName() . '.' . $this->getTable();
    }

    /**
     * @inheritDoc
     */
    public function getKey(): mixed
    {
        /** @var string|string[] $keyName */
        $keyName = $this->getKeyName();

        if (! \is_array($keyName)) {
            return $this->getAttributeValue($keyName);
        }

        // adding support for composed (multi-column) keys...
        $attributes = [];
        foreach ($keyName as $key) {
            $attributes[$key] = $this->getAttributeValue($key);
        }

        return $attributes;
    }

    /**
     * @param mixed[] $data
     *
     * @return static
     */
    public static function makeCompactlyFilled(array $data): static
    {
        $instance = self::makeBlank();

        if (\method_exists($instance, 'compactFill')) {
            $instance->compactFill($data);
        } else {
            $instance->fill($data);
        }

        return $instance;
    }

    /**
     * Get the current connection name and table for the model.
     *
     * @return string
     */
    public static function tableConnection(): string
    {
        return self::makeBlank()->getTableConnection();
    }

    /**
     * Get the current connection name and table for the model.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return self::makeBlank()->getTable();
    }

    /**
     * Determine whether an attribute should be cast to a native type.
     *
     * @param string $key
     * @param null|mixed[]|string $types
     *
     * @return bool
     */
    public function hasCast($key, $types = null): bool
    {
        $casts = $this->getCasts();
        if (isset($casts[$key])) {
            return empty($types) || \in_array(
                \is_array($casts[$key]) ? $casts[$key][0] : $casts[$key],
                (array) $types,
                true
            );
        }

        if ($this->getIncrementing() && $key === $this->getKeyName()) {
            return empty($types) || \in_array(CastTypesEnum::INT, (array) $types, true);
        }

        return false;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getCastedOriginal(string $key): mixed
    {
        if (! $key) {
            return null;
        }

        if (\array_key_exists($key, $this->original) && $this->hasCast($key)) {
            return $this->castAttribute($key, $this->original[$key]);
        }

        return $this->original[$key] ?? null;
    }

    /**
     * @param int|int[]|string|string[] $key
     * @param array<string,mixed> $attributes
     *
     * @return static
     */
    public static function makeExistingBlank(int|string|array $key, array $attributes = []): static
    {
        $model = self::makeBlank();

        $model->forceFill($attributes);
        $model->setAttribute($model->getKeyName(), $key);

        $model->exists = true;

        return $model;
    }

    /**
     * @return static
     */
    public static function makeBlank(): static
    {
        /** @phpstan-ignore-next-line */
        return new static();
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setAttribute($key, $value): self
    {
        // we shall call our custom attribute mutator hooks here.
        foreach (static::$setAttributeMutators[static::class] ?? [] as $mutator) {
            $interrupt = false;
            $value = $mutator($this, $key, $value, $interrupt);

            /** @var false|true $interrupt can be modified by mutator using & ref */
            if ($interrupt) {
                return $this;
            }
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Get an attribute from the model.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttributeValue($key): mixed
    {
        $value = parent::getAttributeValue($key);

        // we can call our custom attribute mutator hooks here.
        foreach (static::$getAttributeMutators[static::class] ?? [] as $mutator) {
            $value = $mutator($this, $key, $value);
        }

        return $value;
    }

    /**
     * Register attribute mutator for setter.
     *
     * @param callable $callback
     */
    public static function registerSetAttributeMutator(callable $callback): void
    {
        static::$setAttributeMutators[static::class][] = $callback;
    }

    /**
     * Register attribute mutator for getter.
     *
     * @param callable $callback
     */
    public static function registerGetAttributeValueMutator(callable $callback): void
    {
        static::$getAttributeMutators[static::class][] = $callback;
    }

    /**
     * Register attributes diff resolver.
     *
     * @param callable $callback
     */
    public static function registerDiffResolver(callable $callback): void
    {
        static::$attributesDiffResolvers[static::class][] = $callback;
    }

    /**
     * Check if model exists.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return $this->exists;
    }

    /**
     * @return bool
     */
    public function wasRecentlyUpdated(): bool
    {
        return $this->wasRecentlyUpdated;
    }

    /**
     * @return bool
     */
    public function wasRecentlyCreated(): bool
    {
        return $this->wasRecentlyCreated;
    }

    /**
     * @param null|string|string[] $attributes
     *
     * @return bool
     */
    public function wasChangedOrInitialized(array|string|null $attributes = null): bool
    {
        if ($this->wasChanged($attributes)) {
            return true;
        }

        return $this->wasRecentlyCreated && $this->isDirty($attributes);
    }

    /**
     * Create new model query.
     *
     * @return \App\Core\Parents\Queries\QueryBuilder
     */
    public function newModelQuery(): QueryBuilderInterface
    {
        return (new QueryBuilder($this))->withoutGlobalScopes();
    }

    /**
     * @param null|string $attribute
     *
     * @return mixed
     */
    public function getPreviouslyChanged(string|null $attribute = null): mixed
    {
        $previous = $this->previous ?? [];

        if ($attribute === null) {
            return $previous;
        }

        return $previous[$attribute] ?? null;
    }

    /**
     * @param null|string $attribute
     *
     * @return bool
     */
    public function hasPreviouslyChanged(string|null $attribute = null): bool
    {
        return $attribute === null ? $this->previous !== null && $this->previous !== [] : isset($this->previous[$attribute]);
    }

    /**
     * @param null|string[] $attributes
     *
     * @return \App\Core\Values\Diff\ModelAttributesDiff
     */
    public function getDiffFromPrevious(?array $attributes = null): ModelAttributesDiff
    {
        if ($attributes !== null) {
            $base = \array_reduce(
                $attributes,
                fn (ModelAttributesDiff $carryDiff, string $attribute): ModelAttributesDiff => $carryDiff->put(
                    $attribute,
                    $this->getAttributeValue($attribute),
                    $this->hasPreviouslyChanged($attribute)
                        ? $this->getPreviouslyChanged($attribute)
                        : $this->getOriginal($attribute)
                ),
                new ModelAttributesDiff()
            );
        } else {
            $base = new ModelAttributesDiff($this->getAttributes(), \array_merge(
                $this->getOriginal(),
                $this->getPreviouslyChanged()
            ));
        }

        return $this->getExtendedAttributesDiffUsingResolvers($base, $attributes);
    }

    /**
     * @return string[]
     */
    public function getAttributeKeys(): array
    {
        return \array_keys($this->attributes);
    }

    /**
     * @param string $attribute
     *
     * @return null|\App\Core\Values\Diff\Diff<mixed>
     */
    public function getAttributeDiffFromPrevious(string $attribute): Diff|null
    {
        return $this->getExtendedAttributesDiffUsingResolvers(
            new ModelAttributesDiff([
                $attribute => $this->getAttributeValue($attribute),
            ], [
                $attribute => $this->getPreviouslyChanged($attribute),
            ])
        )->getAttributeDiff($attribute);
    }

    /**
     * Is attribute changed by previous save?
     *
     * @param string $attribute
     *
     * @return bool
     */
    public function wasPreviouslyChanged(string $attribute): bool
    {
        return \array_key_exists($attribute, $this->getPreviouslyChanged());
    }

    /**
     * @param string $relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function getRelationInstance(string $relation): Relation
    {
        return $this->{$relation}();
    }

    /**
     * @inheritDoc
     */
    public function isBeingSaved(): bool
    {
        return $this->isBeingSaved;
    }

    /**
     * Eager load relation counts on the model.
     *
     * @param \App\Core\Values\RelationSumDefinition|\App\Core\Values\RelationSumDefinition[] $relations
     *
     * @return \App\Core\Parents\Models\Model
     */
    public function loadSumFromDefinitions(array|RelationSumDefinition $relations): self
    {
        $relations = $relations instanceof RelationSumDefinition ? \func_get_args() : $relations;

        /**
         * @var \App\Core\Values\Collections\ModelCollection $collection for phpstan
         *
         * @noinspection PhpRedundantVariableDocTypeInspection
         */
        $collection = ModelCollection::make([$this]);

        $collection->loadSumFromDefinitions($relations);

        return $this;
    }

    /**
     * @inheritDoc
     *
     * This method is copy of the parent function, with added isBeingSaved behaviour.
     *
     * @param array<string,mixed> $options
     *
     * @return bool
     */
    public function save(array $options = []): bool
    {
        $hasBeenSaving = $this->isBeingSaved();

        $saved = $this->saveAndDontFinish();

        if (! $hasBeenSaving) {
            $this->finishSaving();
        }

        return $saved;
    }

    /**
     * Finish saving process.
     */
    public function finishSaving(): void
    {
        $this->onSaveFinished();
        $this->fireModelEvent(ModelEventEnum::SAVE_FINISHED);
    }

    /**
     * Save the model to the database, but do not finish the saving process.
     *
     * @param array<string,mixed> $options
     *
     * @return bool
     */
    public function saveAndDontFinish(array $options = []): bool
    {
        $this->mergeAttributesFromClassCasts();

        $query = $this->newModelQuery();

        // When is this method called before previous save is not finished, keep the previous data unchanged.
        // Otherwise, set previous data as diff between original and new data (keeping original data).
        $hasBeenSaving = $this->isBeingSaved();
        if (! $hasBeenSaving) {
            $this->previous = \array_diff_assoc($this->getRawOriginal(), $this->getAttributes());
            $this->isBeingSaved = true;
        }

        // If the "saving" event returns false we'll bail out of the save and return
        // false, indicating that the save failed. This provides a chance for any
        // listeners to cancel save operations if validations fail or whatever.
        if ($this->fireModelEvent(ModelEventEnum::SAVING) === false) {
            // keep the previous state when saving is interrupted.
            $this->isBeingSaved = $hasBeenSaving;
            return false;
        }

        // If the model already exists in the database we can just update our record
        // that is already in this database using the current IDs in this "where"
        // clause to only update this model. Otherwise, we'll just insert them.
        if ($this->exists) {
            $saved = ! $this->isDirty() || $this->performUpdate($query);
        }

        // If the model is brand new, we'll insert it into our database and set the
        // ID attribute on the model to the value of the newly inserted row's ID
        // which is typically an auto-increment value managed by the database.
        else {
            $saved = $this->performInsert($query);

            if (! $this->getConnectionName()) {
                $connection = $query->getConnection();

                if ($connection instanceof Connection) {
                    $this->setConnection($connection->getName());
                }
            }
        }

        // If the model is successfully saved, we need to do a few more things once
        // that is done. We will call the "saved" method here to run any actions
        // we need to happen after a model gets successfully saved right here.
        if ($saved) {
            $this->finishSave($options);
        }

        return $saved;
    }

    /**
     * @inheritDoc
     */
    protected function isEnumCastable($key)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function transformModelValue($key, $value): mixed
    {
        if ($this->hasCast($key)) {
            return $this->castAttribute($key, $value);
        }

        if ($value !== null
            && \in_array($key, $this->getDates(), false)
        ) {
            return $this->asDateTime($value);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    protected function performDeleteOnModel(): void
    {
        $this->fireModelEvent(ModelEventEnum::BEFORE_DELETED);

        parent::performDeleteOnModel();
    }

    /**
     * @param \App\Core\Values\Diff\ModelAttributesDiff $baseDiff
     * @param null|string[] $attributes
     *
     * @return \App\Core\Values\Diff\ModelAttributesDiff
     */
    protected function getExtendedAttributesDiffUsingResolvers(
        ModelAttributesDiff $baseDiff,
        ?array $attributes = null
    ): ModelAttributesDiff {
        return \array_reduce(
            static::$attributesDiffResolvers[static::class] ?? [],
            fn (ModelAttributesDiff $carryDiff, callable $resolver): ModelAttributesDiff => $carryDiff->merge(
                $resolver($this, $attributes)
            ),
            $baseDiff
        );
    }

    /**
     * Get the type of cast for a model attribute.
     *
     * @param string $key
     *
     * @return \App\Core\Values\DynamicModelCast|string
     */
    protected function getCastType($key): DynamicModelCast|string
    {
        $type = $this->getCasts()[$key] ?? null;

        if ($type) {
            if (\is_array($type)) {
                // array casts we shall convert to the custom unified cast type...
                return DynamicModelCast::fromArray($type);
            }

            if (\is_string($type) && Utils::isClassName($type)) {
                $parts = \explode(':', $type, 2);

                if (Utils::isClassName($parts[0])) {
                    if (\is_subclass_of($parts[0], Castable::class) || \is_subclass_of($parts[0], CastsAttributes::class)) {
                        return $type;
                    }

                    return DynamicModelCast::fromArray($parts);
                }
            }

            return \strtolower(\trim($type));
        }

        if ($this->getIncrementing() && $key === $this->getKeyName()) {
            return CastTypesEnum::INT;
        }

        return CastTypesEnum::STRING;
    }

    /**
     * @inheritDoc
     *
     * @param mixed[]|string $class
     *
     * @return string
     */
    protected function parseCasterClass(mixed $class): string
    {
        // Because of custom cast definitions, we need to handle arrays (parent expects only string)
        if (\is_array($class)) {
            return $class[0];
        }

        return parent::parseCasterClass($class);
    }

    /**
     * @inheritDoc
     */
    protected function resolveCasterClass($key): mixed
    {
        // We need to use the same method to have unified behaviour...
        $castType = $this->getCasts()[$key];

        $arguments = [];

        if (\is_string($castType) && \str_contains($castType, ':')) {
            $segments = \explode(':', $castType, 2);

            $castType = $segments[0];
            $arguments = \explode(',', $segments[1]);
        }

        if (\is_subclass_of($castType, Castable::class)) {
            $castType = $castType::castUsing($arguments);
        }

        if (\is_object($castType)) {
            return $castType;
        }

        return new DynamicModelCast($castType, ...$arguments);
    }

    /**
     * Cast an attribute to a native PHP type.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return mixed
     */
    protected function castAttribute($key, $value): mixed
    {
        $castType = $this->getCastType($key);

        // Custom cast type unifies our different kinds of cast definitions...
        if ($castType instanceof DynamicModelCast) {
            return $castType->cast($value);
        }

        if ($value === null) {
            return null;
        }

        switch ($castType) {
            case CastTypesEnum::INT:
                return (int) $value;
            case CastTypesEnum::STRING:
                return (string) $value;
            case CastTypesEnum::BOOL:
                return (bool) $value;
            case CastTypesEnum::ARRAY:
                return $this->fromJson($value);
            case CastTypesEnum::DATE:
                return $this->asDate($value)->toImmutable();
            case CastTypesEnum::DATETIME:
                return $this->asDateTime($value)->toImmutable();
        }

        if ($this->isEnumCastable($key)) {
            return $this->getEnumCastableAttributeValue($key, $value);
        }

        if ($this->isClassCastable($key)) {
            return $this->getClassCastableAttributeValue($key, $value);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    protected function isCustomDateTimeCast($cast): bool
    {
        // Because of the custom casting, we need to check that cast is actually string,
        // because parent method expects only string
        return \is_string($cast) && parent::isCustomDateTimeCast($cast);
    }

    /**
     * @inheritDoc
     */
    protected function isImmutableCustomDateTimeCast($cast): bool
    {
        // Because of the custom casting, we need to check that cast is actually string,
        // because parent method expects only string
        return \is_string($cast) && parent::isImmutableCustomDateTimeCast($cast);
    }

    /**
     * Set the keys for a save update query.
     * This is a fix for tables with composite keys.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query): Builder
    {
        // Custom behaviour for composed (multi-column) keys...
        /** @var string|string[] $primaryKeyName */
        $primaryKeyName = $this->getKeyName();

        if (\is_array($primaryKeyName)) {
            foreach ($primaryKeyName as $pk) {
                $query->where($pk, '=', $this->original[$pk] ?? $this->getAttribute($pk));
            }

            return $query;
        }

        return parent::setKeysForSaveQuery($query);
    }

    /**
     * Boot model.
     */
    protected static function boot(): void
    {
        parent::boot();

        self::updated(static function (self $model): void {
            $model->wasRecentlyUpdated = true;
        });
    }

    /**
     * Finish model saving (after all changes and events are done).
     */
    protected function onSaveFinished(): void
    {
        $this->isBeingSaved = false;
    }

    /**
     * Get relation count.
     *
     * @param string $alias
     * @param array<int|string,callable|string> $loader
     *
     * @return int
     */
    protected function getRelationCount(string $alias, array $loader): int
    {
        $value = $this->getAttributeValue($alias);

        if ($value === null) {
            $this->loadCount($loader);
        }

        return $this->getAttributeValue($alias);
    }

    /**
     * Get relation sum.
     *
     * @param string $alias
     * @param \App\Core\Values\RelationSumDefinition $loader
     *
     * @return float
     */
    protected function getRelationSum(string $alias, RelationSumDefinition $loader): float
    {
        $value = $this->getAttributeValue($alias);

        if ($value === null) {
            $this->loadSumFromDefinitions($loader);
        }

        return (float) ($this->getAttributeValue($alias) ?? 0);
    }
}
