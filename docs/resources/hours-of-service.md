# Hours of Service Resource

Manage HOS logs, clocks, and violations.

## Basic Usage

```php
use ErikGall\Samsara\Facades\Samsara;

// Get HOS logs
$logs = Samsara::hoursOfService()->logs()->get();

// Get HOS clocks
$clocks = Samsara::hoursOfService()->clocks()->get();

// Get HOS violations
$violations = Samsara::hoursOfService()->violations()->get();

// Get daily logs
$dailyLogs = Samsara::hoursOfService()->dailyLogs()->get();
```

## HOS Logs

```php
// Get logs for specific drivers
$logs = Samsara::hoursOfService()
    ->logs()
    ->whereDriver(['driver-1', 'driver-2'])
    ->get();

// Get logs for a time range
$logs = Samsara::hoursOfService()
    ->logs()
    ->between('2024-01-01', '2024-01-07')
    ->get();

// Filter by tag
$logs = Samsara::hoursOfService()
    ->logs()
    ->whereTag('tag-id')
    ->get();
```

## HOS Clocks

Get real-time HOS clock data:

```php
// Get clocks for all drivers
$clocks = Samsara::hoursOfService()->clocks()->get();

// Get clocks for specific drivers
$clocks = Samsara::hoursOfService()
    ->clocks()
    ->whereDriver('driver-id')
    ->get();
```

## HOS Violations

```php
// Get all violations
$violations = Samsara::hoursOfService()->violations()->get();

// Get violations for specific drivers
$violations = Samsara::hoursOfService()
    ->violations()
    ->whereDriver('driver-id')
    ->between('2024-01-01', '2024-01-31')
    ->get();
```

## Daily Logs

```php
// Get daily logs
$dailyLogs = Samsara::hoursOfService()
    ->dailyLogs()
    ->whereDriver('driver-id')
    ->between('2024-01-01', '2024-01-07')
    ->get();
```

## Duty Status

Use the `DutyStatus` enum:

```php
use ErikGall\Samsara\Enums\DutyStatus;

DutyStatus::OFF_DUTY;           // 'offDuty'
DutyStatus::SLEEPER_BERTH;      // 'sleeperBerth'
DutyStatus::DRIVING;            // 'driving'
DutyStatus::ON_DUTY;            // 'onDuty'
DutyStatus::YARD_MOVE;          // 'yardMove'
DutyStatus::PERSONAL_CONVEYANCE; // 'personalConveyance'
```

## HosLog Entity

```php
$log = Samsara::hoursOfService()->logs()->first();

$log->id;           // string
$log->driverId;     // string
$log->dutyStatus;   // string
$log->startTime;    // string
$log->endTime;      // ?string
$log->vehicleId;    // ?string
$log->location;     // ?array
$log->notes;        // ?string
```

## HosClock Entity

```php
$clock = Samsara::hoursOfService()->clocks()->first();

$clock->driverId;           // string
$clock->currentDutyStatus;  // string
$clock->driveRemaining;     // ?int (seconds)
$clock->shiftRemaining;     // ?int (seconds)
$clock->cycleRemaining;     // ?int (seconds)
$clock->breakRemaining;     // ?int (seconds)
```

## HosViolation Entity

```php
$violation = Samsara::hoursOfService()->violations()->first();

$violation->id;           // string
$violation->driverId;     // string
$violation->type;         // string
$violation->startTime;    // string
$violation->endTime;      // ?string
```
