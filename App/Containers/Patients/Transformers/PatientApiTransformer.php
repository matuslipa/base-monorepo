<?php

declare(strict_types=1);

namespace App\Containers\Patients\Transformers;

use App\Containers\Patients\Models\Patient;
use App\Core\Parents\Transformers\ApiTransformer;

/**
 * @package App\Containers\Patients
 */
final class PatientApiTransformer extends ApiTransformer
{
    public const string PROP_ID = Patient::ATTR_ID;

    public const string PROP_FIRST_NAME = Patient::ATTR_FIRST_NAME;

    public const string PROP_LAST_NAME = Patient::ATTR_LAST_NAME;

    public const string PROP_SEX = Patient::ATTR_SEX;

    public const string PROP_BIRTHDATE = Patient::ATTR_BIRTHDATE;

    public const string PROP_INSURANCE_NUMBER = Patient::ATTR_INSURANCE_NUMBER;

    public const string PROP_INSURANCE_CODE = Patient::ATTR_INSURANCE_CODE;

    public const string PROP_WEIGHT = Patient::ATTR_WEIGHT;

    public const string PROP_HEIGHT = Patient::ATTR_HEIGHT;

    public const string PROP_PHONE = Patient::ATTR_PHONE;

    public const string PROP_EMAIL = Patient::ATTR_EMAIL;

    public const string PROP_IS_ACTIVE = Patient::ATTR_IS_ACTIVE;

    public const string PROP_LAST_EXAMINATION = Patient::ATTR_LAST_EXAMINATION;

    public const ?string PROP_CREATED_AT = Patient::ATTR_CREATED_AT;

    public const ?string PROP_UPDATED_AT = Patient::ATTR_UPDATED_AT;

    public function transform(Patient $patient): array
    {
        return [
            self::PROP_ID => $patient->getKey(),
            self::PROP_FIRST_NAME => $patient->getFirstName(),
            self::PROP_LAST_NAME => $patient->getLastName(),
            self::PROP_SEX => $patient->getSex(),
            self::PROP_BIRTHDATE => $this->formatDate($patient->getBirthDate()),
            self::PROP_INSURANCE_NUMBER => $patient->getInsuranceNumber(),
            self::PROP_INSURANCE_CODE => $patient->getInsuranceCode(),
            self::PROP_WEIGHT => $patient->getWeight(),
            self::PROP_HEIGHT => $patient->getHeight(),
            self::PROP_PHONE => $patient->getPhone(),
            self::PROP_EMAIL => $patient->getEmail(),
            self::PROP_IS_ACTIVE => $patient->isActive(),
            self::PROP_LAST_EXAMINATION => $patient->getLastExaminationTime(),
            self::PROP_CREATED_AT => $this->formatDateTime($patient->getCreatedAt()),
            self::PROP_UPDATED_AT => $this->formatDateTime($patient->getUpdatedAt()),
        ];
    }
}
