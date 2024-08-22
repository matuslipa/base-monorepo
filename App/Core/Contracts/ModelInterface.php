<?php

declare(strict_types=1);

namespace App\Core\Contracts;

interface ModelInterface
{
    /**
     * Get identifier key of the model.
     *
     * @return array|int|string
     */
    public function getKey(): mixed;

    /**
     * Is model being saved?
     *
     * @return bool
     */
    public function isBeingSaved(): bool;
}
