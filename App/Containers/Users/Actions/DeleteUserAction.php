<?php

declare(strict_types=1);

namespace App\Containers\Users\Actions;

use App\Containers\Users\Contracts\UsersRepositoryInterface;
use App\Containers\Users\Models\User;
use App\Core\Parents\Actions\Action;
use Illuminate\Database\DatabaseManager;

/**
 * @package App\Containers\Users
 */
final class DeleteUserAction extends Action
{
    /**
     * @param \App\Containers\Users\Contracts\UsersRepositoryInterface $usersRepository
     * @param \Illuminate\Database\DatabaseManager $databaseManager
     */
    public function __construct(
        private readonly UsersRepositoryInterface $usersRepository,
        private readonly DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param \App\Containers\Users\Models\User $user
     *
     * @throws \Throwable
     */
    public function run(User $user): void
    {
        $this->databaseManager->transaction(function () use ($user): void {
            $this->usersRepository->delete($user);
        });
    }
}
