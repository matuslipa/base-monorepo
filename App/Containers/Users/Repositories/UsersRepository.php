<?php

declare(strict_types=1);

namespace App\Containers\Users\Repositories;

use App\Containers\Users\Contracts\UsersQueryInterface;
use App\Containers\Users\Contracts\UsersRepositoryInterface;
use App\Containers\Users\Models\User;
use App\Containers\Users\Queries\UsersQueryBuilder;
use Illuminate\Support\Collection;

/**
 * @package App\Containers\Users
 */
final class UsersRepository implements UsersRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function get(int $id): User
    {
        /** @var \App\Containers\Users\Models\User $user */
        $user = $this->query()->getById($id);
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): Collection
    {
        return $this->query()->getAll();
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): User
    {
        $user = new User();
        $user->compactFill($data);
        $this->save($user);

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function findByEmail(string $email): ?User
    {
        /** @var null|\App\Containers\Users\Models\User $user */
        $user = $this->query()->whereEmail($email)->getFirst();
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function save(User $user): void
    {
        $user->save();
    }

    /**
     * @inheritDoc
     */
    public function delete(User $user): void
    {
        $user->delete();
    }

    /**
     * @inheritDoc
     */
    public function query(): UsersQueryInterface
    {
        return new UsersQueryBuilder(new User());
    }
}
