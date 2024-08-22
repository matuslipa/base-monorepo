<?php

declare(strict_types=1);

namespace App\Core\Values\Enums;

use App\Core\Parents\Enums\InstantiableEnum;

/**
 * @method static \App\Core\Values\Enums\DataTypeEnum MIXED()
 * @method static \App\Core\Values\Enums\DataTypeEnum INT()
 * @method static \App\Core\Values\Enums\DataTypeEnum DECIMAL()
 * @method static \App\Core\Values\Enums\DataTypeEnum MONETARY()
 * @method static \App\Core\Values\Enums\DataTypeEnum DATE()
 * @method static \App\Core\Values\Enums\DataTypeEnum DATETIME()
 * @method static \App\Core\Values\Enums\DataTypeEnum STRING()
 * @method static \App\Core\Values\Enums\DataTypeEnum BOOL()
 */
final class DataTypeEnum extends InstantiableEnum
{
    public const string MIXED = 'mixed';

    public const string INT = 'int';

    public const string DECIMAL = 'decimal';

    public const string MONETARY = 'monetary';

    public const string DATE = 'date';

    public const string DATETIME = 'datetime';

    public const string STRING = 'string';

    public const string BOOL = 'bool';
}
