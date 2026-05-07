---
title: Home
layout: home
nav_order: 1
description: Laravel SDK for the Samsara Fleet Management API.
permalink: /
---

# Samsara SDK for Laravel
{: .fs-9 }

A Laravel SDK for the Samsara Fleet Management API.
{: .fs-6 .fw-300 }

[Get Started](/samsara/getting-started){: .btn .btn-primary .fs-5 .mb-4 .mb-md-0 .mr-2 }
[View on GitHub](https://github.com/erikgall/samsara){: .btn .fs-5 .mb-4 .mb-md-0 }

- [Introduction](#introduction)
- [Installation](#installation)
- [Configuration](#configuration)
- [Your First Query](#your-first-query)
- [Where to Next](#where-to-next)
- [Requirements](#requirements)
- [License](#license)

## Introduction

Samsara is a Laravel SDK for the Samsara Fleet Management API. You reach for it when you need to read or mutate fleet data — drivers, vehicles, telematics, hours of service, safety events — from a Laravel application without hand-rolling HTTP calls. The SDK exposes resources through a `Samsara` facade, returns typed entities, and provides a fluent query builder for filtering, paginating, and streaming results.

## Installation

Install the SDK with Composer:

```bash
composer require erikgall/samsara
```

Laravel auto-discovers the package's service provider and the `Samsara` facade alias, so no manual registration is required.

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --provider="Samsara\SamsaraServiceProvider"
```

Then add your API token to `.env`:

```env
SAMSARA_API_KEY=your-api-token-here
```

For EU customers, set `SAMSARA_REGION=eu`. See [configuration.md](configuration.md) for every option, including timeout, retry, default page size, and the webhook secret.

## Your First Query

```php
use Samsara\Facades\Samsara;

$drivers = Samsara::drivers()->all();

foreach ($drivers as $driver) {
    logger()->info("{$driver->name} ({$driver->id})");
}
```

Every resource accessor on the facade returns a resource object. Resources expose verbs like `all()`, `find()`, and `query()`, plus resource-specific helpers. The fluent query builder layers on filters, time windows, pagination, and lazy iteration:

```php
$stats = Samsara::vehicleStats()
    ->current()
    ->whereVehicle(['vehicle-1', 'vehicle-2'])
    ->types(['gps', 'engineStates'])
    ->get();
```

## Where to Next

- [Getting Started](getting-started.md) — install, configure, and walk through dependency injection.
- [Configuration](configuration.md) — every config key, env var, and runtime override.
- [Query Builder](query-builder.md) — filtering, pagination, lazy collections, and the underlying HTTP client.
- [Error Handling](error-handling.md) — exception hierarchy and Laravel 12 exception handler integration.
- [Testing](testing.md) — `SamsaraFake` and HTTP-level fakes.
- [Resources](resources/index.md) — every resource the SDK exposes.

## Requirements

- PHP 8.2 or higher
- Laravel 12.x or 13.x
- A Samsara API token

## License

The SDK is open-sourced software licensed under the [MIT license](https://github.com/erikgall/samsara/blob/main/LICENSE).
