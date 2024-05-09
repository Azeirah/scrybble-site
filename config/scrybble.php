<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Deployment environment
    |--------------------------------------------------------------------------
    |
    | Scrybble is typically hosted on a server
    | Options
    | - "self-hosted": Deployment for yourself only, no gumroad license required to use
    |                  You can also use this value for development
    | - "commercial": Deployment for multiple (and paid) users
     */
    'deployment_environment' => env("SCRYBBLE_DEPLOYMENT_ENVIRONMENT", 'self-hosted'),


    /*
    |--------------------------------------------------------------------------
    | Storage platform
    |--------------------------------------------------------------------------
    |
    | Where PRM files are kept and made available for download
    | Options
    | - "S3": aws S3 or compatible API
    | - "disk": uses storage_path() + "efs/"
     */
    'storage_platform' => env("APP_STORAGE_PLATFORM", 'disk'),
];
