# Speeding Resource

Access speeding interval data for compliance and safety monitoring.

## Basic Usage

```php
use Samsara\Facades\Samsara;
use Carbon\Carbon;

// Get speeding intervals stream
$intervals = Samsara::speeding()
    ->intervalsStream()
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->get();
```

## Query Builder

```php
// Filter by vehicle
$intervals = Samsara::speeding()
    ->intervalsStream()
    ->whereVehicle('vehicle-123')
    ->between(now()->subDays(7), now())
    ->get();

// Filter by driver
$intervals = Samsara::speeding()
    ->intervalsStream()
    ->whereDriver('driver-456')
    ->between(now()->subDays(7), now())
    ->get();

// Filter by tag
$intervals = Samsara::speeding()
    ->intervalsStream()
    ->whereTag('delivery-fleet')
    ->between(now()->subDays(7), now())
    ->get();
```

## Lazy Loading

```php
// Stream through large datasets
Samsara::speeding()
    ->intervalsStream()
    ->between(now()->subMonth(), now())
    ->lazy(500)
    ->each(function ($interval) {
        // Process each speeding interval
        $maxSpeed = $interval->maxSpeedMph;
        $duration = $interval->durationMs;
    });
```
