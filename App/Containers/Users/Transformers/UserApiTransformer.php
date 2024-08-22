<?php

declare(strict_types=1);

namespace App\Containers\Users\Transformers;

use App\Containers\Users\Models\User;
use App\Core\Parents\Transformers\ApiTransformer;

final class UserApiTransformer extends ApiTransformer
{
    public const string PROP_ID = User::ATTR_ID;

    public const string PROP_FIRST_NAME = User::ATTR_FIRST_NAME;

    public const string PROP_LAST_NAME = User::ATTR_LAST_NAME;

    public const string PROP_EMAIL = User::ATTR_EMAIL;

    public const string PROP_IS_ACTIVE = User::ATTR_IS_ACTIVE;

    public const string PROP_CREATED_AT = User::ATTR_CREATED_AT;

    /**
     * @param \App\Containers\Users\Models\User $user
     *
     * @return array{id: mixed, first_name: string, last_name: string, email: null|string, is_active: bool, created_at: null|string}
     */
    public function transform(User $user): array
    {
        return [
            self::PROP_ID => $user->getKey(),
            self::PROP_FIRST_NAME => $user->getFirstName(),
            self::PROP_LAST_NAME => $user->getLastName(),
            self::PROP_EMAIL => $user->getEmail(),
            self::PROP_IS_ACTIVE => $user->isActive(),
            self::PROP_CREATED_AT => $this->formatDateTime($user->getCreatedAt()),
        ];
    }
}
