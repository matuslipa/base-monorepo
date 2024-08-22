<?php

declare(strict_types=1);

namespace App\Core\Values\Enums;

use App\Core\Parents\Enums\InstantiableEnum;

/**
 * @method static \App\Core\Values\Enums\DataTypePartEnum YEAR()
 * @method static \App\Core\Values\Enums\DataTypePartEnum MONTH()
 * @method static \App\Core\Values\Enums\DataTypePartEnum WEEK()
 * @method static \App\Core\Values\Enums\DataTypePartEnum WEEKDAY()
 * @method static \App\Core\Values\Enums\DataTypePartEnum DAY()
 * @method static \App\Core\Values\Enums\DataTypePartEnum HOUR()
 * @method static \App\Core\Values\Enums\DataTypePartEnum MINUTE()
 */
final class DataTypePartEnum extends InstantiableEnum
{
    public const string YEAR = 'year';

    public const string MONTH = 'month';

    public const string WEEK = 'week';

    public const string WEEKDAY = 'weekday';

    public const string DAY = 'day';

    public const string HOUR = 'hour';

    public const string MINUTE = 'minute';

    /**
     * @param \App\Core\Values\Enums\DataTypeEnum $dataType
     *
     * @return string[]
     */
    public static function getForDataType(DataTypeEnum $dataType): array
    {
        if ($dataType->equals(DataTypeEnum::DATE())) {
            return [
                self::YEAR,
                self::MONTH,
                self::WEEK,
                self::WEEKDAY,
                self::DAY,
            ];
        }

        if ($dataType->equals(DataTypeEnum::DATETIME())) {
            return [
                self::YEAR,
                self::MONTH,
                self::WEEK,
                self::WEEKDAY,
                self::DAY,
                self::HOUR,
                self::MINUTE,
            ];
        }

        return [];
    }
}
