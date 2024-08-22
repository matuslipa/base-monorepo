<?php

declare(strict_types=1);

namespace App\Core\Helpers;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;

final class ContainerHelper
{
    /**
     * @template TClass
     *
     * @param class-string<TClass> $className
     *
     * @return TClass
     */
    public static function make(string $className): mixed
    {
        try {
            return Container::getInstance()->make($className);
        } catch (BindingResolutionException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
