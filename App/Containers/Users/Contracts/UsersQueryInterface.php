<?php

declare(strict_types=1);

namespace App\Containers\Users\Contracts;

use App\Core\Contracts\QueryBuilderInterface;

/**
 * @package App\Containers\Users
 */
interface UsersQueryInterface extends QueryBuilderInterface
{
    /**
     * Filter only user with given email address.
     *
     * @param string $email
     *
     * @return \App\Containers\Users\Contracts\UsersQueryInterface
     */
    public function whereEmail(string $email): self;

    /**
     * Filter only user with given token.
     *
     * @param string $token
     *
     * @return \App\Containers\Users\Contracts\UsersQueryInterface
     */
    public function whereToken(string $token): self;

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function wherePasswordExpirationEnabled(bool $enabled = true): self;
}
