<?php

declare(strict_types=1);

namespace App\Containers\Patients\Requests;

use App\Containers\Patients\Contracts\PatientsRepositoryInterface;
use App\Containers\Patients\Models\Patient;
use App\Containers\Patients\Values\InputData\PatientInputData;
use App\Containers\Users\Models\User;
use App\Core\Parents\Requests\RequestFilter;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Validation\ValidationException;

/**
 * @package App\Containers\Patients
 */
final class PatientRequestFilter extends RequestFilter
{
    public const string FIELD_FIRST_NAME = Patient::ATTR_FIRST_NAME;

    public const string FIELD_LAST_NAME = Patient::ATTR_LAST_NAME;

    public const string FIELD_SEX = Patient::ATTR_SEX;

    public const string FIELD_BIRTHDATE = Patient::ATTR_BIRTHDATE;

    public const string FIELD_INSURANCE_NUMBER = Patient::ATTR_INSURANCE_NUMBER;

    public const string FIELD_INSURANCE_CODE = Patient::ATTR_INSURANCE_CODE;

    public const string FIELD_WEIGHT = Patient::ATTR_WEIGHT;

    public const string FIELD_HEIGHT = Patient::ATTR_HEIGHT;

    public const string FIELD_PHONE = Patient::ATTR_PHONE;

    public const string FIELD_EMAIL = Patient::ATTR_EMAIL;

    public const string FIELD_IS_ACTIVE = Patient::ATTR_IS_ACTIVE;


    /**
     * @param \Illuminate\Validation\Factory $validatorFactory
     * @param \App\Containers\Patients\Contracts\PatientsRepositoryInterface $patientsRepository
     */
    public function __construct(
        private readonly ValidatorFactory $validatorFactory,
        private readonly PatientsRepositoryInterface $patientsRepository,
    ) {
    }

    /**
     * Get values for model.
     *
     * @param \Illuminate\Http\Request $request
     * @param null|\App\Containers\Patients\Models\Patient $patient
     *
     * @return \App\Containers\Patients\Values\InputData\PatientInputData
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getValidatedData(
        Request $request,
        ?Patient $patient = null
    ): PatientInputData {
        $rawData = $this->validate($request, $patient);
        unset($rawData['groups']);
        return new PatientInputData($rawData);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param null|\App\Containers\Patients\Models\Patient $patient
     *
     * @return string[] validated fields
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(Request $request, ?Patient $patient = null): array
    {
        $rules = $this->getRules($request, $patient);
        $validator = $this->validatorFactory->make($request->all(), $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * @phpstan-ignore-next-line
     */
    private function getRules(Request $request, ?Patient $patient = null): array
    {
        $isPatch = $request->isMethod(Request::METHOD_PATCH);

        $required = $isPatch ? 'sometimes' : 'required';

        return [
            self::FIELD_FIRST_NAME => [
                $required,
                'string',
                'max:' . User::LIMIT_FIRST_NAME,
            ],
            self::FIELD_LAST_NAME => [
                $required,
                'string',
                'max:' . User::LIMIT_LAST_NAME,
            ],

            self::FIELD_PHONE => [
                $required,
                'string',
                'min:' . User::LIMIT_PHONE,
                'max:' . User::LIMIT_PHONE,
            ],
            self::FIELD_EMAIL => [
                'present',
                'nullable',
                'email:rfc,dns',
                function (string $attribute, ?string $value, callable $fail) use ($patient): void {
                    if ($value) {
                        $this->validateUniqueEmail($value, $patient, $fail);
                    }
                },
            ],
            self::FIELD_IS_ACTIVE => [
                $required,
                'bool',
            ],


            self::FIELD_SEX => [
                $required,
                'int',
            ],
            self::FIELD_BIRTHDATE => [
                $required,
                'date',
            ],

            self::FIELD_HEIGHT => [
                $required,
                'int',
            ],
            self::FIELD_WEIGHT => [
                $required,
                'int',
            ],

            self::FIELD_INSURANCE_CODE => [
                $required,
                'int',
            ],

            self::FIELD_INSURANCE_NUMBER => [
                $required,
                'string',
                'min:9',
                'max:10',
                function (string $attribute, ?string $value, callable $fail) use ($patient): void {
                    if ($value) {
                        $this->validateInsuranceNumberUnique($value, $patient, $fail);
                    }
                },
            ],
        ];
    }

    /**
     * @param string $value
     * @param null|\App\Containers\Patients\Models\Patient $patient
     * @param callable $fail
     */
    private function validateUniqueEmail(string $value, ?Patient $patient, callable $fail): void
    {
        $query = $this->patientsRepository->query()->whereEmail($value);

        if ($patient) {
            $query->wherePrimaryKeyNot($patient->getKey());
        }

        if ($query->someExists()) {
            $fail('validation.exists');
        }
    }

    /**
     * @param string $value
     * @param null|\App\Containers\Patients\Models\Patient $patient
     * @param callable $fail
     */
    private function validateInsuranceNumberUnique(string $value, ?Patient $patient, callable $fail): void
    {
        $query = $this->patientsRepository->query()->whereInsuranceNumber($value);

        if ($patient) {
            $query->wherePrimaryKeyNot($patient->getKey());
        }

        if ($query->someExists()) {
            $fail('validation.exists');
        }
    }
}
