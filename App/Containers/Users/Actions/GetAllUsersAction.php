<?php

declare(strict_types=1);

namespace App\Containers\Users\Actions;

use App\Containers\Users\Contracts\UsersQueryInterface;
use App\Containers\Users\Contracts\UsersRepositoryInterface;
use App\Core\Parents\Actions\Action;
use Illuminate\Support\Collection;

/**
 * @package App\Containers\Users
 */
final class GetAllUsersAction extends Action
{
    /**
     * @param \App\Containers\Users\Contracts\UsersRepositoryInterface $usersRepository
     */
    public function __construct(
        private readonly UsersRepositoryInterface $usersRepository
    ) {
    }

    /**
     * @return \Illuminate\Support\Collection<\App\Containers\Users\Models\User>
     */
    public function run(): Collection
    {
        return $this->query()->getAll();
    }

    /**
     * @return \App\Containers\Users\Contracts\UsersQueryInterface
     */
    public function query(): UsersQueryInterface
    {
        return $this->usersRepository->query();
    }
}
