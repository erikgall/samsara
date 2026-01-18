# Samsara SDK - Complete Rewrite Plan

## Overview

Complete rewrite of the Samsara PHP SDK with a fluent, Laravel-style API using first-party Laravel packages (HTTP Client) instead of Saloon. The goal is a simple, intuitive, developer-friendly SDK with complete coverage of the Samsara API (197 endpoints across 30+ categories).

## Design Principles

1. **Fluent API** - Laravel-style chainable methods
2. **No readonly/final classes** - Extensible and mockable
3. **Laravel HTTP Client** - First-party `illuminate/http`, not Saloon
4. **Developer Experience First** - Simple, intuitive, well-documented
5. **Complete API Coverage** - All 197 endpoints

---

## Architecture

### Directory Structure

```
src/
├── Samsara.php                    # Main SDK client
├── SamsaraServiceProvider.php     # Laravel integration
├── Facades/
│   └── Samsara.php               # Facade
├── Concerns/                      # Shared traits
├── Contracts/                     # Interfaces
├── Data/                          # DTOs by domain
│   ├── Entity.php                # Base entity
│   ├── EntityCollection.php
│   ├── Address/
│   ├── Driver/
│   ├── Vehicle/
│   └── ... (organized by domain)
├── Enums/                         # PHP 8.1 backed enums
├── Exceptions/                    # Exception hierarchy
├── Query/
│   ├── Builder.php               # Fluent query builder
│   └── Pagination/
├── Resources/                     # API resources by domain
│   ├── Resource.php              # Base resource
│   ├── Fleet/
│   ├── Telematics/
│   ├── Safety/
│   ├── Dispatch/
│   ├── Organization/
│   ├── Industrial/
│   └── Integrations/
├── Support/
└── Testing/
    ├── SamsaraFake.php
    └── Fixtures/
```

---

## Core Components

### 1. Main Client (`Samsara.php`)

```php
<?php

namespace ErikGall\Samsara;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;

class Samsara
{
    protected string $baseUrl = 'https://api.samsara.com';
    protected ?string $token = null;
    protected HttpFactory $http;
    protected array $config = [];
    protected array $resources = [];

    public function __construct(?string $token = null, array $config = [])
    {
        $this->token = $token;
        $this->config = $config;
        $this->http = new HttpFactory();
    }

    public static function make(?string $token = null, array $config = []): static
    {
        return new static($token, $config);
    }

    public function withToken(string $token): static
    {
        $this->token = $token;
        return $this;
    }

    public function useEuEndpoint(): static
    {
        $this->baseUrl = 'https://api.eu.samsara.com';
        return $this;
    }

    public function client(): PendingRequest
    {
        return $this->http->baseUrl($this->baseUrl)
            ->withToken($this->token)
            ->acceptJson()
            ->asJson()
            ->timeout($this->config['timeout'] ?? 30)
            ->retry($this->config['retry'] ?? 3, 100);
    }

    public function drivers(): DriversResource
    {
        return $this->resource(DriversResource::class);
    }

    public function vehicles(): VehiclesResource
    {
        return $this->resource(VehiclesResource::class);
    }

    // ... more resource accessors
}
```

### 2. Query Builder (`Query/Builder.php`)

```php
<?php

namespace ErikGall\Samsara\Query;

class Builder
{
    protected Resource $resource;
    protected array $filters = [];
    protected ?int $limit = null;
    protected ?string $cursor = null;

    // Fluent filtering - all return static for proper inheritance
    public function whereTag(array|string $tagIds): static
    public function whereDriver(array|string $driverIds): static
    public function whereVehicle(array|string $vehicleIds): static
    public function updatedAfter(DateTimeInterface|string $time): static
    public function between(DateTimeInterface|string $start, DateTimeInterface|string $end): static
    public function types(array|string $types): static
    public function limit(int $limit): static
    public function after(string $cursor): static

    // Execution
    public function get(): EntityCollection
    public function paginate(?int $perPage = null): CursorPaginator
    public function lazy(?int $chunkSize = null): LazyCollection
    public function first(): ?object
}
```

### 3. Base Resource (`Resources/Resource.php`)

