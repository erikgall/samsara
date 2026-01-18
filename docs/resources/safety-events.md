# Safety Events Resource

Access safety events such as harsh braking, speeding, and other safety-related incidents.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all safety events
$events = Samsara::safetyEvents()->all();

// Find a safety event
$event = Samsara::safetyEvents()->find('event-id');
```

## Query Builder

```php
use Carbon\Carbon;

// Get safety events for a time range
$events = Samsara::safetyEvents()
    ->query()
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->get();

// Filter by vehicle
$events = Samsara::safetyEvents()
    ->query()
    ->whereVehicle('vehicle-123')
    ->between(now()->subWeek(), now())
    ->get();

// Filter by driver
$events = Samsara::safetyEvents()
    ->query()
    ->whereDriver('driver-456')
    ->between(now()->subDays(30), now())
    ->get();

// Filter by tag
$events = Samsara::safetyEvents()
    ->query()
    ->whereTag('fleet-west')
    ->between(now()->subDays(7), now())
    ->get();
```

## Audit Logs

Access safety event audit logs to track changes and reviews:

```php
// Get safety event audit logs feed
$auditLogs = Samsara::safetyEvents()->auditLogs()->get();

// Filter audit logs by time
$auditLogs = Samsara::safetyEvents()
    ->auditLogs()
    ->between(now()->subDays(7), now())
    ->get();
```

## Lazy Loading

```php
// Stream through large safety event datasets
Samsara::safetyEvents()
    ->query()
    ->between(now()->subYear(), now())
    ->lazy(500)
    ->each(function ($event) {
        // Process each safety event
    });
```

## Safety Event Entity

```php
$event = Samsara::safetyEvents()->find('event-id');

$event->id;            // string
$event->driverId;      // ?string
$event->vehicleId;     // ?string
$event->eventType;     // string
$event->time;          // string
$event->location;      // object
$event->behaviorLabel; // ?string
```

## Event Types

Common safety event types include:

- `harshBrake` - Harsh braking event
- `harshAcceleration` - Harsh acceleration event
- `harshTurn` - Harsh turn event
- `speeding` - Speeding event
- `collision` - Collision detected
- `nearCollision` - Near collision detected
- `distractedDriving` - Distracted driving detected
- `drowsiness` - Driver drowsiness detected
- `seatbeltNotWorn` - Seatbelt not worn

## Available Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | Safety event ID |
| `driverId` | string | Driver ID |
| `vehicleId` | string | Vehicle ID |
| `eventType` | string | Type of safety event |
| `time` | string | Event timestamp (ISO 8601) |
| `location` | object | Event location |
| `behaviorLabel` | string | Behavior classification |
| `maxSpeedMph` | float | Maximum speed during event |
| `durationMs` | int | Event duration in milliseconds |
| `coachingState` | string | Coaching status |
