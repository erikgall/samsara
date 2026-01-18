# Samsara SDK for Laravel

A comprehensive Laravel SDK for the [Samsara Fleet Management API](https://developers.samsara.com/). Built with Laravel's HTTP client, this SDK provides a fluent, type-safe interface for all Samsara API endpoints.

## Features

- **40+ Resource Endpoints** - Full coverage of Samsara's API including Fleet, Telematics, Safety, Dispatch, Industrial, and more
- **Fluent Query Builder** - Filter, paginate, and stream data with an intuitive query interface
- **Type-Safe Entities** - All API responses are mapped to strongly-typed entity classes
- **Cursor Pagination** - Built-in support for Samsara's cursor-based pagination
- **Lazy Collections** - Memory-efficient streaming for large datasets
- **Testing Support** - `SamsaraFake` class for easy mocking in tests

## Requirements

- PHP 8.1 or higher
- Laravel 10.x, 11.x, or 12.x

## Installation

```bash
composer require erikgall/samsara
```

The package will auto-register its service provider and facade.

### Publish Configuration

```bash
php artisan vendor:publish --provider="Samsara\SamsaraServiceProvider"
```

This will create a `config/samsara.php` file where you can configure your API settings.

## Configuration

Add your Samsara API token to your `.env` file:

```env
SAMSARA_API_KEY=your-api-token-here
SAMSARA_REGION=us  # or 'eu' for European API
```

Configuration options in `config/samsara.php`:

```php
return [
    'api_key' => env('SAMSARA_API_KEY'),
    'region' => env('SAMSARA_REGION', 'us'),
    'timeout' => env('SAMSARA_TIMEOUT', 30),
    'retry' => env('SAMSARA_RETRY', 3),
    'per_page' => env('SAMSARA_PER_PAGE', 100),
];
```

## Quick Start

### Using the Facade

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

### Using Dependency Injection

```php
use Samsara\Samsara;

class FleetController extends Controller
{
    public function __construct(private Samsara $samsara) {}

    public function drivers()
    {
        return $this->samsara->drivers()->all();
    }
}
```

### Using the Client Directly

```php
use Samsara\Samsara;

$samsara = new Samsara('your-api-token');

// Use EU endpoint
$samsara->useEuEndpoint();

$drivers = $samsara->drivers()->all();
```

## Available Resources

### Fleet Resources

```php
// Drivers
$drivers = Samsara::drivers()->all();
$driver = Samsara::drivers()->find('driver-id');
$driver = Samsara::drivers()->create(['name' => 'John Doe', ...]);
$driver = Samsara::drivers()->update('driver-id', ['name' => 'Jane Doe']);
$samsara->drivers()->delete('driver-id');
$samsara->drivers()->activate('driver-id');
$samsara->drivers()->deactivate('driver-id');

// Vehicles
$vehicles = Samsara::vehicles()->all();
$vehicle = Samsara::vehicles()->find('vehicle-id');

// Trailers
$trailers = Samsara::trailers()->all();

// Equipment
$equipment = Samsara::equipment()->all();
```

### Telematics Resources

```php
// Vehicle Stats (current snapshot)
$stats = Samsara::vehicleStats()
    ->current()
    ->types(['gps', 'fuelPercents', 'engineStates'])
    ->get();

// Vehicle Stats (streaming feed)
$stats = Samsara::vehicleStats()
    ->feed()
    ->types(['gps'])
    ->lazy();

// Vehicle Stats (historical)
$stats = Samsara::vehicleStats()
    ->history()
    ->between($startTime, $endTime)
    ->get();

// Vehicle Locations
$locations = Samsara::vehicleLocations()->current()->get();

// Trips
$trips = Samsara::trips()->query()->get();
```

### Safety Resources

```php
// Hours of Service
$logs = Samsara::hoursOfService()->logs()->get();
$clocks = Samsara::hoursOfService()->clocks()->get();
$violations = Samsara::hoursOfService()->violations()->get();

// Safety Events
$events = Samsara::safetyEvents()->query()->get();

// Maintenance (DVIRs)
$dvirs = Samsara::maintenance()->dvirs()->get();
$defects = Samsara::maintenance()->defects()->get();
```

### Dispatch Resources

```php
// Routes
$routes = Samsara::routes()->all();
$route = Samsara::routes()->create([...]);

// Addresses
$addresses = Samsara::addresses()->all();
```

### Organization Resources

```php
// Users
$users = Samsara::users()->all();

// Tags
$tags = Samsara::tags()->all();

// Contacts
$contacts = Samsara::contacts()->all();
```

### Industrial Resources

```php
// Industrial Assets
$assets = Samsara::industrial()->assets()->get();

// Data Inputs
$inputs = Samsara::industrial()->dataInputs()->get();

// Legacy Sensors (v1 API)
$sensors = Samsara::sensors()->list(['groupId' => 123]);
```

### Integration Resources

```php
// Webhooks
$webhooks = Samsara::webhooks()->all();
$webhook = Samsara::webhooks()->create([
    'name' => 'My Webhook',
    'url' => 'https://example.com/webhook',
    'eventTypes' => ['VehicleLocationUpdated'],
]);

// Gateways
$gateways = Samsara::gateways()->all();

// Live Sharing Links
$shares = Samsara::liveShares()->all();
```

### Additional Resources

```php
// Alerts
Samsara::alerts()->configurations()->get();
Samsara::alerts()->incidents()->get();

// Forms
Samsara::forms()->submissions()->get();
Samsara::forms()->templates();

// Fuel & Energy
Samsara::fuelAndEnergy()->driverEfficiency()->get();

// IFTA Reports
Samsara::ifta()->jurisdictionReport()->get();

// And many more...
```

## Query Builder

The query builder provides a fluent interface for filtering and paginating data:

```php
$drivers = Samsara::drivers()
    ->query()
    ->whereTag(['tag-1', 'tag-2'])
    ->updatedAfter(now()->subDays(7))
    ->limit(50)
    ->get();
```

### Available Methods

```php
// Filtering
->whereTag($tagIds)
->whereParentTag($parentTagIds)
->whereVehicle($vehicleIds)
->whereDriver($driverIds)
->whereAttribute($attributeValueIds)

// Time Filters
->updatedAfter($datetime)
->createdAfter($datetime)
->startTime($datetime)
->endTime($datetime)
->between($start, $end)

// Stats Types
->types(['gps', 'engineStates', 'fuelPercents'])
->withDecorations(['reverseGeo'])

// Pagination
->limit(100)
->after($cursor)

// Expand Related Data
->expand(['driver', 'vehicle'])

// Custom Filters
->where('status', 'active')
```

### Execution Methods

```php
// Get results as EntityCollection
$results = $query->get();

// Get first result
$first = $query->first();

// Paginate with cursor
$paginator = $query->paginate(100);

// Lazy stream (memory efficient)
$lazy = $query->lazy(100);
```

### Cursor Pagination

```php
$paginator = Samsara::drivers()->query()->paginate(50);

foreach ($paginator as $driver) {
    // Process driver
}

while ($paginator->hasMorePages()) {
    $paginator = $paginator->nextPage();

    foreach ($paginator as $driver) {
        // Process next page
    }
}
```

### Lazy Collections

For large datasets, use lazy collections to stream results:

```php
$drivers = Samsara::drivers()
    ->query()
    ->lazy(100) // Fetch 100 at a time
    ->filter(fn ($driver) => $driver->isActive())
    ->take(500);

foreach ($drivers as $driver) {
    // Memory-efficient processing
}
```

## Entities

All API responses are mapped to entity classes that extend `Illuminate\Support\Fluent`:

```php
$driver = Samsara::drivers()->find('driver-id');

// Access properties
$driver->id;
$driver->name;
$driver->phone;

// Check status
$driver->isActive();
$driver->isDeactivated();

// Convert to array
$driver->toArray();

// Convert to JSON
$driver->toJson();
```

### Entity Collections

```php
$drivers = Samsara::drivers()->all();

// Find by ID
$driver = $drivers->findById('driver-123');

// Get all IDs
$ids = $drivers->ids();

// All Laravel Collection methods work
$activeDrivers = $drivers->filter(fn ($d) => $d->isActive());
```

## Error Handling

The SDK throws specific exceptions for different error types:

```php
use Samsara\Exceptions\AuthenticationException;
use Samsara\Exceptions\AuthorizationException;
use Samsara\Exceptions\NotFoundException;
use Samsara\Exceptions\ValidationException;
use Samsara\Exceptions\RateLimitException;
use Samsara\Exceptions\ServerException;

try {
    $driver = Samsara::drivers()->find('invalid-id');
} catch (NotFoundException $e) {
    // Handle 404
} catch (RateLimitException $e) {
    $retryAfter = $e->getRetryAfter();
    // Handle rate limiting
} catch (ValidationException $e) {
    $errors = $e->getErrors();
    // Handle validation errors
}
```

## Testing

The SDK includes a `SamsaraFake` class for testing:

```php
use Samsara\Testing\SamsaraFake;

public function test_it_fetches_drivers(): void
{
    $fake = SamsaraFake::create();

    $fake->fakeDrivers([
        ['id' => 'driver-1', 'name' => 'John Doe'],
        ['id' => 'driver-2', 'name' => 'Jane Doe'],
    ]);

    $drivers = $fake->drivers()->all();

    $this->assertCount(2, $drivers);
    $fake->assertRequested('/fleet/drivers');
}
```

### Using Test Fixtures

```php
use Samsara\Testing\Fixtures;

$driversData = Fixtures::drivers();
$vehiclesData = Fixtures::vehicles();
```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

- [Erik Galloway](https://github.com/erikgall)
