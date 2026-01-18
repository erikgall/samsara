# Samsara Testing

Use `SamsaraFake` to mock API responses in tests.

## Creating a Fake

@verbatim
<code-snippet name="Create fake instance" lang="php">
use Samsara\Facades\Samsara;
use Samsara\Testing\SamsaraFake;

// Via facade
$fake = Samsara::fake();

// Or directly
$fake = SamsaraFake::create();
</code-snippet>
@endverbatim

## Faking Responses

@verbatim
<code-snippet name="Fake API responses" lang="php">
$fake = Samsara::fake();

// Generic response faking
$fake->fakeResponse('/fleet/drivers', [
    ['id' => 'driver-1', 'name' => 'John Doe'],
    ['id' => 'driver-2', 'name' => 'Jane Smith'],
]);

// Convenience methods
$fake->fakeDrivers([['id' => 'd-1', 'name' => 'John', 'phone' => '555-0100']]);
$fake->fakeVehicles([['id' => 'v-1', 'name' => 'Truck 101', 'vin' => '1HGBH41JXMN109186']]);
$fake->fakeVehicleStats([['id' => 's-1', 'vehicleId' => 'v-1', 'gps' => ['latitude' => 37.77, 'longitude' => -122.41]]]);

// Fake error responses
$fake->fakeResponse('/fleet/drivers/invalid', [], 404);
$fake->fakeResponse('/fleet/drivers', ['message' => 'Validation failed', 'errors' => ['name' => ['Required']]], 422);
$fake->fakeResponse('/fleet/vehicles', ['message' => 'Rate limit exceeded'], 429);
</code-snippet>
@endverbatim

## Fixtures

@verbatim
<code-snippet name="Use fixture data" lang="php">
use Samsara\Testing\Fixtures;

// Load fixtures
$drivers = Fixtures::drivers();
$vehicles = Fixtures::vehicles();
$vehicleStats = Fixtures::vehicleStats();
$hosLogs = Fixtures::hosLogs();
$routes = Fixtures::routes();
$addresses = Fixtures::addresses();
$tags = Fixtures::tags();
$webhooks = Fixtures::webhooks();

// Use with fake
$fake->fakeDrivers($drivers['data']);
</code-snippet>
@endverbatim

## Assertions

@verbatim
<code-snippet name="Assert API calls" lang="php">
$fake = Samsara::fake();
$fake->fakeDrivers([]);

$fake->drivers()->all();

// Assert endpoint called
$fake->assertRequested('/fleet/drivers');

// Assert with parameters
$fake->drivers()->query()->whereTag('fleet-a')->limit(10)->get();
$fake->assertRequestedWithParams('/fleet/drivers', ['tagIds' => ['fleet-a'], 'limit' => 10]);

// Assert nothing called
$fake->assertNothingRequested();

// Get recorded requests
$requests = $fake->getRecordedRequests();
</code-snippet>
@endverbatim

## Test Example

@verbatim
<code-snippet name="Complete test example" lang="php">
<?php

namespace Tests\Unit;

use Tests\TestCase;
use Samsara\Facades\Samsara;
use Samsara\Exceptions\RateLimitException;
use PHPUnit\Framework\Attributes\Test;

class FleetServiceTest extends TestCase
{
    #[Test]
    public function it_fetches_active_drivers(): void
    {
        $fake = Samsara::fake();
        $fake->fakeDrivers([
            ['id' => 'd-1', 'name' => 'John', 'status' => 'active'],
            ['id' => 'd-2', 'name' => 'Jane', 'status' => 'active'],
        ]);

        $drivers = $fake->drivers()->all();

        $this->assertCount(2, $drivers);
        $fake->assertRequested('/fleet/drivers');
    }

    #[Test]
    public function it_handles_rate_limits(): void
    {
        $fake = Samsara::fake();
        $fake->fakeResponse('/fleet/drivers', ['message' => 'Rate limited'], 429);

        $this->expectException(RateLimitException::class);

        $fake->drivers()->all();
    }
}
</code-snippet>
@endverbatim
