<?php

declare(strict_types=1);

namespace App\Core\Contracts;

use Illuminate\Support\Collection;

interface QueryInterface
{
    /**
     * Get all items.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll(): Collection;
}
