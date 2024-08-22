<?php

declare(strict_types=1);

namespace Database\Factories\Authorization;

use App\Containers\Authorization\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * @inheritdoc
     */
    protected $model = Role::class;

    /**$
     * @inheritdoc
     */
    public function definition(): array
    {
        return [
            Role::ATTR_NAME => $this->faker->realText(20),
            Role::ATTR_TYPE => 1,
            Role::ATTR_IS_PROTECTED => false,
            Role::ATTR_IS_ACTIVE => true,
        ];
    }
}