```php
<?php

namespace ErikGall\Samsara\Resources;

abstract class Resource
{
    protected Samsara $samsara;
    protected string $endpoint;
    protected string $entity;

    public function query(): Builder
    public function all(): EntityCollection
    public function find(string $id): ?object
    public function create(array $data): object
    public function update(string $id, array $data): object
    public function delete(string $id): bool
}
```

### 4. Entity/DTO (`Data/Entity.php`)

Extends Laravel's `Illuminate\Support\Fluent` class for built-in array/object handling:

```php
<?php

namespace ErikGall\Samsara\Data;

use Illuminate\Support\Fluent;

class Entity extends Fluent
{
    // Fluent provides: ArrayAccess, Arrayable, Jsonable, JsonSerializable
    // Plus magic __get/__set for property access

    public static function make(array $attributes = []): static
    {
        return new static($attributes);
    }

    // Override to return static for proper type hints in child classes
    public function fill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            $this->offsetSet($key, $value);
        }
        return $this;
    }
}
```

**Why Fluent?**
- Built-in `ArrayAccess`, `Arrayable`, `Jsonable`, `JsonSerializable`
- Magic `__get`/`__set` for property access (`$entity->name`)
- No custom casting logic needed - Laravel handles it
- Methods returning `$this` use `static` return type for proper inheritance

---

## API Categories & Resources

| Category | Resource Class | Key Endpoints |
|----------|---------------|---------------|
| **Fleet** | | |
| Drivers | `DriversResource` | `/fleet/drivers` |
| Vehicles | `VehiclesResource` | `/fleet/vehicles` |
| Trailers | `TrailersResource` | `/fleet/trailers` |
| Equipment | `EquipmentResource` | `/fleet/equipment` |
| **Telematics** | | |
| Vehicle Stats | `VehicleStatsResource` | `/fleet/vehicles/stats` |
| Vehicle Locations | `VehicleLocationsResource` | `/fleet/vehicles/locations` |
| Trips | `TripsResource` | `/trips/stream` |
| **Safety & Compliance** | | |
| Hours of Service | `HoursOfServiceResource` | `/fleet/hos/*` |
| Maintenance | `MaintenanceResource` | `/dvirs/*`, `/defects/*` |
| Safety Events | `SafetyResource` | `/safety-events` |
| Coaching | `CoachingResource` | `/coaching/*` |
| **Dispatch** | | |
| Routes | `RoutesResource` | `/fleet/routes` |
| Addresses | `AddressesResource` | `/addresses` |
| Documents | `DocumentsResource` | `/fleet/documents` |
| **Organization** | | |
| Users | `UsersResource` | `/users` |
| Contacts | `ContactsResource` | `/contacts` |
| Tags | `TagsResource` | `/tags` |
| Attributes | `AttributesResource` | `/attributes` |
| **Industrial** | | |
| Industrial | `IndustrialResource` | `/industrial/*` |
| Sensors | `SensorsResource` | `/v1/sensors/*` |
| Assets | `AssetsResource` | `/assets` |
| **Integrations** | | |
| Webhooks | `WebhooksResource` | `/webhooks` |
| Gateways | `GatewaysResource` | `/gateways` |
| Live Sharing | `LiveSharingLinksResource` | `/live-shares` |

---

## Fluent API Usage Examples

