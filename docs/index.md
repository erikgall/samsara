---
title: Home
layout: home
nav_order: 1
description: "A comprehensive Laravel SDK for the Samsara Fleet Management API"
permalink: /
---

# Samsara SDK for Laravel
{: .fs-9 }

A comprehensive Laravel SDK for the Samsara Fleet Management API with fluent query builder, type-safe entities, and full API coverage.
{: .fs-6 .fw-300 }

[Get Started](/samsara/getting-started){: .btn .btn-primary .fs-5 .mb-4 .mb-md-0 .mr-2 }
[View on GitHub](https://github.com/erikgall/samsara){: .btn .fs-5 .mb-4 .mb-md-0 }

---

## Features

- **40+ Resource Endpoints** - Full coverage of Samsara's API including Fleet, Telematics, Safety, Dispatch, Industrial, and more
- **Fluent Query Builder** - Filter, paginate, and stream data with an intuitive query interface
- **Type-Safe Entities** - All API responses are mapped to strongly-typed entity classes
- **Cursor Pagination** - Built-in support for Samsara's cursor-based pagination
- **Lazy Collections** - Memory-efficient streaming for large datasets
- **Testing Support** - `SamsaraFake` class for easy mocking in tests

---

## Quick Example

```php
use Samsara\Facades\Samsara;

// Get all drivers
$drivers = Samsara::drivers()->all();

// Find a specific vehicle
$vehicle = Samsara::vehicles()->find('vehicle-id');

// Query with filters
$stats = Samsara::vehicleStats()
    ->current()
    ->whereVehicle(['vehicle-1', 'vehicle-2'])
    ->types(['gps', 'engineStates'])
    ->get();
```

---

## Documentation

### Getting Started

- [Getting Started](/samsara/getting-started) - Installation and quick start guide
- [Configuration](/samsara/configuration) - All configuration options

### Guides

- [Query Builder](/samsara/query-builder) - Fluent filtering, pagination, and data retrieval
- [Error Handling](/samsara/error-handling) - Exception handling and retry logic
- [Testing](/samsara/testing) - Testing with SamsaraFake and fixtures

### Resources

See the [Resources](/samsara/resources/) section for detailed documentation on each API resource:

| Category | Resources |
|:---------|:----------|
| **Fleet** | [Drivers](/samsara/resources/drivers), [Vehicles](/samsara/resources/vehicles), [Trailers](/samsara/resources/trailers), [Equipment](/samsara/resources/equipment) |
| **Telematics** | [Vehicle Stats](/samsara/resources/vehicle-stats), [Vehicle Locations](/samsara/resources/vehicle-locations), [Trips](/samsara/resources/trips) |
| **Safety** | [Hours of Service](/samsara/resources/hours-of-service), [Maintenance](/samsara/resources/maintenance), [Safety Events](/samsara/resources/safety-events) |
| **Dispatch** | [Routes](/samsara/resources/routes), [Addresses](/samsara/resources/addresses) |
| **Organization** | [Users](/samsara/resources/users), [Tags](/samsara/resources/tags), [Contacts](/samsara/resources/contacts) |

---

## Requirements

- PHP 8.1 or higher
- Laravel 10.x, 11.x, or 12.x
- Samsara API token

---

## License

This package is open-sourced software licensed under the [MIT license](https://github.com/erikgall/samsara/blob/main/LICENSE).
