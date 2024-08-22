<?php

declare(strict_types=1);

namespace App\Containers\Users\Models;

use App\Containers\Users\Contracts\UsersQueryInterface;
use App\Containers\Users\Queries\UsersQueryBuilder;
use App\Core\Parents\Models\Model;
use App\Core\Values\Enums\CastTypesEnum;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\Authenticatable;

final class User extends Model implements Authenticatable
{
    public const string TABLE_NAME = 'user';

    /**
     * Attributes of the model.
     */
    public const string ATTR_ID = 'id';

    public const string ATTR_FIRST_NAME = 'first_name';

    public const string ATTR_LAST_NAME = 'last_name';

    public const string ATTR_EMAIL = 'email';

    public const string ATTR_PASSWORD = 'password';

    public const string ATTR_PASSWORD_EXPIRATION_ENABLED = 'password_expiration_enabled';

    public const string ATTR_IS_ACTIVE = 'is_active';

    public const string ATTR_TOKEN = 'token';

    public const string ATTR_CREATED_AT = self::CREATED_AT;

    public const string ATTR_UPDATED_AT = self::UPDATED_AT;

    /**
     * Model limits.
     */
    public const int LIMIT_EMAIL = 60;

    public const int LIMIT_PHONE = 50;

    public const int LIMIT_PASSWORD = 100;

    public const int LIMIT_PASSWORD_MIN = 8;

    public const int LIMIT_PASSWORD_MAX = 100;

    public const int LIMIT_FIRST_NAME = 50;

    public const int LIMIT_LAST_NAME = 50;

    /**
     * @inheritDoc
     */
    protected $fillable = [
        self::ATTR_EMAIL,
        self::ATTR_FIRST_NAME,
        self::ATTR_LAST_NAME,
        self::ATTR_PASSWORD,
        self::ATTR_PASSWORD_EXPIRATION_ENABLED,
        self::ATTR_IS_ACTIVE,
        self::ATTR_TOKEN,
    ];

    /**
     * @inhertiDoc
     */
    protected $hidden = [
        self::ATTR_PASSWORD,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var string[]
     */
    protected $casts = [
        self::ATTR_FIRST_NAME => CastTypesEnum::STRING,
        self::ATTR_LAST_NAME => CastTypesEnum::STRING,
        self::ATTR_EMAIL => CastTypesEnum::STRING,
        self::ATTR_PASSWORD_EXPIRATION_ENABLED => CastTypesEnum::BOOL,
        self::ATTR_IS_ACTIVE => CastTypesEnum::BOOL,
        self::ATTR_TOKEN => CastTypesEnum::STRING,
    ];

    /**
     * Create new model query.
     *
     * @return \App\Containers\Users\Contracts\UsersQueryInterface
     */
    public function newModelQuery(): UsersQueryInterface
    {
        return (new UsersQueryBuilder($this))->withoutGlobalScopes();
    }

    /**
     * Get first name.
     *
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->getAttribute(self::ATTR_FIRST_NAME);
    }

    /**
     * @return null|string
     */
    public function getToken(): ?string
    {
        return $this->getAttribute(self::ATTR_TOKEN);
    }

    /**
     * Get last name.
     *
     * @return string
     */
    public function getLastName(): string
    {
        return $this->getAttribute(self::ATTR_LAST_NAME);
    }

    /**
     * Get email.
     *
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->getAttribute(self::ATTR_EMAIL);
    }

    /**
     * Check if user is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getAttribute(self::ATTR_IS_ACTIVE);
    }

    /**
     * Get full name.
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * Fill model with compact data.
     *
     * @param mixed[] $data
     */
    public function compactFill(array $data): void
    {
        // place some default values?

        $this->fill($data);
    }

    /**
     * @return \Carbon\CarbonImmutable
     */
    public function getCreatedAt(): CarbonImmutable
    {
        return $this->getAttributeValue(self::ATTR_CREATED_AT);
    }

    /**
     * @return \Carbon\CarbonImmutable
     */
    public function getUpdatedAt(): CarbonImmutable
    {
        return $this->getAttributeValue(self::ATTR_UPDATED_AT);
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return $this->getKeyName();
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return int
     */
    public function getAuthIdentifier(): int
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        return $this->getAttribute(self::ATTR_PASSWORD);
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken(): string
    {
        return '';
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     */
    public function setRememberToken($value): void
    {
    }

    /**
     * Set password
     *
     * @param null|string $hash - hashed password
     */
    public function setPassword(?string $hash): void
    {
        $this->setAttribute(self::ATTR_PASSWORD, $hash);
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    /**
     * @inheritDoc
     */
    public function getAuthPasswordName(): string
    {
        return self::ATTR_PASSWORD;
    }
}
