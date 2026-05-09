<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Origins that are allowed to make requests to this API. Use ['*'] to
    | allow all origins, or specify exact origins like 'http://localhost:3000'.
    |
    */

    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', '*')),

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | HTTP methods that are allowed for CORS requests.
    |
    */

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Headers that are allowed in CORS requests.
    |
    */

    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'Accept',
        'X-Requested-With',
        'Origin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Headers that the browser is allowed to access from the response.
    |
    */

    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Whether the response can be exposed when the credentials flag is true.
    | When set to true, Access-Control-Allow-Origin cannot be '*'.
    |
    */

    'supports_credentials' => (bool) env('CORS_SUPPORTS_CREDENTIALS', false),

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | How long (in seconds) the results of a preflight request can be cached.
    |
    */

    'max_age' => (int) env('CORS_MAX_AGE', 86400),

];
