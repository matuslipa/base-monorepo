<?php

declare(strict_types=1);

namespace Database\Factories\Users;

use App\Containers\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * @inheritdoc
     */
    protected $model = User::class;

    /**$
     * @inheritdoc
     */
    public function definition(): array
    {
        return [
            User::ATTR_FIRST_NAME => $this->faker->firstName,
            User::ATTR_LAST_NAME => $this->faker->lastName,
            User::ATTR_EMAIL => $this->faker->unique()->email,
            User::ATTR_IS_ACTIVE => true,
        ];
    }
}
