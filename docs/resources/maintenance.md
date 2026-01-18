# Maintenance Resource

Access DVIRs (Driver Vehicle Inspection Reports) and defects for fleet maintenance tracking.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all DVIRs
$dvirs = Samsara::maintenance()->all();
```

## DVIRs (Driver Vehicle Inspection Reports)

```php
// Get DVIRs stream
$dvirs = Samsara::maintenance()->dvirs()->get();

// Create a DVIR
$dvir = Samsara::maintenance()->createDvir([
    'driverId' => 'driver-123',
    'vehicleId' => 'vehicle-456',
    'inspectionType' => 'preTrip',
    'trailerIds' => ['trailer-789'],
    'defects' => [
        [
            'defectType' => 'Brakes',
            'comment' => 'Brake pad worn',
        ],
    ],
]);

// Filter DVIRs by driver
$dvirs = Samsara::maintenance()
    ->dvirs()
    ->whereDriver('driver-123')
    ->get();

// Filter DVIRs by vehicle
$dvirs = Samsara::maintenance()
    ->dvirs()
    ->whereVehicle('vehicle-456')
    ->get();
```

## Time-Based Queries

```php
use Carbon\Carbon;

// Get DVIRs for a time range
$dvirs = Samsara::maintenance()
    ->dvirs()
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->get();

// Get DVIRs updated after a specific time
$dvirs = Samsara::maintenance()
    ->dvirs()
    ->updatedAfter(Carbon::now()->subHour())
    ->get();
```

## Defects

```php
// Get defects stream
$defects = Samsara::maintenance()->defects()->get();

// Filter defects by time range
$defects = Samsara::maintenance()
    ->defects()
    ->between(now()->subDays(30), now())
    ->get();

// Filter defects by vehicle
$defects = Samsara::maintenance()
    ->defects()
    ->whereVehicle('vehicle-123')
    ->get();
```

## Lazy Loading

```php
// Stream through large DVIR datasets
Samsara::maintenance()
    ->dvirs()
    ->between(now()->subYear(), now())
    ->lazy(500)
    ->each(function ($dvir) {
        // Process each DVIR
    });
```

## DVIR Entity

```php
$dvir = Samsara::maintenance()->dvirs()->first();

$dvir->id;             // string
$dvir->driverId;       // ?string
$dvir->vehicleId;      // ?string
$dvir->inspectionType; // string
$dvir->defects;        // array
$dvir->trailerIds;     // array
$dvir->createdAtTime;  // string
```

## Available Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | DVIR ID |
| `driverId` | string | Driver who created the DVIR |
| `vehicleId` | string | Inspected vehicle ID |
| `inspectionType` | string | 'preTrip' or 'postTrip' |
| `defects` | array | List of defects found |
| `trailerIds` | array | Trailer IDs included in inspection |
| `createdAtTime` | string | Creation timestamp (ISO 8601) |
| `odometerMeters` | float | Odometer reading |
| `location` | object | Inspection location |
