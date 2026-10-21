---
title: Getting Started
layout: default
nav_order: 2
description: Install Samsara, configure your API token, and make your first call.
permalink: /getting-started
---

# Getting Started

{: .warning }

> This page describes a fresh install. Existing v1.1.0 applications must complete the [v2 upgrade guide](upgrade-guide-v2.html) before changing the Composer constraint.

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Basic Usage](#basic-usage)
    - [Using the Facade](#using-the-facade)
    - [Using Dependency Injection](#using-dependency-injection)
    - [Creating a Fresh Instance](#creating-a-fresh-instance)
- [Querying Records](#querying-records)
- [Error Handling](#error-handling)
- [Testing](#testing)
- [Next Steps](#next-steps)

## Introduction

Samsara is a Laravel SDK for the Samsara Fleet Management API. You install the SDK with Composer, publish a single config file, drop your API token into `.env`, and then reach the API through the `Samsara` facade. The walk-through below covers each step and ends with a query that returns a typed `EntityCollection` from your fleet.

## Requirements

- PHP 8.2 or higher
- Laravel 12.x or 13.x
- A Samsara API token

## Installation

Install the SDK with Composer:

```bash
composer require erikgall/samsara
```

Laravel auto-discovers the package's service provider and the `Samsara` facade alias, so no further registration is required.

## Configuration

First, publish the configuration file:

```bash
php artisan vendor:publish --provider="Samsara\SamsaraServiceProvider"
```

This creates `config/samsara.php`.

Next, set your API token in `.env`:

```env
SAMSARA_API_KEY=your-api-token-here
```

If your fleet is on Samsara's EU or Canada infrastructure, also set the region:

```env
SAMSARA_REGION=eu
# or: SAMSARA_REGION=ca
```

Finally, if you receive webhooks from Samsara, add the Base64-encoded secret you copied from the Samsara dashboard:

```env
SAMSARA_WEBHOOK_SECRET=your-base64-encoded-secret
```

See [configuration.md](configuration.html) for the full list of options and [resources/webhooks.md](resources/webhooks.html) for verifying webhook signatures.

## Basic Usage

### Using the Facade

The `Samsara` facade resolves the lifecycle-scoped client registered by the service provider. Each resource accessor returns a resource object you can call directly or compose with the query builder.

```php
use Samsara\Facades\Samsara;

$drivers = Samsara::drivers()->query()->get();

$vehicle = Samsara::vehicles()->find('vehicle-id');

$stats = Samsara::vehicleStats()
    ->current()
    ->types(['gps', 'engineStates'])
    ->get();
```

The `vehicleStats()` accessor returns a resource, not a builder. Chain through `current()`, `history()`, `feed()`, `gps()`, `engineStates()`, `fuelPercents()`, or `odometer()` before calling builder methods like `types()`, `between()`, or `get()`.

### Using Dependency Injection

You may inject the `Samsara\Samsara` client directly. Laravel resolves one client per application lifecycle, including an HTTP request or queue/Octane scope:

```php
use Samsara\Samsara;

class FleetController extends Controller
{
    public function __construct(
        protected Samsara $samsara,
    ) {}

    public function index()
    {
        return $this->samsara->drivers()->query()->get();
    }
}
```

### Creating a Fresh Instance

For ad-hoc clients—for example, multi-tenant applications that hold a token per tenant—call `Samsara::make()`. The fresh client inherits the package's configured region and request settings:

```php
use Samsara\Samsara;

$samsara = Samsara::make('your-api-token');

$drivers = $samsara->drivers()->query()->get();
```

You may also pass per-instance config overrides:

```php
$samsara = Samsara::make('your-api-token', [
    'region'   => 'eu',
    'timeout'  => 60,
    'attempts' => 5,
    'per_page' => 50,
]);
```

## Querying Records

Most resources expose a `query()` method that returns the fluent query builder. The builder supports filtering, pagination, cursor pagination, and lazy iteration:

```php
$drivers = Samsara::drivers()
    ->query()
    ->whereTag('tag-123')
    ->get();

$vehicles = Samsara::vehicles()
    ->query()
    ->limit(50)
    ->paginate();

Samsara::vehicleStats()
    ->history()
    ->types(['gps'])
    ->between($start, $end)
    ->lazy()
    ->each(function ($stat) {
        // Stream one record at a time without loading the full result set.
    });
```

See [query-builder.md](query-builder.html) for the full method reference, including cursor pagination, working with `EntityCollection`, and the underlying HTTP client escape hatch. For the full list of accessors on the facade, see [resources/index.md](resources/index.html).

## Error Handling

Supported generic resource operations, custom helpers, and query-builder terminal methods map HTTP failures to typed SDK exceptions. Unsupported CRUD methods are absent; explicit unsupported helpers can still throw `UnsupportedOperationException`.

```php
use Samsara\Exceptions\AuthenticationException;
use Samsara\Exceptions\RateLimitException;

try {
    $drivers = Samsara::drivers()->query()->get();
} catch (AuthenticationException $e) {
    // 401: invalid or revoked token.
} catch (RateLimitException $e) {
    $retryAfter = $e->getRetryAfter();
}

$driver = Samsara::drivers()->find('driver-id');
// find() returns null for a 404.
```

See [error-handling.md](error-handling.html) for the full exception hierarchy and how to register handlers in Laravel 12's `bootstrap/app.php`.

## Testing

Use `SamsaraFake` to intercept calls in tests. The fake records every request and returns canned responses:

```php
use Samsara\Facades\Samsara;

#[\PHPUnit\Framework\Attributes\Test]
public function it_lists_drivers(): void
{
    $fake = Samsara::fake();

    $fake->fakeResponse('/fleet/drivers', [
        ['id' => 'driver-1', 'name' => 'John Doe'],
        ['id' => 'driver-2', 'name' => 'Jane Smith'],
    ]);

    $drivers = $fake->drivers()->query()->get();

    $this->assertCount(2, $drivers);
    $this->assertSame('John Doe', $drivers[0]->name);
}
```

See [testing.md](testing.html) for the full fake API and `HttpFactory` patterns.

## Next Steps

- [Configuration](configuration.html) — every option and runtime override.
- [Query Builder](query-builder.html) — filters, pagination, and lazy collections.
- [Testing](testing.html) — testing with `SamsaraFake` and the HTTP factory.
- [Error Handling](error-handling.html) — exception hierarchy and handler registration.
