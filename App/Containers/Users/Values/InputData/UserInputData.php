<?php

declare(strict_types=1);

namespace App\Containers\Users\Values\InputData;

use App\Containers\Users\Models\User;

/**
 * @package App\Containers\Users
 */
final class UserInputData
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        private array $attributes,
    ) {
    }

    /**
     * @return mixed[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->attributes[User::ATTR_IS_ACTIVE] = $isActive;
    }
}
