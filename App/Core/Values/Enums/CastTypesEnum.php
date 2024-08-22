<?php

declare(strict_types=1);

namespace App\Core\Values\Enums;

use App\Core\Values\MonetaryAttribute;
use App\Core\Values\MultilingualAttribute;

final class CastTypesEnum
{
    public const string STRING = 'string';

    public const string BOOL = 'bool';

    public const string INT = 'int';

    public const string DOUBLE = 'double';

    public const string DATETIME = 'datetime';

    public const string DATE = 'date';

    public const string ARRAY = 'array';

    public const string MULTILINGUAL = MultilingualAttribute::class;

    public const string MONETARY = MonetaryAttribute::class;
}
