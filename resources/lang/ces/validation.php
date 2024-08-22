<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Pole musí být přijato.',
    'active_url' => 'Pole není platnou URL adresou.',
    'after' => 'Pole musí být datum po :date.',
    'after_or_equal' => 'datum musí být :date nebo pozdější.',
    'alpha' => 'Pole může obsahovat pouze písmena.',
    'alpha_dash' => 'Pole může obsahovat pouze písmena, číslice, pomlčky a podtržítka. České znaky (á, é, í, ó, ú, ů, ž, š, č, ř, ď, ť, ň) nejsou podporovány.',
    'alpha_num' => 'Pole může obsahovat pouze písmena a číslice.',
    'array' => 'Pole musí být pole.',
    'before' => 'Pole musí být datum před :date.',
    'before_or_equal' => 'Datum musí být před nebo rovno :date.',
    'between' => [
        'numeric' => 'Pole musí být hodnota mezi :min a :max.',
        'file' => 'Pole musí být větší než :min a menší než :max Kilobytů.',
        'string' => 'Pole musí být delší než :min a kratší než :max znaků.',
        'array' => 'Pole musí obsahovat nejméně :min a nesmí obsahovat více než :max prvků.',
    ],
    'boolean' => 'Pole musí být true nebo false.',
    'confirmed' => 'Pole nesouhlasí',
    'date' => 'Pole musí být platné datum.',
    'date_equals' => 'Pole musí být datum shodné s :date.',
    'date_format' => 'Pole není platný formát data podle :format.',
    'different' => 'Pole a :other se musí lišit.',
    'digits' => 'Pole musí být :digits pozic dlouhé.',
    'digits_between' => 'Pole musí být dlouhé nejméně :min a nejvíce :max pozic.',
    'dimensions' => 'Pole má neplatné rozměry.',
    'distinct' => 'Pole má duplicitní hodnotu.',
    'email' => 'Pole není platný formát.',
    'ends_with' => 'Pole musí končit jednou z následujících hodnot: :values',
    'exists' => 'Zvolená hodnota pro pole není platná.',
    'file' => 'Pole musí být soubor.',
    'filled' => 'Pole musí být vyplněno.',
    'gt' => [
        'numeric' => 'Pole musí být větší než :value.',
        'file' => 'Velikost souboru pole musí být větší než :value kB.',
        'string' => 'Počet znaků pole musí být větší :value.',
        'array' => 'Pole pole musí mít více prvků než :value.',
    ],
    'gte' => [
        'numeric' => 'pole musí být větší nebo rovno :value.',
        'file' => 'Velikost souboru pole musí být větší nebo rovno :value kB.',
        'string' => 'Počet znaků pole musí být větší nebo rovno :value.',
        'array' => 'Pole pole musí mít :value prvků nebo více.',
    ],
    'image' => 'Pole musí být obrázek.',
    'in' => 'Zvolená hodnota pro pole je neplatná.',
    'in_array' => 'Pole není obsažen v :other.',
    'integer' => 'Pole musí být celé číslo.',
    'ip' => 'Pole musí být platnou IP adresou.',
    'ipv4' => 'Pole musí být platná IPv4 adresa.',
    'ipv6' => 'Pole musí být platná IPv6 adresa.',
    'json' => 'Pole musí být platný JSON řetězec.',
    'lt' => [
        'numeric' => 'Pole musí být menší než :value.',
        'file' => 'Velikost souboru pole musí být menší než :value kB.',
        'string' => 'Pole musí obsahovat méně než :value znaků.',
        'array' => 'Pole by měl obsahovat méně než :value položek.',
    ],
    'lte' => [
        'numeric' => 'Pole musí být menší nebo rovno než :value.',
        'file' => 'Velikost souboru pole musí být menší než :value kB.',
        'string' => 'Pole nesmí být delší než :value znaků.',
        'array' => 'Pole by měl obsahovat maximálně :value položek.',
    ],
    'max' => [
        'numeric' => 'Pole nemůže být větší než :max.',
        'file' => 'Velikost souboru pole musí být menší než :value kB.',
        'string' => 'Pole nemůže být delší než :max znaků.',
        'array' => 'Pole nemůže obsahovat více než :max prvků.',
    ],
    'mimes' => 'Pole musí být jeden z následujících datových typů :values.',
    'mimetypes' => 'Pole musí být jeden z následujících datových typů :values.',
    'min' => [
        'numeric' => 'Pole musí být větší než :min.',
        'file' => 'Pole musí být větší než :min kB.',
        'string' => 'Pole musí být delší než :min znaků.',
        'array' => 'Pole musí obsahovat více než :min prvků.',
    ],
    'not_in' => 'Zvolená hodnota pro pole je neplatná.',
    'object' => 'Pole musí být objekt',
    'not_regex' => 'Pole musí být regulární výraz.',
    'numeric' => 'Pole musí být číslo.',
    'present' => 'Pole musí být vyplněno.',
    'regex' => 'Pole nemá správný formát.',
    'required' => 'Pole musí být vyplněno.',
    'required_if' => 'Pole musí být vyplněno pokud :other je :value.',
    'required_unless' => 'Pole musí být vyplněno dokud :other je v :values.',
    'required_with' => 'Pole musí být vyplněno pokud :values je vyplněno.',
    'required_with_all' => 'Pole musí být vyplněno pokud :values je zvoleno.',
    'required_without' => 'Pole musí být vyplněno pokud :values není vyplněno.',
    'required_without_all' => 'Pole musí být vyplněno pokud není žádné z :values zvoleno.',
    'same' => 'Pole a :other se musí shodovat.',
    'size' => [
        'numeric' => 'Pole musí být přesně :size.',
        'file' => 'Pole musí mít přesně :size Kilobytů.',
        'string' => 'Pole musí být přesně :size znaků dlouhý.',
        'array' => 'Pole musí obsahovat právě :size prvků.',
    ],
    'starts_with' => 'Pole musí začínat jednou z následujících hodnot: :values',
    'string' => 'Pole musí být řetězec znaků.',
    'timezone' => 'Pole musí být platná časová zóna.',
    'unique' => 'Pole musí být unikátní.',
    'uploaded' => 'Nahrávání pole se nezdařilo.',
    'url' => 'Formát pole je neplatný.',
    'uuid' => 'Pole musí být validní UUID.',
    'decimal' => 'Neplatná decimální hodnota.',
    'amount' => [
        'currency_not_exists' => 'Zvolená měna není dostupná pro tento obchod.',
    ],
    'translation' => [
        'language_not_exists' => 'Zvolený jazyk není dostupný pro tento obchod.',
    ],
    'access' => 'Přístup byl zamítnut.',
    'checked' => 'Pole musí být zaškrtnuto',
    'password' => [
        'mixed' => 'Pole musí obsahovat alespoň jedno velké a jedno malé písmeno',
        'numbers' => 'Pole musí obsahovat alespoň jedno číslo',
        'uncompromised' => 'Heslo se objevilo při úniku dat. Zvolte prosím jiné heslo.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'attribute_translation_missing_languages' => 'Chybí překlady pro některé jazyky.',
    'unknown_field' => 'Neznámé pole.',
    'secure_url' => 'Adresa musí být HTTPS.',

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],
];
