<?php

declare(strict_types=1);

namespace App\Core\Values\Diff;

use App\Core\Helpers\ValueComparator;

final class ModelAttributesDiff
{
    /**
     * @param array<string,mixed> $new
     * @param array<string,mixed> $old
     */
    public function __construct(
        private array $new = [],
        private array $old = []
    ) {
    }

    /**
     * @param array<string,\App\Core\Values\Diff\Diff<mixed>> $diffs
     *
     * @return self
     */
    public static function makeFromDiffs(array $diffs): self
    {
        $new = [];
        $old = [];

        foreach ($diffs as $attribute => $diff) {
            $new[$attribute] = $diff->getNew();
            $old[$attribute] = $diff->getOld();
        }

        return new self($new, $old);
    }

    /**
     * @param string $attribute
     *
     * @return null|\App\Core\Values\Diff\Diff<mixed>
     */
    public function getAttributeDiff(string $attribute): ?Diff
    {
        $new = $this->new[$attribute] ?? null;
        $old = $this->old[$attribute] ?? null;

        if (ValueComparator::equal($new, $old)) {
            return null;
        }

        return new Diff($new, $old);
    }

    /**
     * @param string $attribute
     * @param mixed $new
     * @param mixed $old
     *
     * @return $this
     */
    public function put(string $attribute, mixed $new, mixed $old): self
    {
        $this->new[$attribute] = $new;
        $this->old[$attribute] = $old;

        return $this;
    }

    /**
     * @param \App\Core\Values\Diff\ModelAttributesDiff $diff
     *
     * @return self
     */
    public function merge(self $diff): self
    {
        return new self(
            [...$this->new, ...$diff->new],
            [...$this->old, ...$diff->old],
        );
    }
}
