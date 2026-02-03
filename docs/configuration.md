---
title: Configuration
layout: default
nav_order: 3
description: "Configuration options for the Samsara SDK"
permalink: /configuration
---

# Configuration

This guide covers all configuration options for the Samsara SDK.

## Publishing Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Samsara\SamsaraServiceProvider"
```

This creates `config/samsara.php`.

## Configuration Options

### API Key

The Samsara API token for authentication.

```php
// config/samsara.php
'api_key' => env('SAMSARA_API_KEY'),
```

```env
# .env
SAMSARA_API_KEY=samsara_api_xxxxxxxxxxxxxxxx
```

You can obtain an API token from the Samsara dashboard under **Settings > API Tokens**.

### Region

The API region. Use `us` for United States or `eu` for European Union.

```php
// config/samsara.php
'region' => env('SAMSARA_REGION', 'us'),
```

```env
# .env
SAMSARA_REGION=eu
```

| Region | API Endpoint |
|--------|-------------|
| `us` | `https://api.samsara.com` |
| `eu` | `https://api.eu.samsara.com` |

### Timeout

Request timeout in seconds.

```php
// config/samsara.php
'timeout' => env('SAMSARA_TIMEOUT', 30),
```

```env
# .env
SAMSARA_TIMEOUT=60
```

### Retry

Number of times to retry failed requests.

```php
// config/samsara.php
'retry' => env('SAMSARA_RETRY', 3),
```

```env
# .env
SAMSARA_RETRY=5
```

### Per Page

Default number of items per page for paginated requests.

```php
// config/samsara.php
'per_page' => env('SAMSARA_PER_PAGE', 100),
```

```env
# .env
SAMSARA_PER_PAGE=50
```

### Webhook Secret

The Base64-encoded secret key for verifying webhook signatures.

```php
// config/samsara.php
'webhook_secret' => env('SAMSARA_WEBHOOK_SECRET'),
```

```env
# .env
SAMSARA_WEBHOOK_SECRET=your-base64-encoded-secret
```

You can obtain this secret from the Samsara dashboard when creating or viewing a webhook. See the [Webhooks Guide](resources/webhooks.md) for usage details.

## Complete Configuration File

```php
<?php

// config/samsara.php

return [
    /*
    |--------------------------------------------------------------------------
    | Samsara API Key
    |--------------------------------------------------------------------------
    |
    | Your Samsara API token. Obtain this from the Samsara dashboard under
    | Settings > API Tokens.
    |
    */
    'api_key' => env('SAMSARA_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | API Region
    |--------------------------------------------------------------------------
    |
    | The region for your Samsara account. Use 'us' for United States or
    | 'eu' for European Union customers.
    |
    */
    'region' => env('SAMSARA_REGION', 'us'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The number of seconds to wait before timing out a request.
    |
    */
    'timeout' => env('SAMSARA_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Count
    |--------------------------------------------------------------------------
    |
    | The number of times to retry a failed request before giving up.
    |
    */
    'retry' => env('SAMSARA_RETRY', 3),

    /*
    |--------------------------------------------------------------------------
    | Items Per Page
    |--------------------------------------------------------------------------
    |
    | The default number of items to fetch per page for paginated requests.
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
    |
    */
    'webhook_secret' => env('SAMSARA_WEBHOOK_SECRET'),
];
```

## Runtime Configuration

### Switching Regions

You can switch regions at runtime:

```php
use Samsara\Facades\Samsara;

// Switch to EU endpoint
Samsara::useEuEndpoint();

// Switch back to US endpoint
Samsara::useUsEndpoint();
```

### Using a Different Token

You can use a different API token for specific requests:

```php
use Samsara\Facades\Samsara;

// Set a different token for subsequent requests
Samsara::withToken('different-api-token');

// All subsequent calls now use the new token
$drivers = Samsara::drivers()->all();
```

Or chain it for a single operation:

```php
use Samsara\Facades\Samsara;

// Use a different token for this specific request
$drivers = Samsara::withToken('different-api-token')
    ->drivers()
    ->all();
```

### Creating a Fresh Instance

Create a new instance with custom configuration:

```php
use Samsara\Samsara;

$samsara = Samsara::make('api-token', [
    'timeout' => 60,
    'retry' => 5,
    'per_page' => 50,
]);

// Use EU endpoint
$samsara->useEuEndpoint();
```

## Environment-Specific Configuration

### Development

```env
# .env.local
SAMSARA_API_KEY=samsara_api_dev_token
SAMSARA_TIMEOUT=60
SAMSARA_RETRY=1
```

### Production

```env
# .env.production
SAMSARA_API_KEY=samsara_api_prod_token
SAMSARA_TIMEOUT=30
SAMSARA_RETRY=3
```

### Testing

For testing, use `SamsaraFake` instead of real API calls:

```php
use Samsara\Facades\Samsara;

$fake = Samsara::fake();
```

See the [Testing Guide](testing.md) for more details.
