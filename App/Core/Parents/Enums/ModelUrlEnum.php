<?php

declare(strict_types=1);

namespace App\Core\Parents\Enums;

/**
 * Class ModelUrlEnum
 *
 * @package App\Core\Parents\Enums
 */
abstract class ModelUrlEnum extends InstantiableEnum
{
    final public const PROJECT = 'project';

    /**
     * @var string[]
     */
    protected static array $models = [];

    /**
     * LikableModelUrlEnum constructor.
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (\class_exists($value)) {
            $urlName = $this->getUrlNameByClass($value);
            if ($urlName) {
                $value = $urlName;
            }
        }

        parent::__construct($value);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public static function isValid(mixed $value): bool
    {
        return parent::isValid($value) &&
            \array_key_exists($value, static::$models);
    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return static::$models[$this->getValue()];
    }

    /**
     * @param string $className
     *
     * @return null|string
     */
    private function getUrlNameByClass(string $className): ?string
    {
        $class = \array_flip(static::$models)[$className] ?? null;
        return $class ? ((string) $class) : null;
    }
}
