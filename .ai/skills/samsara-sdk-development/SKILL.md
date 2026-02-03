---
name: samsara-sdk-development
description: Build and extend the Samsara ELD Laravel SDK - resources, entities, queries, webhooks, and testing patterns.
---

# Samsara SDK Development

## When to Use This Skill

Use this skill when:
- Adding new API resources (endpoints)
- Creating new entity/DTO classes
- Adding query builder methods
- Creating enums for API values
- Adding exception types
- Working with webhooks
- Writing tests for SDK components

## Architecture Overview

```
src/
├── Samsara.php                    # Main client (resource access)
├── Resources/                     # API resource classes
│   └── {Category}/{Entity}Resource.php
├── Data/                          # Entity/DTO classes
│   ├── Entity.php                 # Base entity
│   └── {Domain}/{EntityName}.php
├── Query/Builder.php              # Fluent query builder
├── Enums/                         # Backed string enums
├── Exceptions/                    # Exception hierarchy
└── Webhooks/                      # Webhook verification
```

## Creating a New Resource

### Step 1: Create the Resource Class

Location: `src/Resources/{Category}/{Entity}Resource.php`

```php
<?php

namespace Samsara\Resources\Fleet;

use Samsara\Data\Fleet\Vehicle;
use Samsara\Data\EntityCollection;
use Samsara\Resources\Resource;

/**
 * Vehicles resource.
 *
 * @author Your Name <your.email@example.com>
 */
class VehiclesResource extends Resource
{
    /**
     * The API endpoint.
     */
    protected string $endpoint = 'fleet/vehicles';

    /**
     * The entity class.
     *
     * @var class-string<Vehicle>
     */
    protected string $entity = Vehicle::class;
}
```

### Step 2: Add Custom Methods (if needed)

```php
/**
 * Get active vehicles.
 */
public function active(): Builder
{
    return $this->query()->where('vehicleStatus', 'active');
}

/**
 * Find vehicle by VIN.
 */
public function findByVin(string $vin): ?Vehicle
{
    return $this->query()
        ->where('vin', $vin)
        ->first();
}

/**
 * Get vehicle locations.
 */
public function locations(): Builder
{
    return $this->createBuilderWithEndpoint('fleet/vehicles/locations');
}
```

### Step 3: Register in Main Client

In `src/Samsara.php`, add the getter method:

```php
public function vehicles(): VehiclesResource
{
    return $this->resource(VehiclesResource::class);
}
```

### Step 4: Write Tests

```php
#[Test]
public function it_can_get_all_vehicles(): void
{
    $this->http->fake([
        '*' => $this->http->response([
            'data' => [
                ['id' => '123', 'name' => 'Truck 1'],
            ],
        ]),
    ]);

    $vehicles = $this->samsara->vehicles()->all();

    $this->assertInstanceOf(EntityCollection::class, $vehicles);
    $this->assertCount(1, $vehicles);
}
```

## Creating a New Entity

### Step 1: Create the Entity Class

Location: `src/Data/{Domain}/{EntityName}.php`

```php
<?php

namespace Samsara\Data\Fleet;

use Samsara\Data\Entity;

/**
 * Vehicle entity.
 *
 * @property-read string|null $id
 * @property-read string|null $name
 * @property-read string|null $vin
 * @property-read string|null $make
 * @property-read string|null $model
 * @property-read int|null $year
 * @property-read array<int, array{id: string, name?: string}>|null $tags
 *
 * @author Your Name <your.email@example.com>
 */
class Vehicle extends Entity
{
    /**
     * Get the vehicle's tags as a collection.
     *
     * @return \Illuminate\Support\Collection<int, array{id: string, name?: string}>
     */
    public function getTags(): \Illuminate\Support\Collection
    {
        return collect($this->get('tags', []));
    }

    /**
     * Check if vehicle has a specific tag.
     */
    public function hasTag(string $tagId): bool
    {
        return $this->getTags()->contains('id', $tagId);
    }

    /**
     * Get nested entity (wrap raw array in typed class).
     */
    public function getGateway(): ?Gateway
    {
        $gateway = $this->get('gateway');

        if (empty($gateway)) {
            return null;
        }

        return new Gateway($gateway);
    }
}
```

### Entity Patterns

