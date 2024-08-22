<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Users Container Translations
    |--------------------------------------------------------------------------
    */

    'validation_errors' => [
        'username_not_unique' => 'User with given username already exists.',
        'email_not_unique' => 'User with given e-mail address already exists.',
        'phone_not_unique' => 'User with given phone number already exists.',
        'role_already_assigned' => 'User already has this role assigned.',
        'billing_information_already_assigned' => 'User already has this billing information assigned.',
        'role_not_exist' => 'This role doesn\'t exist.',
        'role_not_assigned' => 'User has not assigned this role.',
        'billing_information_not_assigned' => 'User has not assigned this billing information.',
    ],
];
