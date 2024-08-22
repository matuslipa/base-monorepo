<?php

declare(strict_types=1);

namespace Database\Factories\Passwords\UserPasswords;

use App\Containers\Passwords\UserPasswords\Models\UserPassword;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserPasswordFactory extends Factory
{
    protected $model = UserPassword::class;

    public function definition(): array
    {
        return [
            UserPassword::ATTR_USER_ID => $this->faker->randomDigit(),
            UserPassword::ATTR_PASSWORD => $this->faker->password(10, 15),
        ];
    }
}
