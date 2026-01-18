# Testing

The Samsara SDK provides `SamsaraFake` for mocking API responses in your tests.

## Basic Usage

Use the `Samsara::fake()` method to create a fake client:

```php
use Samsara\Facades\Samsara;
use Samsara\Testing\SamsaraFake;

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

## Faking Resource Responses

### Drivers

```php
$fake->fakeDrivers([
    [
        'id' => 'driver-1',
        'name' => 'John Doe',
        'phone' => '+1234567890',
        'driverActivationStatus' => 'active',
    ],
]);
```

### Vehicles

```php
$fake->fakeVehicles([
    [
        'id' => 'vehicle-1',
        'name' => 'Truck 001',
        'vin' => '1HGBH41JXMN109186',
        'make' => 'Ford',
        'model' => 'F-150',
    ],
]);
```

### Vehicle Stats

```php
$fake->fakeVehicleStats([
    [
        'id' => 'vehicle-1',
        'name' => 'Truck 001',
        'gps' => [
            'latitude' => 37.7749,
            'longitude' => -122.4194,
            'time' => '2024-01-15T10:00:00Z',
        ],
        'engineState' => [
            'value' => 'On',
            'time' => '2024-01-15T10:00:00Z',
        ],
    ],
]);
```

## Custom Response Faking

For endpoints without dedicated fake methods, use `fakeResponse()`:

```php
$fake->fakeResponse('/fleet/routes', [
    [
        'id' => 'route-1',
        'name' => 'Morning Route',
        'scheduledStartTime' => '2024-01-15T08:00:00Z',
    ],
]);

$routes = $fake->routes()->all();
```

### With Status Codes

```php
// Fake a 404 response
$fake->fakeResponse('/fleet/drivers/invalid-id', [], 404);

// Fake a validation error
$fake->fakeResponse('/fleet/drivers', [
    'message' => 'Validation failed',
    'errors' => ['name' => ['Name is required']],
], 422);
```

## Assertions

### Assert Endpoint Was Called

```php
$fake = Samsara::fake();
$fake->fakeDrivers([['id' => 'driver-1', 'name' => 'John']]);

$fake->drivers()->all();

$fake->assertRequested('/fleet/drivers');
```

### Assert Nothing Was Requested

```php
$fake = Samsara::fake();

$fake->assertNothingRequested();
```

### Get Recorded Requests

```php
$fake = Samsara::fake();
$fake->fakeDrivers([['id' => 'driver-1', 'name' => 'John']]);

$fake->drivers()->all();

$requests = $fake->getRecordedRequests();
// ['GET /fleet/drivers']
```

## Using Fixtures

The SDK includes JSON fixtures for common responses:

```php
use Samsara\Testing\Fixtures;

// Load driver fixtures
$drivers = Fixtures::drivers();

// Load vehicle fixtures
$vehicles = Fixtures::vehicles();

// Load vehicle stats fixtures
$stats = Fixtures::vehicleStats();
```

### Available Fixtures

- `Fixtures::drivers()` - Sample driver data
- `Fixtures::vehicles()` - Sample vehicle data
- `Fixtures::vehicleStats()` - Sample vehicle stats
- `Fixtures::trailers()` - Sample trailer data
- `Fixtures::equipment()` - Sample equipment data
- `Fixtures::routes()` - Sample route data
- `Fixtures::addresses()` - Sample address data
- `Fixtures::hosLogs()` - Sample HOS log data
- `Fixtures::dvirs()` - Sample DVIR data
- `Fixtures::safetyEvents()` - Sample safety event data
- `Fixtures::webhooks()` - Sample webhook data
- `Fixtures::users()` - Sample user data
- `Fixtures::tags()` - Sample tag data

### Using Fixtures with SamsaraFake

```php
use Samsara\Facades\Samsara;
use Samsara\Testing\Fixtures;

public function test_with_fixtures(): void
{
    $fake = Samsara::fake();
    $fake->fakeDrivers(Fixtures::drivers());

    $drivers = $fake->drivers()->all();

    $this->assertNotEmpty($drivers);
}
```

## Testing Error Handling

### Authentication Errors

```php
use Samsara\Exceptions\AuthenticationException;

public function test_handles_auth_error(): void
{
    $fake = Samsara::fake();
    $fake->fakeResponse('/fleet/drivers', [
        'message' => 'Invalid API token',
    ], 401);

    $this->expectException(AuthenticationException::class);

    $fake->drivers()->all();
}
```

### Validation Errors

```php
use Samsara\Exceptions\ValidationException;

public function test_handles_validation_error(): void
{
    $fake = Samsara::fake();
    $fake->fakeResponse('/fleet/drivers', [
        'message' => 'Validation failed',
        'errors' => ['name' => ['Name is required']],
    ], 422);

    try {
        $fake->drivers()->create([]);
    } catch (ValidationException $e) {
        $this->assertArrayHasKey('name', $e->getErrors());
    }
}
```

### Rate Limiting

```php
use Samsara\Exceptions\RateLimitException;

public function test_handles_rate_limit(): void
{
    $fake = Samsara::fake();
    $fake->fakeResponse('/fleet/drivers', [
        'message' => 'Rate limit exceeded',
    ], 429);

    $this->expectException(RateLimitException::class);

    $fake->drivers()->all();
}
```

## Complete Test Example

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Samsara\Facades\Samsara;
use Samsara\Testing\Fixtures;
use Samsara\Data\Driver\Driver;

class FleetServiceTest extends TestCase
{
    public function test_it_can_list_active_drivers(): void
    {
        $fake = Samsara::fake();
        $fake->fakeDrivers([
            ['id' => 'driver-1', 'name' => 'John', 'driverActivationStatus' => 'active'],
            ['id' => 'driver-2', 'name' => 'Jane', 'driverActivationStatus' => 'deactivated'],
        ]);

        $drivers = $fake->drivers()->all();

        $this->assertCount(2, $drivers);
        $this->assertInstanceOf(Driver::class, $drivers[0]);
    }

    public function test_it_can_find_driver_by_id(): void
    {
        $fake = Samsara::fake();
        $fake->fakeResponse('/fleet/drivers/driver-1', [
            'id' => 'driver-1',
            'name' => 'John Doe',
        ]);

        $driver = $fake->drivers()->find('driver-1');

        $this->assertNotNull($driver);
        $this->assertSame('John Doe', $driver->name);
    }

    public function test_it_returns_null_for_missing_driver(): void
    {
        $fake = Samsara::fake();
        $fake->fakeResponse('/fleet/drivers/invalid', [], 404);

        $driver = $fake->drivers()->find('invalid');

        $this->assertNull($driver);
    }
}
```

## PHPUnit Traits

Create a trait to simplify test setup:

```php
<?php

namespace Tests\Concerns;

use Samsara\Facades\Samsara;
use Samsara\Testing\SamsaraFake;

trait UsesSamsaraFake
{
    protected SamsaraFake $samsara;

    protected function setUpSamsara(): void
    {
        $this->samsara = Samsara::fake();
    }
}
```

Usage:

```php
class MyTest extends TestCase
{
    use UsesSamsaraFake;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpSamsara();
    }

    public function test_example(): void
    {
        $this->samsara->fakeDrivers([/* ... */]);
        // ...
    }
}
```
