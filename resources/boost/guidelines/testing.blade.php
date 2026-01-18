# Samsara ELD Laravel SDK - Testing Guidelines

## Overview

The SDK provides testing utilities to mock API responses and assert requests in your application tests. Use `SamsaraFake` to create a fake client instance that records requests and returns predefined responses.

## Creating a Fake Instance

### Using the Facade

@verbatim
<code-snippet name="Create fake instance via facade" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Create a new SamsaraFake instance
$fake = Samsara::fake();
</code-snippet>
@endverbatim

### Using the Class Directly

@verbatim
<code-snippet name="Create fake instance directly" lang="php">
use ErikGall\Samsara\Testing\SamsaraFake;

// Create directly
$fake = new SamsaraFake();

// Or use static factory
$fake = SamsaraFake::create();
</code-snippet>
@endverbatim

## Faking Responses

### Generic Response Faking

@verbatim
<code-snippet name="Fake a generic API response" lang="php">
use ErikGall\Samsara\Facades\Samsara;

$fake = Samsara::fake();

// Fake response for any endpoint
$fake->fakeResponse('/fleet/drivers', [
    ['id' => 'driver-1', 'name' => 'John Doe'],
    ['id' => 'driver-2', 'name' => 'Jane Smith'],
]);

// Now API calls return faked data
$drivers = $fake->drivers()->all();
// Returns EntityCollection with 2 drivers
</code-snippet>
@endverbatim

### Resource-Specific Faking

@verbatim
<code-snippet name="Use convenience methods for common resources" lang="php">
use ErikGall\Samsara\Facades\Samsara;

$fake = Samsara::fake();

// Fake drivers response
$fake->fakeDrivers([
    ['id' => 'driver-1', 'name' => 'John Doe', 'phone' => '555-0100'],
    ['id' => 'driver-2', 'name' => 'Jane Smith', 'phone' => '555-0101'],
]);

// Fake vehicles response
$fake->fakeVehicles([
    ['id' => 'vehicle-1', 'name' => 'Truck 101', 'vin' => '1HGBH41JXMN109186'],
    ['id' => 'vehicle-2', 'name' => 'Van 202', 'vin' => '2HGBH41JXMN109187'],
]);

// Fake vehicle stats response
$fake->fakeVehicleStats([
    ['id' => 'stat-1', 'vehicleId' => 'vehicle-1', 'gps' => ['latitude' => 37.77, 'longitude' => -122.41]],
]);
</code-snippet>
@endverbatim

### Faking Error Responses

@verbatim
<code-snippet name="Fake error responses for testing error handling" lang="php">
use ErikGall\Samsara\Facades\Samsara;

$fake = Samsara::fake();

// Fake a 404 Not Found response
$fake->fakeResponse('/fleet/drivers/invalid-id', [], 404);

// Fake a 422 Validation Error response
$fake->fakeResponse('/fleet/drivers', [
    'message' => 'Validation failed',
    'errors' => ['name' => ['The name field is required.']],
], 422);

// Fake a 429 Rate Limit response
$fake->fakeResponse('/fleet/vehicles', [
    'message' => 'Rate limit exceeded',
], 429);

// Fake a 500 Server Error response
$fake->fakeResponse('/fleet/drivers', [
    'message' => 'Internal server error',
], 500);
</code-snippet>
@endverbatim

## Using Test Fixtures

The SDK includes fixture files with realistic sample data for testing.

### Loading Fixtures

@verbatim
<code-snippet name="Load fixture data for tests" lang="php">
use ErikGall\Samsara\Facades\Samsara;
use ErikGall\Samsara\Testing\Fixtures;

$fake = Samsara::fake();

// Load fixture data
$driversData = Fixtures::drivers();
$vehiclesData = Fixtures::vehicles();

// Use fixtures in fake responses
$fake->fakeDrivers($driversData['data']);
$fake->fakeVehicles($vehiclesData['data']);
</code-snippet>
@endverbatim

### Available Fixtures

@verbatim
<code-snippet name="Available fixture methods" lang="php">
use ErikGall\Samsara\Testing\Fixtures;

// Fleet fixtures
$drivers = Fixtures::drivers();
$vehicles = Fixtures::vehicles();
$trailers = Fixtures::trailers();
$equipment = Fixtures::equipment();

// Safety fixtures
$hosLogs = Fixtures::hosLogs();
$safetyEvents = Fixtures::safetyEvents();
$dvirs = Fixtures::dvirs();

