<?php

declare(strict_types=1);

namespace App\Containers\Users\Queries;

use App\Containers\Users\Contracts\UsersQueryInterface;
use App\Containers\Users\Models\User;
use App\Core\Parents\Queries\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;

/**
 * @package App\Containers\Users
 *
 * @property \App\Containers\Users\Models\User $model
 */
final class UsersQueryBuilder extends QueryBuilder implements UsersQueryInterface
{
    /**
     * @inheritDoc
     */
    public function whereEmail(string $email): self
    {
        return $this->where(User::ATTR_EMAIL, '=', $email);
    }

    /**
     * @inheritDoc
     */
    public function whereToken(string $token): self
    {
        return $this->where(User::ATTR_TOKEN, '=', $token);
    }

    public function wherePasswordExpirationEnabled(bool $enabled = true): self
    {
        return $this->where(User::ATTR_PASSWORD_EXPIRATION_ENABLED, '=', $enabled);
    }

    /**
     * @inheritDoc
     */
    public function whereHavingRole(int $roleId): self
    {
        $this->whereHas(User::RELATION_ROLES, static function (Builder $query) use ($roleId): void {
            $query->whereKey($roleId);
        });

        return $this;
    }
}
