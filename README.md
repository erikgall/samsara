# Samsara SDK for Laravel

A Laravel SDK for selected [Samsara Fleet Management API](https://developers.samsara.com/) endpoints. It is built on Laravel's HTTP client and provides resource wrappers, typed and generic entities, raw-array helpers, and a fluent query builder.

See the [documentation coverage and API-sync report](https://github.com/erikgall/samsara/blob/dev/docs/api-sync/documentation-coverage.md) for the audited package surface and current upstream endpoint status as of 2026-08-10.

> **Upgrading to v2:** Version 2 changes public signatures, generic capabilities, DTOs, errors, pagination, query encoding, and HTTP paths. Follow the complete [v1.1.0 to v2.0.0 upgrade guide](UPGRADE.md) before changing the Composer constraint.

## Features

- **41 Resource Accessors** - Package wrappers across Fleet, Telematics, Safety, Dispatch, Industrial, and more
- **Fluent Query Builder** - Filter, paginate, and stream data with an intuitive query interface
- **Mixed Response Models** - Typed entities where the resource declares one, generic entities for other builders, and raw arrays for several report and legacy helpers
- **Cursor Pagination** - Built-in support for Samsara's cursor-based pagination
- **Lazy Collections** - Memory-efficient streaming for large datasets
- **Webhook Signature Verification** - Middleware for validating incoming Samsara webhook requests
- **Testing Support** - `SamsaraFake` class for easy mocking in tests

## Requirements

- PHP 8.2 or higher
- Laravel 12.x or 13.x

## Installation

```bash
composer require erikgall/samsara
```

The package will auto-register its service provider and facade.

### Laravel Boost Agent Guidance

This package ships a concise Laravel Boost guideline plus on-demand skills for current v2 integration work and v1.1-to-v2 upgrades. Laravel Boost discovers them from the installed package; no files need to be copied manually.

For a new Boost setup, run `php artisan boost:install`, enable AI guidelines and Agent Skills, and select `erikgall/samsara` when Boost lists third-party packages. For an existing Boost setup, discover the package after installing or updating it:

```bash
php artisan boost:update --discover
```

Select `erikgall/samsara`, then keep the generated resources current with `php artisan boost:update`. The published source lives in `resources/boost/guidelines/` and `resources/boost/skills/` for package maintainers; consuming applications should use Boost's generated agent-specific files.

### Publish Configuration

```bash
php artisan vendor:publish --provider="Samsara\SamsaraServiceProvider"
```

This will create a `config/samsara.php` file where you can configure your API settings.

## Configuration

Add your Samsara API token to your `.env` file:

```env
SAMSARA_API_KEY=your-api-token-here
SAMSARA_REGION=us  # 'us', 'eu', or 'ca'
SAMSARA_WEBHOOK_SECRET=your-base64-encoded-secret
```

Configuration options in `config/samsara.php`:

```php
return [
    'api_key' => env('SAMSARA_API_KEY'),
    'region' => env('SAMSARA_REGION', 'us'),
    'timeout' => env('SAMSARA_TIMEOUT', 30),
    'attempts' => env('SAMSARA_ATTEMPTS', 3),
    'retry_writes' => env('SAMSARA_RETRY_WRITES', false),
    'retry_after_max_seconds' => env('SAMSARA_RETRY_AFTER_MAX_SECONDS', 30),
    'per_page' => env('SAMSARA_PER_PAGE', 100),
    'webhook_secret' => env('SAMSARA_WEBHOOK_SECRET'),
];
```

`attempts` is the maximum total request count, including the initial request. Set it to `1` to make one request without retries; the default of `3` permits up to two retries.

`retry_after_max_seconds` is the largest rate-limit delay the SDK will honor inside the current process. A larger `Retry-After` value throws `RateLimitException` immediately so a web request or worker can reschedule instead of blocking indefinitely.

`per_page` is the implicit `limit` for `paginate()` and `lazy()` when the caller does not supply one. An explicit `paginate($perPage)`, `lazy($chunkSize)`, `limit()`, or `where('limit', ...)` value wins. Endpoint helpers whose official contract has cursors but no `limit` disable this default automatically; do not add an explicit limit to those endpoints.

For a fleet-wide token, the `api_key` setting above reads `SAMSARA_API_KEY`. For per-tenant credentials, retain or chain the isolated client:

```php
use Samsara\Facades\Samsara;

$client = Samsara::withToken($carrier->samsara_token);
$drivers = $client->drivers()->query()->get();
```

`withToken()`, `withRegion('us'|'eu'|'ca')`, and `withBaseUrl($url)` return copies. An unknown region throws `InvalidArgumentException`.

Writes (POST, PUT, PATCH, DELETE) are not retried on connection failures, HTTP 408, or 5xx by default. Set `SAMSARA_RETRY_WRITES=true` only when replaying accepted writes is safe. HTTP 429 remains retryable for every method within the attempt budget and Retry-After ceiling.

## Quick Start

### Using the Facade

```php
use Samsara\Facades\Samsara;

// Get one response page of drivers
$drivers = Samsara::drivers()->query()->get();

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
        return $this->samsara->drivers()->query()->get();
    }
}
```

### Using the Client Directly

```php
use Samsara\Samsara;

$samsara = new Samsara('your-api-token');

// Retain the copy for the Canada region ('us' and 'eu' are also supported).
$samsara = $samsara->withRegion('ca');

$drivers = $samsara->drivers()->query()->get();
```

## Available Resources

This section inventories package accessors. Resources expose only supported CRUD methods; `all()` is removed. Use an endpoint builder for collection reads; consult each resource page and the [capability matrix](https://github.com/erikgall/samsara/blob/dev/docs/api-sync/documentation-coverage.md#exact-generic-capability-matrix) before using an operation.

### Fleet Resources

```php
// Drivers
$drivers = Samsara::drivers()->query()->get();
$driver = Samsara::drivers()->find('driver-id');
$driver = Samsara::drivers()->create([
    'name' => 'John Doe',
    'username' => 'john.doe',
    'password' => $driverPassword,
]);
$driver = Samsara::drivers()->update('driver-id', ['name' => 'Jane Doe']);
Samsara::drivers()->activate('driver-id');
Samsara::drivers()->deactivate('driver-id');

// Vehicles
$vehicles = Samsara::vehicles()->query()->get();
$vehicle = Samsara::vehicles()->find('vehicle-id');

// Trailers
$trailers = Samsara::trailers()->query()->get();

// Equipment
$equipment = Samsara::equipment()->query()->get();
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
    ->types(['gps'])
    ->between($startTime, $endTime)
    ->get();

// Vehicle Locations
$locations = Samsara::vehicleLocations()->current()->get();

// Trips
$trips = Samsara::trips()
    ->query()
    ->startTime($startTime)
    ->where('ids', 'vehicle-1,vehicle-2')
    ->get();
```

### Safety Resources

```php
// Hours of Service
$logs = Samsara::hoursOfService()->logs()->get();
$clocks = Samsara::hoursOfService()->clocks()->get();
$violations = Samsara::hoursOfService()->violations()->get();

// Safety Events
$events = Samsara::safetyEvents()->query()->between($startTime, $endTime)->get();

// Maintenance (DVIRs)
$dvirs = Samsara::maintenance()->dvirs()->startTime($startTime)->get();
$defects = Samsara::maintenance()->defects()->startTime($startTime)->get();
```

### Dispatch Resources

```php
// Routes
$routes = Samsara::routes()->query()->between($startTime, $endTime)->get();
$route = Samsara::routes()->create([
    'name' => 'Morning delivery',
    'stops' => [
        ['addressId' => 'address-1', 'scheduledArrivalTime' => $startTime],
        ['addressId' => 'address-2', 'scheduledArrivalTime' => $endTime],
    ],
]);

// Addresses
$addresses = Samsara::addresses()->query()->get();
```

### Organization Resources

```php
$organization = Samsara::me();

// Users
$users = Samsara::users()->query()->get();

// Tags
$tags = Samsara::tags()->query()->get();

// Contacts
$contacts = Samsara::contacts()->query()->get();
```

### Industrial Resources

```php
// Industrial Assets
$assets = Samsara::industrial()->assets()->get();

// Data Inputs
$inputs = Samsara::industrial()->dataInputs()->get();

// Legacy Sensors (v1 API)
$sensors = Samsara::sensors()->list();
```

### Integration Resources

```php
// Webhooks
$webhooks = Samsara::webhooks()->query()->get();
$webhook = Samsara::webhooks()->create([
    'name' => 'My Webhook',
    'url' => 'https://example.com/webhook',
    'eventTypes' => ['VehicleUpdated'],
]);

// Gateways
$gateways = Samsara::gateways()->query()->get();

// Live Sharing Links
$shares = Samsara::liveShares()->query()->get();
```

### Additional Resources

```php
// Alerts
Samsara::alerts()->configurations()->get();

// Forms
Samsara::forms()->submissions()->where('ids', 'submission-id')->get();
Samsara::forms()->templates()->get();

// Current streaming endpoints
Samsara::idling()->events()->between($startTime, $endTime)->get();
Samsara::issues()->stream()->startTime($startTime)->get();

// And many more...
```

The package also exposes alert incidents, Fuel and Energy, IFTA, Camera Media, Settings, Tachograph, Preview, and other helpers. Their resource pages give the exact implemented path, signature, and return shape. Operations present only in the 2026-08-10 official contract remain deferred and are not implied by an accessor or inherited method name.

## Query Builder

The query builder provides a fluent interface for filtering and paginating data. It does not validate filters against the selected endpoint, so use only parameters declared on that resource page:

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
->withDecorations(['gps'])

// Pagination
->limit(100)
->after($cursor)

// Expand Related Data (only on endpoints that declare expand)
->expand('vehicle')

// Custom Filters
->where('status', 'active')
```

Dedicated list helpers such as `types()`, `expand()`, `withDecorations()`, and the `where*()` ID helpers use comma-separated query values. Endpoint helpers mark OpenAPI `explode=true` arrays explicitly and send repeated keys, such as `ids=a&ids=b`. Arbitrary arrays passed through `where()` retain Laravel's standard query encoding.

### Execution Methods

```php
// Get results as EntityCollection
$results = $query->get();

// Get first result
$first = $query->first();

// Paginate one page with cursor metadata
$paginator = $query->paginate(100);

// Lazy stream (memory efficient; chunk size only where limit is supported)
$lazy = $query->lazy(100);
```

### Cursor Pagination

```php
$paginator = Samsara::drivers()->query()->paginate(50);

foreach ($paginator as $driver) {
    // Process driver
}

while ($paginator->hasMorePages()) {
    $next = $paginator->nextPage();

    if ($next === null) {
        break;
    }

    $paginator = $next;

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

Many collection and record responses are mapped to entity classes that extend `Illuminate\Support\Fluent`. Some multi-endpoint resources reuse one entity class for different payload shapes, and several helpers return generic entities or raw arrays; check the resource page before relying on a concrete type.

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
$drivers = Samsara::drivers()->query()->get();

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
use Samsara\Exceptions\ValidationException;
use Samsara\Exceptions\RateLimitException;
use Samsara\Exceptions\ServerException;

try {
    $drivers = Samsara::drivers()->query()->get();
} catch (RateLimitException $e) {
    $retryAfter = $e->getRetryAfter();
    // Handle rate limiting
} catch (ValidationException $e) {
    $errors = $e->getErrors();
    // Handle validation errors
}
```

Supported generic resource operations, custom resource helpers, and all query-builder terminal methods use the same typed exception mapper. `find()` remains the exception: a 404 returns `null`. HTTP 400 and 422 responses become `ValidationException`; 401, 403, 404, 429, and 5xx responses use their status-specific SDK exceptions. Unsupported CRUD methods are absent. Explicitly unsupported helpers, such as equipment external-ID lookup, throw `UnsupportedOperationException` before any HTTP request. Exhausted transport failures remain `Illuminate\Http\Client\ConnectionException`.

## Webhook Signature Verification

The SDK includes middleware to verify that incoming webhook requests are authentic. It validates the HMAC-SHA256 signature sent in the `X-Samsara-Signature` header.

```php
use Samsara\Webhooks\VerifyWebhookSignature;

Route::post('/samsara/webhook', WebhookController::class)
    ->middleware(VerifyWebhookSignature::class);
```

The middleware uses the `webhook_secret` from your `config/samsara.php` by default. To configure a custom secret and tolerance (in seconds) without registering your own middleware alias, pass a custom secret and tolerance through the `samsara.webhook` alias:

```php
Route::post('/samsara/webhook', WebhookController::class)
    ->middleware('samsara.webhook:'.base64_encode('my-secret-key').',600');
```

For detailed usage, custom secrets, and controller examples, see the [Webhooks documentation](https://github.com/erikgall/samsara/blob/dev/docs/resources/webhooks.md).

## Testing

The SDK includes a `SamsaraFake` class for testing:

```php
use Samsara\Facades\Samsara;

#[\PHPUnit\Framework\Attributes\Test]
public function it_fetches_drivers(): void
{
    $fake = Samsara::fake([
        '/fleet/drivers' => [
            ['id' => 'driver-1', 'name' => 'John Doe'],
            ['id' => 'driver-2', 'name' => 'Jane Doe'],
        ],
    ]);

    // The facade root and both container bindings now resolve to this fake.
    $drivers = Samsara::drivers()->query()->get();

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

This package is open-sourced software licensed under the MIT license.

## Credits

- [Erik Galloway](https://github.com/erikgall)
