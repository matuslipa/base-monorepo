<?php

declare(strict_types=1);

namespace App\Containers\Users\Contracts;

use App\Containers\Users\Models\User;
use Illuminate\Support\Collection;

/**
 * @package App\Containers\Users
 */
interface UsersRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return \App\Containers\Users\Models\User
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function get(int $id): User;

    /**
     * @return \Illuminate\Support\Collection<\App\Containers\Users\Models\User>
     */
    public function getAll(): Collection;

    /**
     * @param mixed[] $data
     *
     * @return \App\Containers\Users\Models\User
     */
    public function create(array $data): User;

    /**
     * @param \App\Containers\Users\Models\User $user
     */
    public function save(User $user): void;

    /**
     * @param \App\Containers\Users\Models\User $user
     */
    public function delete(User $user): void;

    /**
     * @return \App\Containers\Users\Contracts\UsersQueryInterface
     */
    public function query(): UsersQueryInterface;

    /**
     * @param string $email
     *
     * @return null|\App\Containers\Users\Models\User
     */
    public function findByEmail(string $email): ?User;
}
