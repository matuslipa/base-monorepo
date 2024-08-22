<?php

declare(strict_types=1);

namespace App\Core\Values\Diff;

/**
 * @template TValue
 */
final class Diff
{
    /**
     * @param TValue $new
     * @param TValue $old
     */
    public function __construct(
        private readonly mixed $new,
        private readonly mixed $old
    ) {
    }

    /**
     * @return TValue
     */
    public function getNew(): mixed
    {
        return $this->new;
    }

    /**
     * @return TValue
     */
    public function getOld(): mixed
    {
        return $this->old;
    }
}
