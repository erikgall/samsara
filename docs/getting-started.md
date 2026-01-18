# Getting Started

This guide will help you get started with the Samsara SDK for Laravel.

## Requirements

- PHP 8.1 or higher
- Laravel 10.x or 11.x
- Samsara API token

## Installation

Install the package via Composer:

```bash
composer require erikgall/samsara
```

The package will auto-register its service provider and facade.

## Configuration

### 1. Publish the Configuration

```bash
php artisan vendor:publish --provider="ErikGall\Samsara\SamsaraServiceProvider"
```

This creates `config/samsara.php`.

### 2. Set Your API Token

Add your Samsara API token to your `.env` file:

```env
SAMSARA_API_KEY=your-api-token-here
```

### 3. Optional: Configure Region

For EU customers, set the region:

```env
SAMSARA_REGION=eu
```

## Basic Usage

### Using the Facade

```php
use ErikGall\Samsara\Facades\Samsara;

// Get all drivers
$drivers = Samsara::drivers()->all();

// Find a specific vehicle
$vehicle = Samsara::vehicles()->find('vehicle-id');

// Get vehicle stats
$stats = Samsara::vehicleStats()
    ->types(['gps', 'engineStates'])
    ->get();
```

### Using Dependency Injection

```php
use ErikGall\Samsara\Samsara;

class FleetController extends Controller
{
    public function __construct(
        protected Samsara $samsara
    ) {}

    public function index()
    {
        return $this->samsara->drivers()->all();
    }
}
```

### Creating a Fresh Instance

```php
use ErikGall\Samsara\Samsara;

$samsara = Samsara::make('your-api-token');
$drivers = $samsara->drivers()->all();
```

## Available Resources

The SDK provides access to 40+ Samsara API endpoints:

### Fleet
- `drivers()` - Driver management
- `vehicles()` - Vehicle management
- `trailers()` - Trailer management
- `equipment()` - Equipment management

### Telematics
- `vehicleStats()` - Vehicle telemetry data
- `vehicleLocations()` - Real-time vehicle locations
- `trips()` - Trip history

### Safety
- `hoursOfService()` - HOS logs, clocks, violations
- `maintenance()` - DVIRs and defects
- `safetyEvents()` - Safety event tracking

### Dispatch
- `routes()` - Route management
- `addresses()` - Address/geofence management

### Organization
- `users()` - User management
- `tags()` - Tag management
- `contacts()` - Contact management

### Integrations
- `webhooks()` - Webhook management
- `gateways()` - Gateway devices
- `liveShares()` - Live sharing links

See the [README](../README.md) for a complete list of resources.

## Query Builder

Most resources support a fluent query builder:

```php
use ErikGall\Samsara\Facades\Samsara;

// Filter by tags
$drivers = Samsara::drivers()
    ->query()
    ->whereTag('tag-123')
    ->get();

// Time-based queries
$stats = Samsara::vehicleStats()
    ->between('2024-01-01', '2024-01-31')
    ->types(['gps', 'fuelPercents'])
    ->get();

// Pagination
$vehicles = Samsara::vehicles()
    ->query()
    ->limit(50)
    ->paginate();

// Lazy loading for large datasets
Samsara::vehicleStats()
    ->types(['gps'])
    ->lazy()
    ->each(function ($stat) {
        // Process each stat
    });
```

## Error Handling

The SDK throws specific exceptions for different error types:

```php
use ErikGall\Samsara\Facades\Samsara;
use ErikGall\Samsara\Exceptions\AuthenticationException;
use ErikGall\Samsara\Exceptions\NotFoundException;
use ErikGall\Samsara\Exceptions\RateLimitException;
use ErikGall\Samsara\Exceptions\ValidationException;

try {
    $driver = Samsara::drivers()->find('driver-id');
} catch (AuthenticationException $e) {
    // Invalid API token (401)
} catch (NotFoundException $e) {
    // Resource not found (404)
} catch (RateLimitException $e) {
    // Rate limited (429)
    $retryAfter = $e->getRetryAfter();
} catch (ValidationException $e) {
    // Validation errors (422)
    $errors = $e->getErrors();
}
```

## Testing

Use `SamsaraFake` to mock API responses in tests:

```php
use ErikGall\Samsara\Facades\Samsara;

public function test_it_lists_drivers(): void
{
    $fake = Samsara::fake();

    $fake->fakeDrivers([
        ['id' => 'driver-1', 'name' => 'John Doe'],
        ['id' => 'driver-2', 'name' => 'Jane Smith'],
    ]);

    $drivers = $fake->drivers()->all();

    $this->assertCount(2, $drivers);
    $this->assertSame('John Doe', $drivers[0]->name);
}
```

## Next Steps

- [Configuration Guide](configuration.md) - All configuration options
- [Query Builder Guide](query-builder.md) - Advanced querying
- [Testing Guide](testing.md) - Testing with SamsaraFake
- [Error Handling Guide](error-handling.md) - Exception handling