// Dispatch fixtures
$routes = Fixtures::routes();
$addresses = Fixtures::addresses();

// Organization fixtures
$tags = Fixtures::tags();
$users = Fixtures::users();

// Integration fixtures
$webhooks = Fixtures::webhooks();

// Telematics fixtures
$vehicleStats = Fixtures::vehicleStats();

// Load any fixture by filename
$custom = Fixtures::load('custom-fixture.json');
</code-snippet>
@endverbatim

### Custom Fixtures Path

@verbatim
<code-snippet name="Use custom fixtures directory" lang="php">
use ErikGall\Samsara\Testing\Fixtures;

// Set custom fixtures path for your app
Fixtures::setFixturesPath(base_path('tests/fixtures/samsara'));

// Load from custom path
$drivers = Fixtures::drivers();
</code-snippet>
@endverbatim

## Asserting Requests

### Assert Endpoint Was Called

@verbatim
<code-snippet name="Assert an API endpoint was requested" lang="php">
use ErikGall\Samsara\Facades\Samsara;

$fake = Samsara::fake();
$fake->fakeDrivers([]);

// Make API call in your code
$fake->drivers()->all();

// Assert the endpoint was called
$fake->assertRequested('/fleet/drivers');
</code-snippet>
@endverbatim

### Assert Request Parameters

@verbatim
<code-snippet name="Assert request included specific parameters" lang="php">
use ErikGall\Samsara\Facades\Samsara;

$fake = Samsara::fake();
$fake->fakeDrivers([]);

// Make filtered API call
$fake->drivers()->query()
    ->whereTag('fleet-a')
    ->limit(10)
    ->get();

// Assert request had specific params
$fake->assertRequestedWithParams('/fleet/drivers', [
    'tagIds' => ['fleet-a'],
    'limit' => 10,
]);
</code-snippet>
@endverbatim

### Assert Nothing Was Requested

@verbatim
<code-snippet name="Assert no API requests were made" lang="php">
use ErikGall\Samsara\Facades\Samsara;

$fake = Samsara::fake();

// Code that should not make API calls
$service = new MyService($fake);
$cachedResult = $service->getCachedDrivers();

// Assert no requests were made
$fake->assertNothingRequested();
</code-snippet>
@endverbatim

## Complete Test Example

### Unit Test with SamsaraFake

@verbatim
<code-snippet name="Complete unit test example" lang="php">
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use ErikGall\Samsara\Facades\Samsara;
use ErikGall\Samsara\Testing\Fixtures;
use App\Services\FleetReportService;
use PHPUnit\Framework\Attributes\Test;

class FleetReportServiceTest extends TestCase
{
    #[Test]
    public function it_generates_driver_report(): void
    {
        // Arrange
        $fake = Samsara::fake();
        $fake->fakeDrivers([
            ['id' => 'driver-1', 'name' => 'John Doe', 'status' => 'active'],
            ['id' => 'driver-2', 'name' => 'Jane Smith', 'status' => 'active'],
            ['id' => 'driver-3', 'name' => 'Bob Wilson', 'status' => 'inactive'],
        ]);

        $service = new FleetReportService($fake);

        // Act
        $report = $service->generateActiveDriverReport();

        // Assert
        $this->assertCount(2, $report['drivers']);
        $fake->assertRequested('/fleet/drivers');
    }

    #[Test]
    public function it_handles_api_errors_gracefully(): void
    {
        // Arrange
        $fake = Samsara::fake();
        $fake->fakeResponse('/fleet/drivers', ['message' => 'Server error'], 500);

        $service = new FleetReportService($fake);

        // Act
        $report = $service->generateActiveDriverReport();

        // Assert
        $this->assertNull($report);
        $this->assertDatabaseHas('error_logs', [
            'source' => 'samsara',
            'message' => 'Server error',
        ]);
    }
}
</code-snippet>
@endverbatim

### Testing with Fixtures

@verbatim
<code-snippet name="Test using fixture data" lang="php">
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use ErikGall\Samsara\Facades\Samsara;
use ErikGall\Samsara\Testing\Fixtures;
use App\Services\VehicleTelemetryService;
use PHPUnit\Framework\Attributes\Test;

