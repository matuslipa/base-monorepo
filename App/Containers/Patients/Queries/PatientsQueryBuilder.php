<?php

declare(strict_types=1);

namespace App\Containers\Patients\Queries;

use App\Containers\Patients\Contracts\PatientsQueryInterface;
use App\Containers\Patients\Models\Patient;
use App\Core\Parents\Queries\QueryBuilder;
use Carbon\CarbonImmutable;

/**
 * @package App\Containers\Patients
 *
 * @property \App\Containers\Patients\Models\Patient $model
 */
final class PatientsQueryBuilder extends QueryBuilder implements PatientsQueryInterface
{
    /**
     * @inheritDoc
     */
    public function whereEmail(string $email): self
    {
        return $this->where(Patient::ATTR_EMAIL, '=', $email);
    }

    /**
     * @inheritDoc
     */
    public function whereIsActive(bool $isActive = true): self
    {
        return $this->where(Patient::ATTR_IS_ACTIVE, $isActive);
    }

    /**
     * @inheritDoc
     */
    public function whereBirthDateIs(CarbonImmutable $birthDate): self
    {
        return $this->whereDate(Patient::ATTR_BIRTHDATE, '=', $birthDate);
    }

    /**
     * @inheritDoc
     */
    public function whereInsuranceNumber(string $insuranceNumber): self
    {
        return $this->where(Patient::ATTR_INSURANCE_NUMBER, '=', $insuranceNumber);
    }
}
