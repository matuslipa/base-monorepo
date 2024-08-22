<?php

declare(strict_types=1);

namespace App\Core\Values\Enums;

enum QueuesEnum: string
{
    /**
     * @comment Fallback queue, should not be used.
     */
    case DEFAULT = 'default';

    /**
     * @comment Used for fast/light operations
     */
    case LIGHT = 'light';

    /**
     * @comment Used for long running or memory heavy operations, eg. IO operations, etc.
     */
    case HEAVY = 'heavy';

    /**
     * @comment Used for sending mails
     */
    case MAIL = 'mail';
}
