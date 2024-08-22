<?php

declare(strict_types=1);

namespace App\Containers\Users\Requests;

use App\Containers\Users\Contracts\UsersRepositoryInterface;
use App\Containers\Users\Models\User;
use App\Containers\Users\Values\InputData\UserInputData;
use App\Core\Parents\Requests\RequestFilter;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * @package App\Containers\Users
 */
final class UserRequestFilter extends RequestFilter
{
    public const string FIELD_FIRST_NAME = User::ATTR_FIRST_NAME;

    public const string FIELD_LAST_NAME = User::ATTR_LAST_NAME;

    public const string FIELD_EMAIL = User::ATTR_EMAIL;

    public const string FIELD_PASSWORD = User::ATTR_PASSWORD;

    public const string FIELD_IS_ACTIVE = User::ATTR_IS_ACTIVE;

    /**
     * @param \Illuminate\Validation\Factory $validatorFactory
     * @param \App\Containers\Users\Contracts\UsersRepositoryInterface $usersRepository
     */
    public function __construct(
        private readonly ValidatorFactory $validatorFactory,
        private readonly UsersRepositoryInterface $usersRepository,
    ) {
    }

    /**
     * Get values for model.
     *
     * @param \Illuminate\Http\Request $request
     * @param null|\App\Containers\Users\Models\User $user
     *
     * @return \App\Containers\Users\Values\InputData\UserInputData
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getValidatedData(
        Request $request,
        ?User $user = null
    ): UserInputData {
        $this->validate($request, $user);

        $rawData = $request->only([
            self::FIELD_FIRST_NAME,
            self::FIELD_LAST_NAME,
            self::FIELD_EMAIL,
            self::FIELD_PASSWORD,
            self::FIELD_IS_ACTIVE,
        ]);
        return new UserInputData($rawData);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param null|\App\Containers\Users\Models\User $user
     *
     * @return string[] validated fields
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(Request $request, ?User $user = null): array
    {
        $rules = $this->getRules($request, $user);
        $validator = $this->validatorFactory->make($request->all(), $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return \array_keys($rules);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param \Illuminate\Http\Request $request
     * @param null|\App\Containers\Users\Models\User $user
     *
     * @return mixed[]
     */
    private function getRules(Request $request, ?User $user = null): array
    {
        $isPatch = $request->isMethod('PATCH') && $user;
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
            self::FIELD_EMAIL => [
                $required,
                'nullable',
                'email',
                function (string $attribute, ?string $value, callable $fail) use ($user): void {
                    if ($value) {
                        $this->validateUniqueEmail($value, $user, $fail);
                    }
                },
            ],
            self::FIELD_IS_ACTIVE => [
                $required,
                'bool',
            ],
            self::FIELD_PASSWORD => [
                $required,
                'string',
                'confirmed',
                Password::min(User::LIMIT_PASSWORD_MIN)
                    ->mixedCase()
                    ->numbers()
                    ->uncompromised(),
            ],

        ];
    }

    /**
     * @param string $value
     * @param null|\App\Containers\Users\Models\User $user
     * @param callable $fail
     */
    private function validateUniqueEmail(string $value, ?User $user, callable $fail): void
    {
        $query = $this->usersRepository->query()->whereEmail($value);

        if ($user) {
            $query->wherePrimaryKeyNot($user->getKey());
        }

        if ($query->someExists()) {
            $fail('users.validation_errors.email_not_unique');
        }
    }
}
