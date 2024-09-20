<?php

declare(strict_types=1);

namespace App\Containers\Patients\Models;

use App\Containers\Patients\Contracts\PatientsQueryInterface;
use App\Containers\Patients\Queries\PatientsQueryBuilder;
use App\Core\Parents\Models\Model;
use App\Core\Values\Enums\CastTypesEnum;
use Carbon\CarbonImmutable;

/**
 * @package App\Containers\Patients
 */
final class Patient extends Model
{
    /**
     * Attributes of the model.
     */
    public const string ATTR_ID = 'id';

    public const string ATTR_FIRST_NAME = 'first_name';

    public const string ATTR_LAST_NAME = 'last_name';

    public const string ATTR_SEX = 'sex';

    public const string ATTR_BIRTHDATE = 'birthdate';

    public const string ATTR_INSURANCE_NUMBER = 'insurance_number';

    public const string ATTR_INSURANCE_CODE = 'insurance_code';

    public const string ATTR_WEIGHT = 'weight';

    public const string ATTR_HEIGHT = 'height';

    public const string ATTR_PHONE = 'phone';

    public const string ATTR_EMAIL = 'email';

    public const string ATTR_LAST_EXAMINATION = 'last_examination';

    public const string ATTR_IS_ACTIVE = 'is_active';

    public const ?string ATTR_CREATED_AT = self::CREATED_AT;

    public const ?string ATTR_UPDATED_AT = self::UPDATED_AT;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        self::ATTR_FIRST_NAME,
        self::ATTR_LAST_NAME,
        self::ATTR_SEX,
        self::ATTR_BIRTHDATE,
        self::ATTR_INSURANCE_NUMBER,
        self::ATTR_INSURANCE_CODE,
        self::ATTR_WEIGHT,
        self::ATTR_HEIGHT,
        self::ATTR_PHONE,
        self::ATTR_EMAIL,
        self::ATTR_IS_ACTIVE,
        self::ATTR_LAST_EXAMINATION,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var string[]
     */
    protected $casts = [
        self::ATTR_FIRST_NAME => CastTypesEnum::STRING,
        self::ATTR_LAST_NAME => CastTypesEnum::STRING,
        self::ATTR_EMAIL => CastTypesEnum::STRING,
        self::ATTR_PHONE => CastTypesEnum::STRING,
        self::ATTR_BIRTHDATE => CastTypesEnum::DATE,
        self::ATTR_SEX => CastTypesEnum::INT,
        self::ATTR_INSURANCE_NUMBER => CastTypesEnum::STRING,
        self::ATTR_INSURANCE_CODE => CastTypesEnum::INT,
        self::ATTR_WEIGHT => CastTypesEnum::INT,
        self::ATTR_HEIGHT => CastTypesEnum::INT,
        self::ATTR_IS_ACTIVE => CastTypesEnum::BOOL,
        self::ATTR_LAST_EXAMINATION => CastTypesEnum::DATETIME,
    ];

    /**
     * Create new model query.
     *
     * @return \App\Containers\Patients\Contracts\PatientsQueryInterface
     */
    public function newModelQuery(): PatientsQueryInterface
    {
        return (new PatientsQueryBuilder($this))->withoutGlobalScopes();
    }

    /**
     * Fill model with compact data.
     *
     * @param mixed[] $data
     */
    public function compactFill(array $data): void
    {
        // place some default values?

        $this->fill($data);
    }

    /**
     * Get first name.
     *
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->getAttribute(self::ATTR_FIRST_NAME);
    }

    /**
     * @return null|string
     */
    public function getPhone(): ?string
    {
        return $this->getAttribute(self::ATTR_PHONE);
    }

    /**
     * @return null|\Carbon\CarbonImmutable
     */
    public function getLastExaminationTime(): ?CarbonImmutable
    {
        return $this->getAttribute(self::ATTR_LAST_EXAMINATION);
    }

    /**
     * @return null|int
     */
    public function getInsuranceCode(): ?int
    {
        return $this->getAttribute(self::ATTR_INSURANCE_CODE);
    }

    /**
     * @return null|string
     */
    public function getInsuranceNumber(): ?string
    {
        return $this->getAttribute(self::ATTR_INSURANCE_NUMBER);
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->getAttribute(self::ATTR_HEIGHT);
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->getAttribute(self::ATTR_WEIGHT);
    }

    /**
     * @return null|\Carbon\CarbonImmutable
     */
    public function getBirthDate(): ?CarbonImmutable
    {
        return $this->getAttributeValue(self::ATTR_BIRTHDATE);
    }

    /**
     * @return int
     */
    public function getSex(): int
    {
        return $this->getAttribute(self::ATTR_SEX);
    }

    /**
     * Get last name.
     *
     * @return string
     */
    public function getLastName(): string
    {
        return $this->getAttribute(self::ATTR_LAST_NAME);
    }

    /**
     * Get email.
     *
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->getAttribute(self::ATTR_EMAIL);
    }

    /**
     * Get full name.
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * Check if is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getAttribute(self::ATTR_IS_ACTIVE);
    }

    /**
     * @return \Carbon\CarbonImmutable
     */
    public function getCreatedAt(): CarbonImmutable
    {
        return $this->getAttributeValue(self::ATTR_CREATED_AT);
    }

    /**
     * @return \Carbon\CarbonImmutable
     */
    public function getUpdatedAt(): CarbonImmutable
    {
        return $this->getAttributeValue(self::ATTR_UPDATED_AT);
    }
}
