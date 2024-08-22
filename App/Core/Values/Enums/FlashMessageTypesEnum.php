<?php

declare(strict_types=1);

namespace App\Core\Values\Enums;

use App\Core\Parents\Enums\InstantiableEnum;

/**
 * @method static \App\Core\Values\Enums\FlashMessageTypesEnum SUCCESS()
 * @method static \App\Core\Values\Enums\FlashMessageTypesEnum ERROR()
 */
final class FlashMessageTypesEnum extends InstantiableEnum
{
    public const string SUCCESS = 'success';

    public const string ERROR = 'error';
}
