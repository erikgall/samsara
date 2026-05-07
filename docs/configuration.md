---
title: Configuration
layout: default
nav_order: 3
description: Configuration options and runtime overrides for the Samsara SDK.
permalink: /configuration
---

# Configuration

- [Introduction](#introduction)
- [Publishing the Configuration](#publishing-the-configuration)
- [Configuration Options](#configuration-options)
  - [API Key](#api-key)
  - [Region](#region)
  - [Timeout](#timeout)
  - [Retry](#retry)
  - [Per Page](#per-page)
  - [Webhook Secret](#webhook-secret)
- [Runtime Configuration](#runtime-configuration)
  - [Switching Regions](#switching-regions)
  - [Using a Different Token](#using-a-different-token)
  - [Reading Configuration at Runtime](#reading-configuration-at-runtime)
  - [Creating a Fresh Instance](#creating-a-fresh-instance)
- [Environment-Specific Configuration](#environment-specific-configuration)

## Introduction

Samsara reads its configuration from `config/samsara.php`, which is merged into the published file when you run `vendor:publish`. The service provider passes `api_key`, `timeout`, `retry`, and `per_page` into the singleton client, and applies the EU base URL when `region` is `eu`. This page documents every config key and the runtime overrides the client exposes.

## Publishing the Configuration

Publish the config file with the package's `samsara-config` tag:

```bash
php artisan vendor:publish --provider="Samsara\SamsaraServiceProvider"
```

This creates `config/samsara.php` in your application. Edit it directly, or override individual values from `.env`.

## Configuration Options

### API Key

Your Samsara API token. The service provider resolves the singleton client with this token, and every request authenticates with `Authorization: Bearer <token>`.

```php
// config/samsara.php
'api_key' => env('SAMSARA_API_KEY'),
```

```env
SAMSARA_API_KEY=samsara_api_xxxxxxxxxxxxxxxx
```

You may obtain a token from the Samsara dashboard under **Settings -> Organization -> API Tokens**.

### Region

The Samsara API region your fleet lives in. Use `us` for the US endpoint or `eu` for the EU endpoint. The service provider switches the client to the EU base URL when this value is `eu`; the default is `us`.

```php
// config/samsara.php
'region' => env('SAMSARA_REGION', 'us'),
```

```env
SAMSARA_REGION=eu
```

| Region | API Endpoint |
|--------|-------------|
| `us` | `https://api.samsara.com` |
| `eu` | `https://api.eu.samsara.com` |

### Timeout

Request timeout in seconds. Increase this if you regularly query large time windows on `vehicleStats()` or `safetyEvents()` and see `Illuminate\Http\Client\ConnectionException` from the Laravel HTTP client.

```php
// config/samsara.php
'timeout' => env('SAMSARA_TIMEOUT', 30),
```

```env
SAMSARA_TIMEOUT=60
```

### Retry

By default, the SDK retries failed requests up to three times before throwing an exception. Set this to `0` to disable retries entirely — useful in tests or when you want to surface network failures immediately to your application's exception handler.

```php
// config/samsara.php
'retry' => env('SAMSARA_RETRY', 3),
```

```env
SAMSARA_RETRY=5
```

### Per Page

The default page size for paginated requests. The query builder uses this value when you call `paginate()` or `lazy()` without an explicit `perPage`. Lower it if you stream large result sets and need finer control over memory; raise it to reduce round trips when you know the total fits in fewer pages.

```php
// config/samsara.php
'per_page' => env('SAMSARA_PER_PAGE', 100),
```

```env
SAMSARA_PER_PAGE=50
```

### Webhook Secret

The Base64-encoded secret used by `VerifyWebhookSignature` middleware to verify webhook payloads from Samsara. Leave it unset if you do not receive webhooks.

```php
// config/samsara.php
'webhook_secret' => env('SAMSARA_WEBHOOK_SECRET'),
```

```env
SAMSARA_WEBHOOK_SECRET=your-base64-encoded-secret
```

You may copy this secret from the Samsara dashboard when creating or viewing a webhook. See [resources/webhooks.md](resources/webhooks.md) for verification details.

## Runtime Configuration

### Switching Regions

If your application talks to fleets in both regions, you may switch endpoints at runtime:

```php
use Samsara\Facades\Samsara;

Samsara::useEuEndpoint();

// Subsequent calls hit api.eu.samsara.com.
$drivers = Samsara::drivers()->all();

Samsara::useUsEndpoint();
```

Both methods mutate the singleton client. Pair them with `withToken()` if each region has a different token.

### Using a Different Token

`withToken()` swaps the API token on the client. Subsequent calls authenticate with the new token until you swap it again or resolve a fresh instance:

```php
use Samsara\Facades\Samsara;

Samsara::withToken('different-api-token');

$drivers = Samsara::drivers()->all();
```

You may also chain it for a single call. Because the singleton client is mutated, the new token persists for later calls in the same request lifecycle:

```php
$drivers = Samsara::withToken('different-api-token')
    ->drivers()
    ->all();
```

### Reading Configuration at Runtime

The client exposes three read accessors useful for diagnostics and conditional logic:

- `Samsara::getConfig(string $key, mixed $default = null): mixed` — reads a single config value (`timeout`, `retry`, `per_page`) the client was constructed with.
- `Samsara::hasToken(): bool` — returns `true` if a token has been set, including via `withToken()`.
- `Samsara::getBaseUrl(): string` — returns the active base URL, switching with `useUsEndpoint()` and `useEuEndpoint()`.

```php
use Samsara\Facades\Samsara;

if (! Samsara::hasToken()) {
    abort(503, 'Samsara token is not configured.');
}

$timeout = Samsara::getConfig('timeout', 30);
$baseUrl = Samsara::getBaseUrl();
```

### Creating a Fresh Instance

`Samsara::make()` returns a brand-new client. Use it when you need an isolated instance — for example, in a multi-tenant application where each tenant holds its own token:

```php
use Samsara\Samsara;

$samsara = Samsara::make('api-token', [
    'timeout'  => 60,
    'retry'    => 5,
    'per_page' => 50,
]);

$samsara->useEuEndpoint();

$drivers = $samsara->drivers()->all();
```

The fresh instance does not affect the singleton resolved by the `Samsara` facade.

## Environment-Specific Configuration

You may tune the SDK per environment by setting the env vars in the matching `.env` file.

For local development, lower retries to surface failures fast:

```env
# .env.local
SAMSARA_API_KEY=samsara_api_dev_token
SAMSARA_TIMEOUT=60
SAMSARA_RETRY=1
```

For production, keep the defaults or raise the timeout for slower networks:

```env
# .env.production
SAMSARA_API_KEY=samsara_api_prod_token
SAMSARA_TIMEOUT=30
SAMSARA_RETRY=3
```

For testing, swap the live client out entirely with the fake:

```php
use Samsara\Facades\Samsara;

$fake = Samsara::fake();
```

See [testing.md](testing.md) for the full fake API.
