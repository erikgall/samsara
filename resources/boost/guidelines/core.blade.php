# Samsara ELD Laravel SDK - Core Guidelines

## Overview

The Samsara ELD Laravel SDK provides a fluent, Laravel-style interface for the Samsara Fleet Management API. It offers access to 40+ API resources including fleet management, telematics, safety compliance (HOS), dispatch, and industrial operations.

**Key Features:**
- Fluent query builder with filtering, pagination, and lazy loading
- Strongly-typed entity objects with convenient property access
- Automatic cursor-based pagination handling
- Comprehensive exception handling for all API error scenarios
- Test utilities with SamsaraFake and fixture data

## Installation

@verbatim
<code-snippet name="Install the SDK via Composer" lang="bash">
composer require erikgall/samsara
</code-snippet>
@endverbatim

## Configuration

Set the following environment variables in your `.env` file:

@verbatim
<code-snippet name="Environment configuration" lang="env">
SAMSARA_API_KEY=your-api-key-here
SAMSARA_REGION=us
SAMSARA_TIMEOUT=30
SAMSARA_RETRY=3
</code-snippet>
@endverbatim

- `SAMSARA_API_KEY` - Your Samsara API token (required)
- `SAMSARA_REGION` - API region: `us` (default) or `eu`
- `SAMSARA_TIMEOUT` - Request timeout in seconds (default: 30)
- `SAMSARA_RETRY` - Number of retry attempts (default: 3)

## Basic Usage

### Using the Facade

@verbatim
<code-snippet name="Basic facade usage pattern" lang="php">
use Samsara\Facades\Samsara;

// Get all drivers
$drivers = Samsara::drivers()->all();

// Find a specific vehicle by ID
$vehicle = Samsara::vehicles()->find('vehicle-123');

// Create a new address
$address = Samsara::addresses()->create([
    'name' => 'Warehouse A',
    'formattedAddress' => '123 Main St, City, ST 12345',
]);

// Update a driver
$driver = Samsara::drivers()->update('driver-456', [
    'name' => 'John Smith',
    'phone' => '555-0123',
]);

// Delete an entity
Samsara::tags()->delete('tag-789');
</code-snippet>
@endverbatim

### Using Dependency Injection

@verbatim
<code-snippet name="Dependency injection pattern" lang="php">
use Samsara\Samsara;

class FleetService
{
    public function __construct(
        protected Samsara $samsara
    ) {}

    public function getActiveDrivers(): array
    {
        return $this->samsara->drivers()
            ->active()
            ->get()
            ->all();
    }
}
</code-snippet>
@endverbatim

### Switching API Regions

@verbatim
<code-snippet name="Switch between US and EU API endpoints" lang="php">
use Samsara\Facades\Samsara;

// Use EU endpoint
$euDrivers = Samsara::useEuEndpoint()->drivers()->all();

// Switch back to US endpoint
$usDrivers = Samsara::useUsEndpoint()->drivers()->all();
</code-snippet>
@endverbatim

## CRUD Operations

All resources support standard CRUD operations through a consistent interface.

### Get All Entities

@verbatim
<code-snippet name="Retrieve all entities from a resource" lang="php">
use Samsara\Facades\Samsara;

// Returns an EntityCollection
$drivers = Samsara::drivers()->all();
$vehicles = Samsara::vehicles()->all();
$tags = Samsara::tags()->all();
</code-snippet>
@endverbatim

### Find Entity by ID

@verbatim
<code-snippet name="Find a single entity by its ID" lang="php">
use Samsara\Facades\Samsara;

// Returns the entity or null if not found
$driver = Samsara::drivers()->find('driver-123');

if ($driver === null) {
    // Handle not found case
}
</code-snippet>
@endverbatim

### Create Entity

@verbatim
<code-snippet name="Create a new entity" lang="php">
use Samsara\Facades\Samsara;

$driver = Samsara::drivers()->create([
    'name' => 'Jane Doe',
    'username' => 'jdoe',
    'phone' => '555-0199',
    'licenseNumber' => 'DL12345678',
    'licenseState' => 'CA',
]);

$webhook = Samsara::webhooks()->create([
    'name' => 'Fleet Events',
    'url' => 'https://example.com/webhooks/samsara',
    'eventTypes' => ['VehicleCreated', 'VehicleUpdated'],
]);
</code-snippet>
@endverbatim

### Update Entity

@verbatim
<code-snippet name="Update an existing entity" lang="php">
use Samsara\Facades\Samsara;

$driver = Samsara::drivers()->update('driver-123', [
    'phone' => '555-0200',
    'notes' => 'Updated contact information',
]);
</code-snippet>
@endverbatim

### Delete Entity

@verbatim
<code-snippet name="Delete an entity by ID" lang="php">
use Samsara\Facades\Samsara;

// Returns true on success
$deleted = Samsara::addresses()->delete('address-456');
</code-snippet>
@endverbatim

## Entity Access

Entities extend Laravel's Fluent class, providing convenient property access and serialization methods.

### Property Access

@verbatim
<code-snippet name="Access entity properties" lang="php">
use Samsara\Facades\Samsara;

$driver = Samsara::drivers()->find('driver-123');

// Access properties directly
$name = $driver->name;
$phone = $driver->phone;
$licenseNumber = $driver->licenseNumber;

// Access nested data
$tags = $driver->tags;

// Get the entity ID
$id = $driver->getId();

// Check if property exists
$hasEmail = isset($driver->email);
</code-snippet>
@endverbatim

### Serialization

@verbatim
<code-snippet name="Convert entities to array or JSON" lang="php">
use Samsara\Facades\Samsara;

$vehicle = Samsara::vehicles()->find('vehicle-123');