class VehicleTelemetryServiceTest extends TestCase
{
    #[Test]
    public function it_processes_vehicle_stats(): void
    {
        // Arrange
        $fake = Samsara::fake();

        // Use realistic fixture data
        $vehicleStats = Fixtures::vehicleStats();
        $fake->fakeVehicleStats($vehicleStats['data']);

        $service = new VehicleTelemetryService($fake);

        // Act
        $summary = $service->getFleetSummary();

        // Assert
        $this->assertArrayHasKey('totalVehicles', $summary);
        $this->assertArrayHasKey('averageFuelPercent', $summary);
        $fake->assertRequested('/fleet/vehicles/stats');
    }
}
</code-snippet>
@endverbatim

### Testing Exception Handling

@verbatim
<code-snippet name="Test exception handling" lang="php">
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use ErikGall\Samsara\Facades\Samsara;
use ErikGall\Samsara\Exceptions\RateLimitException;
use App\Services\DriverSyncService;
use PHPUnit\Framework\Attributes\Test;

class DriverSyncServiceTest extends TestCase
{
    #[Test]
    public function it_retries_on_rate_limit(): void
    {
        // Arrange
        $fake = Samsara::fake();
        $fake->fakeResponse('/fleet/drivers', ['message' => 'Rate limited'], 429);

        $service = new DriverSyncService($fake);

        // Assert exception is thrown
        $this->expectException(RateLimitException::class);

        // Act
        $service->syncAllDrivers();
    }

    #[Test]
    public function it_logs_validation_errors(): void
    {
        // Arrange
        $fake = Samsara::fake();
        $fake->fakeResponse('/fleet/drivers', [
            'message' => 'Validation failed',
            'errors' => ['name' => ['Name is required']],
        ], 422);

        $service = new DriverSyncService($fake);

        // Act
        $result = $service->createDriver(['phone' => '555-0100']);

        // Assert
        $this->assertFalse($result);
        $this->assertDatabaseHas('sync_errors', [
            'field' => 'name',
            'message' => 'Name is required',
        ]);
    }
}
</code-snippet>
@endverbatim

## Accessing Recorded Requests

@verbatim
<code-snippet name="Access recorded requests for custom assertions" lang="php">
use ErikGall\Samsara\Facades\Samsara;

$fake = Samsara::fake();
$fake->fakeDrivers([]);
$fake->fakeVehicles([]);

// Make multiple API calls
$fake->drivers()->all();
$fake->vehicles()->all();

// Get all recorded requests
$requests = $fake->getRecordedRequests();

// Custom assertions
$this->assertCount(2, $requests);
$this->assertStringContains('/fleet/drivers', $requests[0]->url());
$this->assertStringContains('/fleet/vehicles', $requests[1]->url());
</code-snippet>
@endverbatim

## Testing Best Practices

1. **Always use SamsaraFake** - Never make real API calls in tests
2. **Use fixtures for realistic data** - Fixtures contain properly structured responses
3. **Test error scenarios** - Fake 4xx and 5xx responses to test error handling
4. **Assert specific endpoints** - Verify your code calls the expected APIs
5. **Test with parameters** - Use `assertRequestedWithParams()` to verify query filters
6. **Isolate tests** - Create fresh fake instances for each test
7. **Test edge cases** - Empty responses, single items, large datasets

## SamsaraFake Method Reference

| Method | Description |
|--------|-------------|
| `fakeResponse($endpoint, $data, $status)` | Fake response for endpoint |
| `fakeDrivers($drivers)` | Fake drivers endpoint response |
| `fakeVehicles($vehicles)` | Fake vehicles endpoint response |
| `fakeVehicleStats($stats)` | Fake vehicle stats endpoint response |
| `assertRequested($endpoint)` | Assert endpoint was called |
| `assertRequestedWithParams($endpoint, $params)` | Assert with specific params |
| `assertNothingRequested()` | Assert no requests made |
| `getRecordedRequests()` | Get all recorded requests |

## Fixtures Method Reference

| Method | Description |
|--------|-------------|
| `drivers()` | Load drivers fixture |
| `vehicles()` | Load vehicles fixture |
| `trailers()` | Load trailers fixture |
| `equipment()` | Load equipment fixture |
| `hosLogs()` | Load HOS logs fixture |
| `safetyEvents()` | Load safety events fixture |
| `dvirs()` | Load DVIRs fixture |
| `routes()` | Load routes fixture |
| `addresses()` | Load addresses fixture |
| `tags()` | Load tags fixture |
| `users()` | Load users fixture |
| `webhooks()` | Load webhooks fixture |
| `vehicleStats()` | Load vehicle stats fixture |
| `load($filename)` | Load any fixture by filename |
| `setFixturesPath($path)` | Set custom fixtures directory |
| `getFixturesPath()` | Get current fixtures directory |
