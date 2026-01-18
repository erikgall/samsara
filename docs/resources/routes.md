# Routes Resource

Manage dispatch routes for drivers and vehicles.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all routes
$routes = Samsara::routes()->all();

// Find a route
$route = Samsara::routes()->find('route-id');

// Create a route
$route = Samsara::routes()->create([
    'name' => 'Delivery Route A',
    'driverId' => 'driver-123',
    'vehicleId' => 'vehicle-456',
    'scheduledStartTime' => '2024-01-15T08:00:00Z',
    'stops' => [
        [
            'addressId' => 'address-1',
            'scheduledArrivalTime' => '2024-01-15T09:00:00Z',
        ],
        [
            'addressId' => 'address-2',
            'scheduledArrivalTime' => '2024-01-15T10:30:00Z',
        ],
    ],
]);

// Update a route
$route = Samsara::routes()->update('route-id', [
    'name' => 'Updated Delivery Route',
]);

// Delete a route
Samsara::routes()->delete('route-id');
```

## Query Builder

```php
use Carbon\Carbon;

// Filter routes by driver
$routes = Samsara::routes()
    ->query()
    ->whereDriver('driver-123')
    ->get();

// Filter routes by tag
$routes = Samsara::routes()
    ->query()
    ->whereTag('delivery-routes')
    ->get();

// Get routes for a time range
$routes = Samsara::routes()
    ->query()
    ->between(Carbon::now(), Carbon::now()->addDays(7))
    ->get();
```

## Route Audit Logs

Track changes to routes with audit logs:

```php
// Get route audit logs feed
$auditLogs = Samsara::routes()->auditLogs()->get();

// Filter audit logs by time
$auditLogs = Samsara::routes()
    ->auditLogs()
    ->between(now()->subDays(7), now())
    ->get();
```

## Route Entity

```php
$route = Samsara::routes()->find('route-id');

$route->id;                  // string
$route->name;                // string
$route->driverId;            // ?string
$route->vehicleId;           // ?string
$route->scheduledStartTime;  // string
$route->scheduledEndTime;    // ?string
$route->actualStartTime;     // ?string
$route->actualEndTime;       // ?string
$route->stops;               // array
$route->status;              // string
```

## Available Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | Route ID |
| `name` | string | Route name |
| `driverId` | string | Assigned driver ID |
| `vehicleId` | string | Assigned vehicle ID |
| `scheduledStartTime` | string | Scheduled start (ISO 8601) |
| `scheduledEndTime` | string | Scheduled end (ISO 8601) |
| `actualStartTime` | string | Actual start (ISO 8601) |
| `actualEndTime` | string | Actual end (ISO 8601) |
| `stops` | array | List of route stops |
| `status` | string | Route status |
| `notes` | string | Route notes |
