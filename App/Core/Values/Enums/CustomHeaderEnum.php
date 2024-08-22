<?php

declare(strict_types=1);

namespace App\Core\Values\Enums;

enum CustomHeaderEnum: string
{
    case UnauthenticatedReason = 'X-Unauthenticated-Reason';
}
