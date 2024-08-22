<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | General API translations
    |--------------------------------------------------------------------------
    */

    'resources' => [
        'errors' => [
            'sort_not_unique' => 'Received sort fields are not unique. Field ":field" is duplicated.',
            'sort_not_supported' => 'Sorting on these fields is not supported (:fields).',
            'filters_invalid_base64' => 'Cannot decode base64 input!',
            'filters_invalid_json' => 'Cannot decode json input!',
            'filters_invalid_item_format' => 'Invalid format of filter item!',
            'filters_field_not_recognized' => 'Filter field ":field" is not recognized!',
            'filters_field_not_unique' => 'Received filter fields are not unique. Field ":field" is duplicated.',
            'filters_invalid_operation' => 'Operation ":operation" is not supported for field ":field" of type ":type".',
            'filters_invalid_value' => 'Unexpected value given for field ":field" of type ":type".',
            'filters_invalid_array_value' => 'Unexpected value in array on index :index for field ":field" of type ":type".',
            'filters_invalid_operation_value' => 'Unexpected value for operator ":operation".',
            'embed_not_unique' => 'Received embedded fields are not unique. Field ":field" is duplicated.',
            'embed_not_supported' => 'Embedding these fields is not supported (:fields).',
            'too_many_attempts' => 'Too many attempts',
        ],
    ],
];