```php
// Create client
$samsara = Samsara::make('your-api-token');

// Get all active drivers
$drivers = $samsara->drivers()->active()->get();

// Query with fluent filters
$drivers = $samsara->drivers()
    ->query()
    ->whereTag(['tag-1', 'tag-2'])
    ->updatedAfter(now()->subDay())
    ->limit(50)
    ->get();

// Pagination
$paginator = $samsara->drivers()
    ->query()
    ->paginate(100);

foreach ($paginator as $page) {
    foreach ($page as $driver) {
        echo $driver->name;
    }
}

// Lazy streaming (memory efficient for large datasets)
$samsara->drivers()
    ->query()
    ->lazy(100)
    ->each(function (Driver $driver) {
        // Process each driver
    });

// Vehicle stats with specific types
$stats = $samsara->vehicleStats()
    ->current()
    ->whereVehicle(['v1', 'v2'])
    ->withGps()
    ->withEngineStates()
    ->get();

// Historical data with time range
$history = $samsara->vehicleStats()
    ->history()
    ->between(now()->subWeek(), now())
    ->types(['gps', 'fuelPercents'])
    ->lazy();

// CRUD operations
$driver = $samsara->drivers()->create([
    'name' => 'John Doe',
    'phone' => '+1234567890',
]);

$samsara->drivers()->update($driver->id, [
    'phone' => '+0987654321',
]);

$samsara->drivers()->delete($driver->id);

// Using Laravel Facade
use ErikGall\Samsara\Facades\Samsara;

$vehicles = Samsara::vehicles()->all();
$stats = Samsara::vehicleStats()->gps()->get();

// Testing with fake
Samsara::fake()->fakeDrivers([
    ['id' => '1', 'name' => 'John Doe'],
]);

$drivers = Samsara::drivers()->all();
Samsara::assertRequested('/fleet/drivers');
```

---

## Laravel Integration

### Service Provider

```php
// Automatic registration via package discovery
// Or manual registration in config/app.php

$this->app->singleton(Samsara::class, function ($app) {
    $config = $app['config']['samsara'];

    $samsara = new Samsara($config['api_key'], $config);

    if ($config['region'] === 'eu') {
        $samsara->useEuEndpoint();
    }

    return $samsara;
});
```

### Configuration (`config/samsara.php`)

```php
return [
    'api_key' => env('SAMSARA_API_KEY'),
    'region' => env('SAMSARA_REGION', 'us'), // 'us' or 'eu'
    'timeout' => env('SAMSARA_TIMEOUT', 30),
    'retry' => env('SAMSARA_RETRY', 3),
    'per_page' => env('SAMSARA_PER_PAGE', 100),
];
```

### Facade

```php
use ErikGall\Samsara\Facades\Samsara;

// Static access
$drivers = Samsara::drivers()->all();

// Testing
Samsara::fake();
```

---

## Exception Hierarchy

```
SamsaraException (base)
├── AuthenticationException (401)
├── AuthorizationException (403)
├── NotFoundException (404)
├── ValidationException (422) - includes $errors array
├── RateLimitException (429) - includes $retryAfter
├── ServerException (5xx)
└── ConnectionException
```

---

## Testing Support

```php
// Fake for testing
$fake = Samsara::fake();

$fake->fakeResponse('/fleet/drivers', [
    ['id' => '1', 'name' => 'John Doe'],
    ['id' => '2', 'name' => 'Jane Doe'],
]);

// Or use convenience methods
$fake->fakeDrivers([...]);
$fake->fakeVehicles([...]);
$fake->fakeVehicleStats([...]);

// Make assertions
$fake->assertRequested('/fleet/drivers');
$fake->assertRequestedWithParams('/fleet/drivers', ['tagIds' => '123']);
$fake->assertNothingRequested();
```

---

## Implementation Phases

| Phase | Focus | Description |
|-------|-------|-------------|
| 1 | Core Infrastructure | Samsara client, HTTP, ServiceProvider, Facade |
| 2 | Exceptions | Exception hierarchy |
| 3 | Base Components | Entity, Resource, Contracts, Traits |
| 4 | Query Builder | Fluent builder, pagination, lazy collections |
| 5 | Enums | PHP 8.1 backed enums for types |
| 6 | Entities | DTOs for all API domains |
| 7 | Resources | All resource classes by domain |
| 8 | Beta/Legacy | Beta, Preview, and Legacy API support |
| 9 | Testing | SamsaraFake, fixtures, unit/feature tests |
| 10 | Refactoring & QA | Code review, refactoring, static analysis, code style |
| 11 | Documentation | Human docs, API reference, AI-friendly llms.txt |
| 12 | Release | Final polish, versioning, changelog, publish |

---

## Phase 10: Refactoring & Quality Assurance

After initial implementation, dedicated phase for code quality:

- **Code Review** - Review all code for consistency, patterns, edge cases
- **Refactoring** - Extract common patterns, reduce duplication, improve naming
- **Static Analysis** - PHPStan level 8, fix all issues
- **Code Style** - Laravel Pint, consistent formatting
- **Performance** - Profile and optimize hot paths
- **Security Audit** - Review for injection, data leaks, auth issues
- **Dependency Audit** - Review and update dependencies

