<?php

declare(strict_types=1);

namespace App\Containers\Patients\Contracts;

use App\Core\Contracts\QueryBuilderInterface;
use Carbon\CarbonImmutable;

/**
 * @package App\Containers\Patients
 */
interface PatientsQueryInterface extends QueryBuilderInterface
{
    /**
     * @param string $email
     *
     * @return $this
     */
    public function whereEmail(string $email): self;

    /**
     * @param bool $isActive
     *
     * @return $this
     */
    public function whereIsActive(bool $isActive = true): self;

    /**
     * @param \Carbon\CarbonImmutable $birthDate
     *
     * @return $this
     */
    public function whereBirthDateIs(CarbonImmutable $birthDate): self;

    /**
     * @param string $insuranceNumber
     *
     * @return $this
     */
    public function whereInsuranceNumber(string $insuranceNumber): self;
}
