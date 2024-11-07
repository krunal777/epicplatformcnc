<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application name
    |--------------------------------------------------------------------------
    |
    | Configured application name - used as second part of the scope
    |
    */
    'app_name' => env('APP_NAME'),

    /*
    |--------------------------------------------------------------------------
    | Application Client ID
    |--------------------------------------------------------------------------
    |
    | Client ID of current application used to retrieve tokens from auth server
    |
    */
    'client_id' => env('APP_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Application Client Secret
    |--------------------------------------------------------------------------
    |
    | Client Secret of current application used to retrieve tokens from auth server
    |
    */
    'client_secret' => env('APP_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | JWT encryption algorithm
    |--------------------------------------------------------------------------
    |
    | Encryption algorithm used for decoding the JWT token
    | received from header
    |
    */
    'encryption_algorithm' => env('AUTH_ENCRYPTION_ALGORITHM', 'RS256'),

    /*
    |--------------------------------------------------------------------------
    | JWT public key path
    |--------------------------------------------------------------------------
    |
    | Path to public key that is used as secret key to decode JWT
    |
    */
    'public_key_path' => env('AUTH_PUBLIC_KEY_PATH', 'keys/public.key'),

    /*
    |--------------------------------------------------------------------------
    | Application tenancy
    |--------------------------------------------------------------------------
    |
    | List of defined tenancies (id => name)
    | Tenancy is being used as the first part of the scope in abilities.
    | Tenancy defines store ID and name.
    |
    | At least one tenancy should be defined.
    |
    */
    'tenancy' => [
        1 => 'store one',
        2 => 'store two'
    ]
];