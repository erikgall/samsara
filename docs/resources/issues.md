# Issues Resource

Access vehicle and equipment issue tracking data.

## Basic Usage

```php
use Samsara\Facades\Samsara;
use Carbon\Carbon;

// Get issues stream
$issues = Samsara::issues()
    ->stream()
    ->between(Carbon::now()->subDays(30), Carbon::now())
    ->get();
```

## Query Builder

```php
// Filter by vehicle
$issues = Samsara::issues()
    ->stream()
    ->whereVehicle('vehicle-123')
    ->between(now()->subDays(30), now())
    ->get();

// Filter by tag
$issues = Samsara::issues()
    ->stream()
    ->whereTag('maintenance-required')
    ->between(now()->subDays(30), now())
    ->get();

// Limit results
$issues = Samsara::issues()
    ->stream()
    ->limit(100)
    ->get();
```

## Lazy Loading

```php
// Stream through large datasets
Samsara::issues()
    ->stream()
    ->between(now()->subYear(), now())
    ->lazy(500)
    ->each(function ($issue) {
        // Process each issue
    });
```
