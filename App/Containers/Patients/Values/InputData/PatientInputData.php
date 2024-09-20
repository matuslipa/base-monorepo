<?php

declare(strict_types=1);

namespace App\Containers\Patients\Values\InputData;

/**
 * @package App\Containers\Patients
 */
final class PatientInputData
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        private readonly array $attributes,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
