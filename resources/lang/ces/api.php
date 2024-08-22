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
            'sort_not_unique' => 'Obdržená pole pro řazení nejsou unikátní. Pole ":field" je duplicitní.',
            'sort_not_supported' => 'Řazení na těchto polích nejsou podporována (:fields).',
            'filters_invalid_base64' => 'Nepodařilo se dekódovat base64 vstup!',
            'filters_invalid_json' => 'Nepodařilo se dekódovat json vstup!',
            'filters_invalid_item_format' => 'Neplatný formát položky pro filtrování!',
            'filters_field_not_recognized' => 'Pole ":field" pro filtrování nebylo rozpoznáno!',
            'filters_field_not_unique' => 'Obdržená pole pro filtrování nejsou unikátní. Pole ":field" je duplicitní.',
            'filters_invalid_operation' => 'Operace ":operation" není podporována pro pole ":field" typu ":type".',
            'filters_invalid_value' => 'Byla zadána neočekávaná hodnota pro pole ":field" typu ":type".',
            'filters_invalid_array_value' => 'Neočekávaná hodnota v polie na indexu :index pro pole ":field" typu ":type".',
            'filters_invalid_operation_value' => 'Neočekávaná hodnota pro operátor ":operation".',
            'embed_not_unique' => 'Obdržená embedovaná pole nejsou unikátní. Pole ":field" je duplicitní.',
            'embed_not_supported' => 'Embedování techto polí není podporováno (:fields).',
            'too_many_attempts' => 'Přílíš mnoho pokusů',
        ],
    ],
];
