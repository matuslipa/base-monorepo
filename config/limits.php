<?php

declare(strict_types=1);

return [
    // Remove Requests Limits
    'remove_requests' => [
        'max_items_to_delete' => 1000,
    ],

    // Image limits
    'images' => [
        'max_size' => 10000000,
    ],

    'passwords' => [
        'min_length' => 8,
    ],
];
