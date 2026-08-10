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

Samsara reads its configuration from `config/samsara.php`, which is merged into the published file when you run `vendor:publish`. The service provider passes `api_key`, `timeout`, `retry`, and `per_page` into the singleton client and applies the selected US, EU, or Canada base URL. The reference below covers every config key and the runtime overrides the client exposes for switching regions, swapping tokens, and inspecting the active configuration.

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

The Samsara API region your fleet lives in. Use `us`, `eu`, or `ca`. The default is `us`; unknown values also fall back to the US endpoint.

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
| `ca` | `https://api.ca.samsara.com` |

The same three endpoints are available at runtime through `useUsEndpoint()`, `useEuEndpoint()`, and `useCanadaEndpoint()`.

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

This value supplies the implicit `limit` for `paginate()` and `lazy()` when the caller did not set a limit. Explicit `paginate($perPage)`, `lazy($chunkSize)`, `limit()`, and `where('limit', ...)` values take precedence. Builders for official cursor endpoints that do not accept `limit` opt out automatically with `withoutDefaultPageSize()`.

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

You may copy this secret from the Samsara dashboard when creating or viewing a webhook. See [resources/webhooks.md](resources/webhooks.html) for verification details.

## Runtime Configuration

### Switching Regions

If your application talks to fleets in more than one region, you may switch endpoints at runtime:

```php
use Samsara\Facades\Samsara;

Samsara::useEuEndpoint();

// Subsequent calls hit api.eu.samsara.com.
$drivers = Samsara::drivers()->all();

Samsara::useUsEndpoint();

Samsara::useCanadaEndpoint();
```

Both methods mutate the singleton client. Pair them with `withToken()` if each region has a different token.

### Using a Different Token

`withToken()` swaps the API token on the client. Subsequent calls authenticate with the new token until you swap it again or resolve a fresh instance:

```php
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
- `Samsara::getBaseUrl(): string` — returns the active base URL, switching with `useUsEndpoint()`, `useEuEndpoint()`, and `useCanadaEndpoint()`.

```php
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

For testing, replace the facade root and both container bindings with a fake client:

```php
$fake = Samsara::fake();
```

`Samsara::fake()` returns the `SamsaraFake` and swaps it into the facade, the `Samsara\Samsara` binding, and the `samsara` alias. Both `$fake->drivers()->all()` and later `Samsara::drivers()->all()` calls use the same fake.

See [testing.md](testing.html) for the full fake API.
