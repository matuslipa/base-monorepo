<?php

declare(strict_types=1);

return [
    // Count of passwords which has to be unique
    'unique_passwords_count' => env('PASSWORDS_UNIQUE_COUNT', 2),

    // After this count of months require password change
    'required_changes_after' => env('PASSWORDS_CHANGES_AFTER', 2),

    'expiration_enabled' => env('PASSWORDS_EXPIRATION_ENABLED', true),

    'days_to_remind_expiration' => [14, 7, 3, 2, 1],
];