**Always use `@property-read` for all API fields:**
```php
/**
 * @property-read string|null $id
 * @property-read string|null $name
 * @property-read array{street?: string, city?: string}|null $address
 */
```

**Getter methods for complex data:**
```php
public function getAddress(): ?Address
{
    $address = $this->get('address');

    if (empty($address)) {
        return null;
    }

    return new Address($address);
}
```

**Boolean helper methods:**
```php
public function isActive(): bool
{
    return $this->get('status') === 'active';
}

public function hasGateway(): bool
{
    return ! empty($this->get('gateway'));
}
```

## Adding Query Builder Methods

Location: `src/Query/Builder.php`

### Filter Methods

```php
/**
 * Filter by vehicle IDs.
 *
 * @param  array<int, string>|string  $ids
 */
public function whereVehicle(array|string $ids): self
{
    return $this->where('vehicleIds', $ids);
}

/**
 * Filter by status.
 */
public function whereStatus(string $status): self
{
    return $this->where('status', $status);
}
```

### Time Methods (use InteractsWithTime trait)

```php
/**
 * Filter by start time.
 */
public function startTime(DateTimeInterface|string $time): self
{
    return $this->where('startTime', $this->formatTime($time));
}

/**
 * Filter between two times.
 */
public function between(DateTimeInterface|string $start, DateTimeInterface|string $end): self
{
    return $this->startTime($start)->endTime($end);
}
```

## Creating Enums

Location: `src/Enums/{EnumName}.php`

### Simple Backed Enum

```php
<?php

namespace Samsara\Enums;

/**
 * Vehicle status enum.
 *
 * @author Your Name <your.email@example.com>
 */
enum VehicleStatus: string
{
    case ACTIVE = 'active';
    case DEACTIVATED = 'deactivated';
}
```

### Feature-Rich Enum

```php
<?php

namespace Samsara\Enums;

use Illuminate\Support\Collection;

/**
 * Webhook event types.
 *
 * @author Your Name <your.email@example.com>
 */
enum WebhookEvent: string
{
    case VEHICLE_CREATED = 'VehicleCreated';
    case VEHICLE_UPDATED = 'VehicleUpdated';
    case DRIVER_CREATED = 'DriverCreated';

    /**
     * Get all enum cases.
     *
     * @return array<int, self>
     */
    public static function all(): array
    {
        return self::cases();
    }

    /**
     * Get all values as collection.
     *
     * @return Collection<int, string>
     */
    public static function values(): Collection
    {
        return collect(self::cases())->map(fn (self $case) => $case->value);
    }
}
```

## Creating Exceptions

Location: `src/Exceptions/{ExceptionName}.php`

### Exception Pattern

```php
<?php

namespace Samsara\Exceptions;

/**
 * Thrown when validation fails.
 *
 * @author Your Name <your.email@example.com>
 */
class ValidationException extends SamsaraException
{
    /**
     * The validation errors.
     *
     * @var array<string, array<int, string>>
     */
    protected array $errors = [];

    /**
     * Create a new exception.
     *
     * @param  array<string, mixed>  $context
     * @param  array<string, array<int, string>>  $errors
     */
    public function __construct(
        string $message,
        array $context = [],
        array $errors = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $context, $previous);
        $this->errors = $errors;
    }

    /**
     * Get the validation errors.
     *
     * @return array<string, array<int, string>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
```

### Static Factory Methods

```php
public static function missingField(string $field): self
{
    return new self(
        "Missing required field: {$field}",
        ['field' => $field]
    );
}

public static function invalidValue(string $field, mixed $value): self
{
    return new self(
        "Invalid value for field: {$field}",
        ['field' => $field, 'value' => $value]
    );
}
```

## Testing Patterns

### Test Setup

```php
<?php

namespace Tests\Unit\Resources\Fleet;

use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\EntityCollection;
use Samsara\Data\Fleet\Vehicle;
use Samsara\Resources\Fleet\VehiclesResource;
use Samsara\Samsara;
use Samsara\Testing\HttpFactory;
use Tests\TestCase;

class VehiclesResourceTest extends TestCase
{
    protected HttpFactory $http;

    protected Samsara $samsara;

    protected function setUp(): void
    {
        parent::setUp();

        $this->http = new HttpFactory;
        $this->samsara = new Samsara('test-token');
        $this->samsara->setHttpFactory($this->http);
    }
}
```

