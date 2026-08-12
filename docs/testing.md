---
title: Testing
layout: default
nav_order: 6
description: "Testing with SamsaraFake and fixtures"
permalink: /testing
---

# Testing

- [Introduction](#introduction)
- [Faking the Client](#faking-the-client)
- [Faking Responses](#faking-responses)
- [Assertions](#assertions)
- [Using Fixtures](#using-fixtures)
- [Testing Error Handling](#testing-error-handling)
- [Sharing the Fake Across Tests](#sharing-the-fake-across-tests)
- [Advanced HTTP Mocking](#advanced-http-mocking)

## Introduction

The SDK ships a `SamsaraFake` class with its own faked HTTP client. You stage canned responses, run code through the returned fake or the facade, then assert on the requests it recorded. `Samsara::fake()` replaces the facade root and both container bindings, including when the service provider has not yet resolved the singleton.

## Faking the Client

Call `Samsara::fake()` to create and install a `SamsaraFake`.

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

    $drivers = Samsara::drivers()->all();

    $this->assertCount(2, $drivers);
    $this->assertSame('John Doe', $drivers->first()->name);
}
```

`Samsara::fake()` returns a new `SamsaraFake`, swaps the facade root, and binds that same instance as `Samsara\Samsara` and `samsara`. Direct calls through `$fake` remain equivalent.

## Faking Responses

The fake provides shorthand helpers for the resources you mock most often, plus a generic `fakeResponse()` for anything else.

### Drivers

```php
$fake->fakeDrivers([
    [
        'id'                     => 'driver-1',
        'name'                   => 'John Doe',
        'phone'                  => '+1234567890',
        'driverActivationStatus' => 'active',
    ],
]);
```

### Vehicles

```php
$fake->fakeVehicles([
    [
        'id'    => 'vehicle-1',
        'name'  => 'Truck 001',
        'vin'   => '1HGBH41JXMN109186',
        'make'  => 'Ford',
        'model' => 'F-150',
    ],
]);
```

### Vehicle Stats

```php
$fake->fakeVehicleStats([
    [
        'id'   => 'vehicle-1',
        'name' => 'Truck 001',
        'gps'  => [
            'latitude'  => 37.7749,
            'longitude' => -122.4194,
            'time'      => '2026-01-15T10:00:00Z',
        ],
        'engineState' => [
            'value' => 'On',
            'time'  => '2026-01-15T10:00:00Z',
        ],
    ],
]);
```

### Custom Endpoints

For endpoints without a dedicated helper, use `fakeResponse()`. The endpoint string is matched as a substring of the request URL.

```php
$fake->fakeResponse('/fleet/routes', [
    [
        'id'                 => 'route-1',
        'name'               => 'Morning Route',
        'scheduledRouteStartTime' => '2026-01-15T08:00:00Z',
    ],
]);

$routes = $fake->routes()
    ->query()
    ->between('2026-01-15T00:00:00Z', '2026-01-16T00:00:00Z')
    ->get();
```

Pass a third argument to fake non-200 responses, which is how you exercise error paths:

```php
// 404 from a single-record lookup.
$fake->fakeResponse('/fleet/drivers/invalid-id', [], 404);

// 400 or 422 status for testing ValidationException.
$fake->fakeResponse('/fleet/drivers', [], 400);
```

`fakeResponse()` always wraps the supplied array in a top-level `data` key. It cannot currently stage an arbitrary top-level error envelope such as `message` plus `errors`; use the [advanced HTTP factory](#advanced-http-mocking) when the error body or headers matter.

## Assertions

The fake records every request made against it. Three assertion helpers cover the common cases.

### `assertRequested(string $endpoint)`

Asserts that at least one request URL contained the given endpoint substring.

```php
$fake = Samsara::fake();
$fake->fakeDrivers([['id' => 'driver-1', 'name' => 'John']]);

$fake->drivers()->all();

$fake->assertRequested('/fleet/drivers');
```

### `assertRequestedWithParams(string $endpoint, array $params)`

Asserts that a request to the endpoint was made with each of the given query parameters. Useful for verifying that filter and time-range arguments reached the API.

```php
use Carbon\Carbon;

$fake = Samsara::fake();
$fake->fakeVehicleStats([]);

$start = Carbon::parse('2026-01-01T00:00:00Z');
$end   = Carbon::parse('2026-01-02T00:00:00Z');

$fake->vehicleStats()
    ->history()
    ->types(['gps'])
    ->between($start, $end)
    ->get();

$fake->assertRequestedWithParams('/fleet/vehicles/stats/history', [
    'startTime' => '2026-01-01T00:00:00Z',
    'endTime'   => '2026-01-02T00:00:00Z',
    'types'     => 'gps',
]);
```

### `assertNothingRequested()`

Asserts the fake recorded zero requests — useful for confirming a code path short-circuits before reaching the API.

```php
$fake = Samsara::fake();

$fake->assertNothingRequested();
```

### Inspecting Recorded Requests

For ad-hoc assertions, `getRecordedRequests()` returns the underlying `Illuminate\Http\Client\Request` objects.

```php
$fake = Samsara::fake();
$fake->fakeDrivers([['id' => 'driver-1', 'name' => 'John']]);

$fake->drivers()->all();

$requests = $fake->getRecordedRequests();
$this->assertCount(1, $requests);
$this->assertStringContainsString('/fleet/drivers', $requests[0]->url());
```

## Using Fixtures

The SDK ships JSON fixtures for common resources. Each helper returns a complete decoded response envelope with a top-level `data` key (and, where present, pagination metadata). Fake helpers expect the inner item list, so pass `Fixtures::drivers()['data']` rather than the full envelope.

```php
use Samsara\Testing\Fixtures;

$drivers  = Fixtures::drivers();
$vehicles = Fixtures::vehicles();
$stats    = Fixtures::vehicleStats();
```

### Available Fixtures

- `Fixtures::drivers()` — sample driver data
- `Fixtures::vehicles()` — sample vehicle data
- `Fixtures::vehicleStats()` — sample vehicle stats
- `Fixtures::trailers()` — sample trailer data
- `Fixtures::equipment()` — sample equipment data
- `Fixtures::routes()` — sample route data
- `Fixtures::addresses()` — sample address data
- `Fixtures::hosLogs()` — sample HOS log data
- `Fixtures::dvirs()` — sample DVIR data
- `Fixtures::safetyEvents()` — sample safety event data
- `Fixtures::webhooks()` — sample webhook data
- `Fixtures::users()` — sample user data
- `Fixtures::tags()` — sample tag data

### Combining Fixtures with the Fake

```php
use Samsara\Testing\Fixtures;

public function test_with_fixtures(): void
{
    $fake = Samsara::fake();
    $payload = Fixtures::drivers();
    $fake->fakeDrivers($payload['data']);

    $drivers = $fake->drivers()->all();

    $this->assertNotEmpty($drivers);
}
```

### Custom Fixture Paths

If you prefer to keep your own fixtures alongside your tests, point the loader at a different directory and load files by name. `Fixtures::load()` is the same machinery the named helpers use.

```php
Fixtures::setFixturesPath(base_path('tests/Fixtures/samsara'));

$payload = Fixtures::load('custom-drivers.json');
```

| Method | Returns | Description |
|---|---|---|
| `getFixturesPath()` | `string` | The current fixtures directory. Defaults to the SDK's bundled `Fixtures/` directory. |
| `setFixturesPath(string $path)` | `void` | Override the directory used for subsequent `load()` calls. |
| `load(string $filename)` | `array<string, mixed>` | Load and decode a JSON fixture file. Throws `RuntimeException` on missing files or invalid JSON. |

## Testing Error Handling

Stage non-200 responses with `fakeResponse()` to exercise your exception handling. Builder terminals and custom resource methods use the same typed mapper as supported generic methods.

### Authentication Errors

```php
use Samsara\Exceptions\AuthenticationException;

public function test_handles_auth_error(): void
{
    $fake = Samsara::fake();
    $fake->fakeResponse('/fleet/drivers', ['message' => 'Invalid API token'], 401);

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
    $fake->fakeResponse('/fleet/drivers', [], 400);

    $this->expectException(ValidationException::class);

    $fake->drivers()->create([]);
}
```

### Rate Limiting

```php
use Samsara\Exceptions\RateLimitException;

public function test_handles_rate_limit(): void
{
    $fake = Samsara::fake();
    $fake->fakeResponse('/fleet/drivers', ['message' => 'Rate limit exceeded'], 429);

    $this->expectException(RateLimitException::class);

    $fake->drivers()->all();
}
```

See [Error Handling](error-handling.html) for the full exception reference.

## Sharing the Fake Across Tests

For larger test suites, extract a trait that resets the fake in `setUp()`.

```php
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

```php
class FleetServiceTest extends TestCase
{
    use UsesSamsaraFake;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpSamsara();
    }

    public function test_example(): void
    {
        $this->samsara->fakeDrivers([
            ['id' => 'driver-1', 'name' => 'John Doe'],
        ]);

        $drivers = $this->samsara->drivers()->all();

        $this->assertSame('John Doe', $drivers->first()->name);
    }
}
```

## Advanced HTTP Mocking

For tests that need finer control than `fakeResponse()` offers — for example, sequenced responses, conditional matchers, or response headers — drop down to Laravel's `Illuminate\Http\Client\Factory` directly. The client exposes `getHttpFactory()` and `setHttpFactory()` for this case.

`Samsara::getHttpFactory(): HttpFactory` returns the underlying HTTP factory. Call `fake(...)` on it with whatever matchers you need.

```php
$factory = Samsara::getHttpFactory();
$factory->fake([
    'api.samsara.com/fleet/drivers' => $factory->sequence()
        ->push(['data' => []], 200)
        ->push(['data' => [['id' => 'driver-1', 'name' => 'John']]], 200),
]);
```

`Samsara::setHttpFactory(HttpFactory $factory): static` swaps the factory entirely. The fake uses this internally, but you may inject your own factory in tests that need the full Laravel HTTP client API.

```php
use Illuminate\Http\Client\Factory;

$factory = new Factory;
$factory->fake();

Samsara::setHttpFactory($factory);
```
