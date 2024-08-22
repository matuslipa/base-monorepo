<?php

declare(strict_types=1);

return [
    /**
     * Value for X-Frame-Options header.
     */
    'x_frame_options' => 'deny',

    /**
     * When true, header "X-Content-Type-Options: nosniff" is send.
     */
    'x_content_type_options' => true,

    /**
     * When enabled, header "Strict-Transport-Security" is send.
     */
    'hsts' => [
        'enabled' => true,
        'max_age' => 31536000,
        'include_subdomains' => true,
    ],

    'cors' => [
        'enabled' => true,

        /**
         * Credentials are currently not supported by API.
         */
        'allow_credentials' => false,

        /**
         * The Access-Control-Allow-Headers response header content.
         * Indicates which headers can be received as part of the request by listing their names.
         */
        'allow_headers' => [
            'Accept',
            'Accept-Language',
            'Content-Language',
            'Content-Type',
            'Origin',
            'Authorization',
            'Content-Range',
            'Content-Length',
            'X-Web-Language',
        ],

        /**
         * The Access-Control-Expose-Headers response header content.
         * Indicates which headers can be exposed as part of the response by listing their names.
         */
        'expose_headers' => [
            'Cache-Control',
            'Content-Language',
            'Content-Type',
            'Expires',
            'Last-Modified',
            'Pragma',
            'X-Frame-Options',
            'X-Content-Type-Options',
            'X-RateLimit-Limit',
            'X-RateLimit-Remaining',
            'Location',
            'X-Robots-Tag',
            'X-Web-Language',
        ],

        /**
         * The Access-Control-Max-Age response header value.
         * Indicates how long the results of a preflight request can be cached.
         */
        // seconds
        'max_age' => 10,
    ],
];
