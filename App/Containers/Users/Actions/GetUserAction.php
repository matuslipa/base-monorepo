<?php

declare(strict_types=1);

namespace App\Containers\Users\Actions;

use App\Containers\Users\Contracts\UsersRepositoryInterface;
use App\Containers\Users\Models\User;
use App\Core\Parents\Actions\Action;

/**
 * @package App\Containers\Users
 */
final class GetUserAction extends Action
{
    /**
     * @param \App\Containers\Users\Contracts\UsersRepositoryInterface $usersRepository
     */
    public function __construct(
        private readonly UsersRepositoryInterface $usersRepository
    ) {
    }

    /**
     * @param int $id
     *
     * @return \App\Containers\Users\Models\User
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function run(int $id): User
    {
        return $this->usersRepository->get($id);
    }
}