// Convert to array
$array = $vehicle->toArray();

// Convert to JSON
$json = $vehicle->toJson();

// Works with Laravel responses
return response()->json($vehicle);
</code-snippet>
@endverbatim

## EntityCollection

Query results are returned as `EntityCollection` objects, which extend Laravel's Collection with entity-specific helpers.

### Collection Methods

@verbatim
<code-snippet name="Work with EntityCollection results" lang="php">
use Samsara\Facades\Samsara;

$drivers = Samsara::drivers()->all();

// Find by ID within collection
$driver = $drivers->findById('driver-123');

// Get all IDs as array
$ids = $drivers->ids();

// Use Laravel collection methods
$names = $drivers->pluck('name');
$active = $drivers->filter(fn ($d) => $d->status === 'active');
$first = $drivers->first();
$count = $drivers->count();

// Iterate over results
foreach ($drivers as $driver) {
    echo $driver->name;
}
</code-snippet>
@endverbatim

## Exception Handling

The SDK provides a hierarchy of exceptions for different API error scenarios.

### Exception Hierarchy

- `SamsaraException` - Base exception for all SDK errors
  - `AuthenticationException` - Invalid or missing API token (401)
  - `AuthorizationException` - Insufficient permissions (403)
  - `NotFoundException` - Resource not found (404)
  - `ValidationException` - Invalid request data (422)
  - `RateLimitException` - API rate limit exceeded (429)
  - `ServerException` - Samsara server error (5xx)
  - `ConnectionException` - Network connection failure

### Handling Exceptions

@verbatim
<code-snippet name="Handle API exceptions" lang="php">
use Samsara\Facades\Samsara;
use Samsara\Exceptions\SamsaraException;
use Samsara\Exceptions\NotFoundException;
use Samsara\Exceptions\ValidationException;
use Samsara\Exceptions\RateLimitException;
use Samsara\Exceptions\AuthenticationException;

try {
    $driver = Samsara::drivers()->create([
        'name' => 'John Doe',
    ]);
} catch (AuthenticationException $e) {
    // Invalid API key
    Log::error('Invalid Samsara API key');
} catch (ValidationException $e) {
    // Invalid request data - access validation errors
    $errors = $e->getErrors();
    Log::warning('Validation failed', ['errors' => $errors]);
} catch (RateLimitException $e) {
    // Rate limited - check retry-after header
    $retryAfter = $e->getRetryAfter();
    Log::info("Rate limited, retry after {$retryAfter} seconds");
} catch (NotFoundException $e) {
    // Resource not found
    Log::warning('Resource not found');
} catch (SamsaraException $e) {
    // Catch-all for other API errors
    $context = $e->getContext();
    Log::error('Samsara API error', $context);
}
</code-snippet>
@endverbatim

### Exception Context

@verbatim
<code-snippet name="Access exception context for debugging" lang="php">
use Samsara\Exceptions\SamsaraException;

try {
    $driver = Samsara::drivers()->find('invalid-id');
} catch (SamsaraException $e) {
    // Get detailed context
    $context = $e->getContext();

    // Context includes:
    // - status: HTTP status code
    // - endpoint: API endpoint called
    // - body: Response body

    Log::error($e->getMessage(), $context);
}
</code-snippet>
@endverbatim

## Available Resources

The SDK provides access to 40+ API resources organized by domain:

### Fleet Resources
- `drivers()` - Driver management with activation status
- `vehicles()` - Vehicle management and tracking
- `trailers()` - Trailer management
- `equipment()` - Equipment tracking

### Telematics Resources
- `vehicleStats()` - Vehicle telemetry data (GPS, fuel, odometer)
- `vehicleLocations()` - Real-time vehicle locations
- `trips()` - Trip history and details

### Safety Resources
- `hoursOfService()` - HOS logs, clocks, violations, daily logs
- `maintenance()` - Maintenance schedules and DVIRs
- `safetyEvents()` - Safety event tracking

### Dispatch Resources
- `routes()` - Route management
- `addresses()` - Address/geofence management

### Organization Resources
- `users()` - User management
- `tags()` - Tag management for grouping
- `contacts()` - Contact management

### Industrial Resources
- `industrial()` - Industrial operations
- `sensors()` - Sensor data (legacy v1)
- `assets()` - Asset tracking

### Integration Resources
- `webhooks()` - Webhook management
- `gateways()` - Gateway management
- `liveShares()` - Live sharing links

### Additional Resources
- `alerts()` - Alert configuration
- `forms()` - Form templates and submissions
- `ifta()` - IFTA reporting
- `idling()` - Idling reports
- `issues()` - Issue tracking
- `fuelAndEnergy()` - Fuel and energy data
- `tachograph()` - Tachograph data (EU)
- `speeding()` - Speeding events
- `workOrders()` - Work order management
- `cameraMedia()` - Camera media access
- `hubs()` - Hub management
- `settings()` - Organization settings
- `routeEvents()` - Route event tracking
- `carrierProposedAssignments()` - Carrier assignments
- `driverVehicleAssignments()` - Driver-vehicle assignments
- `driverTrailerAssignments()` - Driver-trailer assignments
- `trailerAssignments()` - Trailer assignments

### Beta/Preview/Legacy
- `beta()` - Beta API features
- `preview()` - Preview API features
- `legacy()` - Deprecated v1 endpoints

## Best Practices

1. **Always handle exceptions** - Wrap API calls in try/catch blocks
2. **Use lazy loading for large datasets** - Use `lazy()` instead of `all()` for memory efficiency
3. **Filter server-side** - Use query builder filters instead of fetching all and filtering in PHP
4. **Cache results** - Cache frequently accessed data like tags or addresses
5. **Use dependency injection** - Inject `Samsara` class for better testability
