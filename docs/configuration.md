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
    - [Request Attempts](#request-attempts)
    - [Retry Writes](#retry-writes)
    - [Maximum Retry-After Delay](#maximum-retry-after-delay)
    - [Per Page](#per-page)
    - [Webhook Secret](#webhook-secret)
- [Runtime Configuration](#runtime-configuration)
    - [Switching Regions](#switching-regions)
    - [Using a Custom Base URL](#using-a-custom-base-url)
    - [Using a Different Token](#using-a-different-token)
    - [Reading Configuration at Runtime](#reading-configuration-at-runtime)
    - [Creating a Fresh Instance](#creating-a-fresh-instance)
- [Environment-Specific Configuration](#environment-specific-configuration)

## Introduction

Samsara reads `config/samsara.php`; the service provider fills any key your published file omits from the package defaults. The service provider passes `api_key`, `timeout`, `attempts`, `retry_writes`, `retry_after_max_seconds`, and `per_page` into the lifecycle-scoped client and applies the selected US, EU, or Canada base URL. The reference below covers every config key and the runtime overrides the client exposes for switching regions, swapping tokens, and inspecting the active configuration.

## Publishing the Configuration

Publish the config file with the package's `samsara-config` tag:

```bash
php artisan vendor:publish --tag=samsara-config
```

This creates `config/samsara.php` in your application. Edit it directly, or override individual values from `.env`.

## Configuration Options

### API Key

Your Samsara API token. The service provider resolves the lifecycle-scoped client with this token. A nonempty token authenticates requests with `Authorization: Bearer <token>`. An unset or blank `SAMSARA_API_KEY=` means no token, so requests omit the `Authorization` header.

```php
// config/samsara.php
'api_key' => env('SAMSARA_API_KEY'),
```

```env
SAMSARA_API_KEY=samsara_api_xxxxxxxxxxxxxxxx
```

You may obtain a token from the Samsara dashboard under **Settings -> Organization -> API Tokens**.

### Region

The Samsara API region your fleet lives in. Use `us`, `eu`, or `ca`. An unset or blank `SAMSARA_REGION` defaults to `us`; unknown nonempty values throw `InvalidArgumentException`.

```php
// config/samsara.php
'region' => env('SAMSARA_REGION', 'us'),
```

```env
SAMSARA_REGION=eu
```

| Region | API Endpoint                 |
| ------ | ---------------------------- |
| `us`   | `https://api.samsara.com`    |
| `eu`   | `https://api.eu.samsara.com` |
| `ca`   | `https://api.ca.samsara.com` |

Choose a region on a copy with `withRegion('us')`, `withRegion('eu')`, or `withRegion('ca')`.

### Timeout

Request timeout in seconds. Increase this if you regularly query large time windows on `vehicleStats()` or `safetyEvents()` and see `Illuminate\Http\Client\ConnectionException` from the Laravel HTTP client.

```php
// config/samsara.php
'timeout' => env('SAMSARA_TIMEOUT', 30),
```

```env
SAMSARA_TIMEOUT=60
```

### Request Attempts

This value is the maximum number of request attempts, including the initial request. The default of `3` allows up to two retries after the initial request. Set it to `1` to make a single request without retries — useful in tests or when you want to surface network failures immediately to your application's exception handler. Values below `1` are invalid and throw `InvalidArgumentException` when the client is constructed.

```php
// config/samsara.php
'attempts' => env('SAMSARA_ATTEMPTS', 3),
```

```env
SAMSARA_ATTEMPTS=5
```

### Retry Writes

Writes (POST, PUT, PATCH, DELETE) do not retry connection failures, HTTP 408, or 5xx by default. Opt in only when replaying an accepted write is safe. HTTP 429 remains retryable for every method, subject to `attempts` and the Retry-After ceiling.

```php
// config/samsara.php
'retry_writes' => env('SAMSARA_RETRY_WRITES', false),
```

```env
SAMSARA_RETRY_WRITES=false
```

### Maximum Retry-After Delay

The default ceiling of 30 seconds is the largest `Retry-After` delay the SDK honors synchronously. A 429 response above this ceiling throws `RateLimitException` without another request so your application can reschedule the work. Only nonnegative integer seconds or an HTTP-date in the `Wed, 21 Oct 2026 13:00:00 GMT` form (RFC 850 and asctime forms are ignored) are accepted. Other values are ignored and use the normal retry delay; valid delays within the ceiling are honored.

```php
// config/samsara.php
'retry_after_max_seconds' => env('SAMSARA_RETRY_AFTER_MAX_SECONDS', 30),
```

```env
SAMSARA_RETRY_AFTER_MAX_SECONDS=30
```

### Per Page

This value supplies the implicit `limit` for `paginate()` and `lazy()` when the caller did not set a limit. Explicit `paginate($perPage)`, `lazy($chunkSize)`, `limit()`, and `where('limit', ...)` values take precedence. Builders for official cursor endpoints that do not accept `limit` opt out automatically with `withoutDefaultPageSize()`; do not add an explicit limit to those endpoints.

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

$euClient = Samsara::withRegion('eu');
$drivers = $euClient->drivers()->query()->get();

$usClient = Samsara::withRegion('us');
$canadaClient = Samsara::withRegion('ca');
```

Region selection returns a copy and clears its resource cache. Retain or chain that copy; the scoped client keeps its original region.

### Using a Custom Base URL

`withBaseUrl()` supports a compatible proxy, test server, or gateway while preserving the SDK's normal request configuration. A trailing slash is removed automatically:

```php
$proxyClient = Samsara::withBaseUrl('https://samsara-proxy.example.test/');
$drivers = $proxyClient->drivers()->query()->get();
```

Like `withRegion()`, this returns a copy. `getBaseUrl()` returns the active value. Constructing a client with `null` as its token omits the `Authorization` header, which is useful for a proxy that owns authentication.

### Using a Different Token

`withToken()` returns an isolated clone with an empty resource cache. It does not mutate the container's scoped client, so retain or chain the returned instance:

```php
$tenantClient = Samsara::withToken('different-api-token');
$drivers = $tenantClient->drivers()->query()->get();
```

You may also chain it for a single call:

```php
$drivers = Samsara::withToken('different-api-token')
    ->drivers()
    ->query()->get();
```

`withoutToken()` returns the same kind of isolated clone with no `Authorization` header. Laravel registers the default client as a scoped service, so Octane requests and queue-job lifecycles do not retain a previous lifecycle's instance.

### Reading Configuration at Runtime

The client exposes three read accessors useful for diagnostics and conditional logic:

- `Samsara::getConfig(string $key, mixed $default = null): mixed` — reads a single config value (`timeout`, `attempts`, `retry_writes`, `retry_after_max_seconds`, `per_page`) the client was constructed with.
- `Samsara::hasToken(): bool` — returns `true` if a token has been set, including via `withToken()`.
- `Samsara::getBaseUrl(): string` — returns the active base URL, selected by configuration, `withRegion()`, or `withBaseUrl()`.

```php
if (! Samsara::hasToken()) {
    abort(503, 'Samsara token is not configured.');
}

$timeout = Samsara::getConfig('timeout', 30);
$baseUrl = Samsara::getBaseUrl();
```

### Creating a Fresh Instance

`Samsara::make()` returns a brand-new client. It inherits the configured package region and request settings unless an override is supplied. Use it when you need an isolated instance—for example, in a multi-tenant application where each tenant holds its own token:

```php
use Samsara\Samsara;

$samsara = Samsara::make('api-token', [
    'region'   => 'eu',
    'timeout'  => 60,
    'attempts' => 5,
    'retry_after_max_seconds' => 30,
    'per_page' => 50,
]);

$drivers = $samsara->drivers()->query()->get();
```

The fresh instance does not affect the scoped client resolved by the `Samsara` facade.

### Multi-tenant Applications

Bind the authenticated tenant's isolated client at middleware entry so downstream dependency injection and the facade resolve the same scoped object:

```php
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Samsara\Samsara;
use Samsara\Facades\Samsara as SamsaraFacade;

public function handle(Request $request, Closure $next): Response
{
    $tenant = $request->user()->tenant;

    $client = Samsara::make(
        $tenant->samsara_api_token,
        ['region' => $tenant->samsara_region],
    );

    SamsaraFacade::swap($client);

    return $next($request);
}
```

For queue jobs, construct from credentials and region carried by or reloaded for that job. Never depend on credentials left by an earlier job:

```php
public function handle(): void
{
    $samsara = Samsara::make($this->apiToken, ['region' => $this->region]);
    $samsara->drivers()->query()->get();
}
```

Container and `make()` clients use Laravel's shared HTTP factory when available. Standalone callers can pass a third constructor factory or use `setHttpFactory()` as an explicit test seam.

## Environment-Specific Configuration

You may tune the SDK per environment by setting the env vars in the matching `.env` file.

For local development, lower the attempt count to surface failures fast:

```env
# .env.local
SAMSARA_API_KEY=samsara_api_dev_token
SAMSARA_TIMEOUT=60
SAMSARA_ATTEMPTS=1
```

For production, keep the defaults or raise the timeout for slower networks:

```env
# .env.production
SAMSARA_API_KEY=samsara_api_prod_token
SAMSARA_TIMEOUT=30
SAMSARA_ATTEMPTS=3
```

For testing, replace the facade root and both container bindings with a fake client:

```php
$fake = Samsara::fake();
```

`Samsara::fake()` returns the `SamsaraFake` and swaps it into the facade, the `Samsara\Samsara` binding, and the `samsara` alias. Both `$fake->drivers()->query()->get()` and later `Samsara::drivers()->query()->get()` calls use the same fake.

See [testing.md](testing.html) for the full fake API.
