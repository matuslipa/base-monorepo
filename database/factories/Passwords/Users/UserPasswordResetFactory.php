<?php

declare(strict_types=1);

namespace Database\Factories\Passwords\Users;

use App\Containers\Passwords\Users\Models\UserPasswordReset;
use App\Containers\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserPasswordResetFactory extends Factory
{
    protected $model = UserPasswordReset::class;

    public function definition(): array
    {
        return [
            UserPasswordReset::ATTR_EXPIRATION_AT => $this->faker->unixTime(),
            UserPasswordReset::ATTR_TOKEN_HASH => $this->faker->sha256(),
        ];
    }

    public function forUser(User $user): self
    {
        return $this->state([
            UserPasswordReset::ATTR_USER_ID => $user->getKey(),
        ]);
    }
}
