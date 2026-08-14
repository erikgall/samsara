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
    | (api.samsara.com), 'eu' for the EU region (api.eu.samsara.com),
    | or 'ca' for the Canada region (api.ca.samsara.com).
    |
    | Supported: "us", "eu", "ca"
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
    | Request Attempts
    |--------------------------------------------------------------------------
    |
    | The maximum number of request attempts, including the initial request.
    | Values below 1 are invalid.
    | Set to 1 to make a single request without retries.
    |
    */

    'attempts' => env('SAMSARA_ATTEMPTS', 3),

    /*
    |--------------------------------------------------------------------------
    | Maximum Retry-After Delay
    |--------------------------------------------------------------------------
    |
    | The largest Retry-After delay the client will honor synchronously. A 429
    | response above this ceiling is surfaced immediately so callers can
    | reschedule work instead of blocking a request or worker indefinitely.
    |
    */

    'retry_after_max_seconds' => env('SAMSARA_RETRY_AFTER_MAX_SECONDS', 30),

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

    /*
    |--------------------------------------------------------------------------
    | Webhook Secret
    |--------------------------------------------------------------------------
    |
    | The Base64-encoded secret key for verifying webhook signatures. You can
    | find this in the Samsara Dashboard when creating or viewing a webhook.
    | This is used by the VerifyWebhookSignature middleware.
    |
    */

    'webhook_secret' => env('SAMSARA_WEBHOOK_SECRET'),

];
