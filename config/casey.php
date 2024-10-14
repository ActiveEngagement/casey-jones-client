<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Api Key
    |--------------------------------------------------------------------------
    |
    | This value is the plain text personal access token generated in the
    | Casey Jones dashboard. To create a new token, create or select the
    | desired app, and then generate a new token and update the ENV.
    |
    */

    'api_key' => env('CASEY_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Base URI
    |--------------------------------------------------------------------------
    |
    | This value is the base URI of the Casey Jones server. The default value
    | will be for production, but can be overrided in your .env file. The
    | value should be the root URI with a trailing slash.
    |
    */

    'base_uri' => env('CASEY_BASE_URI', 'https://casey.actengage.com/api/'),

    /*
    |--------------------------------------------------------------------------
    | Redis Connection
    |--------------------------------------------------------------------------
    |
    | This value is the name of the Redis connection to use when dispatching
    | events to the stream. The possible values for this connection can be
    | found in the config/database.php in the redis configuration.
    |
    */

    'redis' => [
        'connection' => env('CASEY_REDIS_CONNECTION', 'casey')
    ]
];