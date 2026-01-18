# Samsara Laravel SDK

Laravel SDK for the Samsara Fleet Management API. Provides 40+ resources for fleet, telematics, safety (HOS), dispatch, and industrial operations.

## Installation & Configuration

@verbatim
<code-snippet name="Install and configure" lang="bash">
composer require erikgall/samsara
</code-snippet>
@endverbatim

Environment variables in `.env`:
- `SAMSARA_API_KEY` - API token (required)
- `SAMSARA_REGION` - `us` (default) or `eu`

## Basic Usage

@verbatim
<code-snippet name="CRUD operations via facade" lang="php">
use Samsara\Facades\Samsara;

// Read
$drivers = Samsara::drivers()->all();
$driver = Samsara::drivers()->find('driver-123');

// Create
$driver = Samsara::drivers()->create(['name' => 'John Doe', 'phone' => '555-0123']);

// Update
$driver = Samsara::drivers()->update('driver-123', ['phone' => '555-0199']);

// Delete
Samsara::tags()->delete('tag-789');

// Switch regions
Samsara::useEuEndpoint();
</code-snippet>
@endverbatim

## Entity Access

@verbatim
<code-snippet name="Entity properties and collections" lang="php">
$driver = Samsara::drivers()->find('driver-123');

// Properties
$name = $driver->name;
$id = $driver->getId();

// Serialization
$array = $driver->toArray();
$json = $driver->toJson();

// EntityCollection methods
$drivers = Samsara::drivers()->all();
$driver = $drivers->findById('driver-123');
$ids = $drivers->ids();
$names = $drivers->pluck('name');
</code-snippet>
@endverbatim

## Exception Handling

Exception hierarchy: `SamsaraException` (base) → `AuthenticationException` (401), `AuthorizationException` (403), `NotFoundException` (404), `ValidationException` (422), `RateLimitException` (429), `ServerException` (5xx)

@verbatim
<code-snippet name="Handle API exceptions" lang="php">
use Samsara\Exceptions\{SamsaraException, ValidationException, RateLimitException};

try {
    $driver = Samsara::drivers()->create(['name' => 'John']);
} catch (ValidationException $e) {
    $errors = $e->getErrors();
} catch (RateLimitException $e) {
    $retryAfter = $e->getRetryAfter();
} catch (SamsaraException $e) {
    $context = $e->getContext();
}
</code-snippet>
@endverbatim

## Available Resources

**Fleet:** `drivers()`, `vehicles()`, `trailers()`, `equipment()`

**Telematics:** `vehicleStats()`, `vehicleLocations()`, `trips()`

**Safety:** `hoursOfService()`, `maintenance()`, `safetyEvents()`

**Dispatch:** `routes()`, `addresses()`

**Organization:** `users()`, `tags()`, `contacts()`

**Integrations:** `webhooks()`, `gateways()`, `liveShares()`

**Industrial:** `industrial()`, `sensors()`, `assets()`

**Additional:** `alerts()`, `forms()`, `ifta()`, `idling()`, `fuelAndEnergy()`, `workOrders()`, `tachograph()`, `speeding()`, `cameraMedia()`, `hubs()`, `settings()`, `routeEvents()`, `driverVehicleAssignments()`, `driverTrailerAssignments()`, `trailerAssignments()`, `carrierProposedAssignments()`