### Testing CRUD Operations

```php
#[Test]
public function it_can_get_all_vehicles(): void
{
    $this->http->fake([
        '*' => $this->http->response([
            'data' => [
                ['id' => '123', 'name' => 'Truck 1'],
                ['id' => '456', 'name' => 'Truck 2'],
            ],
        ]),
    ]);

    $vehicles = $this->samsara->vehicles()->all();

    $this->assertInstanceOf(EntityCollection::class, $vehicles);
    $this->assertCount(2, $vehicles);
    $this->assertInstanceOf(Vehicle::class, $vehicles->first());
}

#[Test]
public function it_can_find_a_vehicle(): void
{
    $this->http->fake([
        '*' => $this->http->response([
            'data' => ['id' => '123', 'name' => 'Truck 1'],
        ]),
    ]);

    $vehicle = $this->samsara->vehicles()->find('123');

    $this->assertInstanceOf(Vehicle::class, $vehicle);
    $this->assertEquals('123', $vehicle->id);
}

#[Test]
public function it_returns_null_when_vehicle_not_found(): void
{
    $this->http->fake([
        '*' => $this->http->response([], 404),
    ]);

    $vehicle = $this->samsara->vehicles()->find('nonexistent');

    $this->assertNull($vehicle);
}
```

### Testing Error Handling

```php
#[Test]
public function it_throws_authentication_exception_on_401(): void
{
    $this->http->fake([
        '*' => $this->http->response(['message' => 'Unauthorized'], 401),
    ]);

    $this->expectException(AuthenticationException::class);

    $this->samsara->vehicles()->all();
}

#[Test]
public function it_throws_rate_limit_exception_on_429(): void
{
    $this->http->fake([
        '*' => $this->http->response(
            ['message' => 'Too many requests'],
            429,
            ['Retry-After' => '60']
        ),
    ]);

    $this->expectException(RateLimitException::class);

    $this->samsara->vehicles()->all();
}
```

### Testing Query Builder

```php
#[Test]
public function it_can_filter_by_status(): void
{
    $this->http->fake([
        '*' => $this->http->response(['data' => []]),
    ]);

    $this->samsara->vehicles()->query()
        ->where('status', 'active')
        ->get();

    $this->http->assertSent(function ($request) {
        return str_contains($request->url(), 'status=active');
    });
}
```

## Code Style Quick Reference

### Control Flow

```php
// Early returns (no else)
if (! $condition) {
    return null;
}

return $this->process();

// Match expressions
$status = match ($value) {
    'active' => Status::ACTIVE,
    'inactive' => Status::INACTIVE,
    default => Status::UNKNOWN,
};

// Null coalescing
$timeout = $config['timeout'] ?? 30;
```

### Naming

| Type | Convention | Example |
|------|------------|---------|
| Variables | camelCase | `$driverLogs` |
| Methods (bool) | is/has/can | `isActive()`, `hasToken()` |
| Methods (getter) | get* | `getDriver()` |
| Methods (setter) | set*/with* | `withToken()` |
| Methods (action) | verb | `create()`, `fetch()` |
| Constants | SCREAMING_SNAKE | `DEFAULT_TIMEOUT` |
| Classes | PascalCase | `DriversResource` |

### Type Declarations

```php
// All methods MUST have return types
public function find(string $id): ?Vehicle

// All parameters MUST have type hints
public function create(array $data): Vehicle

// Use union types when appropriate
public function findBy(string|int $identifier): ?Entity

// Use nullable types explicitly
protected ?string $token = null;
```

### PHPDoc Array Shapes

```php
/**
 * @param  array{name: string, email?: string}  $data
 * @return array<int, Vehicle>
 */

/**
 * @return Collection<int, Driver>
 */

/**
 * @var array<string, class-string<Resource>>
 */
```

## Checklist: Adding a New Feature

- [ ] Create resource class extending `Resource`
- [ ] Set `$endpoint` and `$entity` properties
- [ ] Create entity class with `@property-read` PHPDoc
- [ ] Add getter method in `Samsara.php`
- [ ] Write unit tests with mocked HTTP
- [ ] Test happy path, errors, and edge cases
- [ ] Run `./vendor/bin/pint --dirty`
- [ ] All methods have return types
- [ ] No `else` statements
- [ ] Author tag in class PHPDoc
