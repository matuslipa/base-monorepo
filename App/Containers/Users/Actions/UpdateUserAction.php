<?php

declare(strict_types=1);

namespace App\Containers\Users\Actions;

use App\Containers\Users\Contracts\UsersRepositoryInterface;
use App\Containers\Users\Models\User;
use App\Containers\Users\Values\InputData\UserInputData;
use App\Core\Parents\Actions\Action;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;

/**
 * @package App\Containers\Users
 */
final class UpdateUserAction extends Action
{
    /**
     * @param \App\Containers\Users\Contracts\UsersRepositoryInterface $usersRepository
     * @param \Illuminate\Database\DatabaseManager $databaseManager
     * @param \Illuminate\Contracts\Hashing\Hasher $hasher
     */
    public function __construct(
        private readonly UsersRepositoryInterface $usersRepository,
        private readonly DatabaseManager $databaseManager,
        private readonly HasherContract $hasher
    ) {
    }

    /**
     * @param \App\Containers\Users\Models\User $user
     * @param \App\Containers\Users\Values\InputData\UserInputData $data
     * @param bool $logActivity
     *
     * @return \App\Containers\Users\Models\User
     *
     * @throws \Throwable
     */
    public function run(User $user, UserInputData $data, bool $logActivity = true): User
    {
        return $this->databaseManager->transaction(function () use ($user, $data, $logActivity): User {
            $attributes = $data->getAttributes();
            if (Arr::has($attributes, User::ATTR_PASSWORD)) {
                $attributes[User::ATTR_PASSWORD] = $this->hasher->make($attributes[User::ATTR_PASSWORD]);
            }

            $user->compactFill($attributes);
            $this->usersRepository->save($user);

            return $user;
        });
    }
}
