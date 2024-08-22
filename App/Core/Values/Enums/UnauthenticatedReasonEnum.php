<?php

declare(strict_types=1);

namespace App\Core\Values\Enums;

enum UnauthenticatedReasonEnum: string
{
    case TWO_FACTOR_REQUIRED = '2FA-Required';
}
