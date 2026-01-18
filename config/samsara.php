<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Samsara API Key
    |--------------------------------------------------------------------------
    |
    | Your Samsara API token. You can obtain this from the Samsara Dashboard
    | under Settings -> Organization -> API Tokens.
    |
    */

    'api_key' => env('SAMSARA_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | API Region
    |--------------------------------------------------------------------------
    |
    | The region for your Samsara API. Use 'us' for the US region
    | (api.samsara.com) or 'eu' for the EU region (api.eu.samsara.com).
    |
    | Supported: "us", "eu"
    |
    */

    'region' => env('SAMSARA_REGION', 'us'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The number of seconds to wait for a response from the Samsara API
    | before timing out.
    |
    */

    'timeout' => env('SAMSARA_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Attempts
    |--------------------------------------------------------------------------
    |
    | The number of times to retry a failed request before giving up.
    | Set to 0 to disable retries.
    |
    */

    'retry' => env('SAMSARA_RETRY', 3),

    /*
    |--------------------------------------------------------------------------
    | Default Items Per Page
    |--------------------------------------------------------------------------
    |
    | The default number of items to retrieve per page when using
    | paginated API endpoints.
    |
    */

    'per_page' => env('SAMSARA_PER_PAGE', 100),

];