---

## Phase 11: Documentation

### Human Documentation
- **README.md** - Quick start, installation, basic usage
- **docs/getting-started.md** - Full setup guide
- **docs/configuration.md** - All config options explained
- **docs/resources/*.md** - Per-resource documentation with examples
- **docs/testing.md** - How to test with SamsaraFake
- **docs/upgrading.md** - Migration guide from previous versions

### API Reference
- **PHPDoc** - Complete inline documentation on all public methods
- **Generated API docs** - Using phpDocumentor or similar

### AI-Friendly Documentation (Laravel Boost)

Following [Laravel's AI Package Guidelines](https://laravel.com/docs/12.x/ai#package-guidelines) for Boost compatibility:

**Directory Structure:**
```
resources/
└── boost/
    └── guidelines/
        ├── core.blade.php          # Main SDK overview & quick start
        ├── resources.blade.php     # All resources with methods
        ├── query-builder.blade.php # Query builder reference
        ├── entities.blade.php      # DTOs and data structures
        └── testing.blade.php       # Testing with SamsaraFake
```

**Format (Blade templates with code snippets):**
```blade
## Samsara SDK

This package provides a fluent Laravel SDK for the Samsara Fleet Management API.

### Features

- Fluent Query Builder: Filter, paginate, and stream API results. Example:

@verbatim
<code-snippet name="Query drivers by tag" lang="php">
$drivers = Samsara::drivers()
    ->query()
    ->whereTag(['fleet-a', 'fleet-b'])
    ->limit(50)
    ->get();
</code-snippet>
@endverbatim

- Resource Access: 40+ resources covering 197 API endpoints. Example:

@verbatim
<code-snippet name="Get vehicle stats" lang="php">
$stats = Samsara::vehicleStats()
    ->current()
    ->withGps()
    ->withEngineStates()
    ->get();
</code-snippet>
@endverbatim
```

**Benefits:**
- Automatic discovery when users run `boost:install`
- AI agents understand SDK patterns and best practices
- Proper code generation for Samsara API integration

---

## Key Dependencies

```json
{
    "require": {
        "php": "^8.1",
        "illuminate/http": "^10.0|^11.0",
        "illuminate/support": "^10.0|^11.0",
        "illuminate/collections": "^10.0|^11.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "larastan/larastan": "^2.0",
        "laravel/pint": "^1.0"
    }
}
```

---

## Files to Modify/Create

### Core (Must do first)
- `src/Samsara.php` - Replace Saloon-based connector
- `src/Resources/Resource.php` - New base resource
- `src/Query/Builder.php` - Fluent query builder
- `src/Data/Entity.php` - Enhanced entity with casts
- `src/Exceptions/*.php` - Exception hierarchy
- `src/Facades/Samsara.php` - Laravel facade
- `src/SamsaraServiceProvider.php` - Updated provider
- `config/samsara.php` - Configuration file

### Per-Domain (Incremental)
- `src/Resources/{Domain}/*Resource.php` - Resource classes
- `src/Data/{Domain}/*.php` - Entity classes
- `src/Enums/*.php` - Enum classes

### Testing
- `src/Testing/SamsaraFake.php` - Test fake
- `tests/Unit/*.php` - Unit tests
- `tests/Feature/*.php` - Feature tests

---

## Verification

1. **Unit Tests** - Test each component in isolation
2. **Feature Tests** - Test full request/response cycles with fakes
3. **Static Analysis** - PHPStan level 8
4. **Code Style** - Laravel Pint
5. **Manual Testing** - Test against Samsara sandbox API

---

## Summary

This plan delivers a complete, fluent Laravel SDK for Samsara's API with:
- 197 endpoint coverage across 30+ categories
- Laravel-native HTTP client (no Saloon)
- Fluent query builder with pagination and lazy streaming
- Comprehensive entity system with casting
- Full testing support with fakes
- Clean exception handling
- Laravel facade and service provider integration

See `TODO.md` for the complete implementation checklist.
