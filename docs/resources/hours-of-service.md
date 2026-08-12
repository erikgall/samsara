---
title: Hours of Service
nav_order: 8
description: Read HOS logs, clocks, daily logs, and violations for your drivers.
permalink: /resources/hours-of-service
---

# Hours of Service

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
  - [HOS Logs](#hos-logs)
  - [HOS Clocks](#hos-clocks)
  - [HOS Violations](#hos-violations)
  - [HOS Daily Logs](#hos-daily-logs)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Filtering](#filtering)
- [Duty Status](#duty-status)
- [HOS Log Types](#hos-log-types)
- [Entities](#entities)
  - [HosLog Entity](#hoslog-entity)
  - [HosClock Entity](#hosclock-entity)
  - [HosViolation Entity](#hosviolation-entity)
  - [HosDailyLog Entity](#hosdailylog-entity)
- [Related Resources](#related-resources)

## Introduction

Hours of Service (HOS) data is the regulatory record of when your drivers were driving, on duty, or resting. The `Samsara::hoursOfService()` resource exposes four sub-builders — `logs()`, `clocks()`, `violations()`, and `dailyLogs()` — each returning a query builder over a different HOS endpoint. Reach for HOS data when you need to surface real-time clocks in a dashboard, audit a driver's history for compliance, or react to a violation.

Each endpoint selects its own response type. `logs()` returns `HosLogsForDriver`, `clocks()` returns `HosClocksForDriver`, `violations()` returns `HosViolationGroup`, and `dailyLogs()` returns `HosDailyLog`. The wrapper helpers documented under [Entities](#entities) expose nested `HosLog`, `HosClock`, and `HosViolation` objects.

## Retrieving Records

### HOS Logs

```php
use Samsara\Facades\Samsara;

$logs = Samsara::hoursOfService()->logs()->get();
```

`logs()` is the default builder for this resource, so `Samsara::hoursOfService()->query()` and `Samsara::hoursOfService()->logs()` return the same `Builder`.

### HOS Clocks

Clocks are the live remaining-time values for each driver — drive, shift, cycle, and break.

```php
$clocks = Samsara::hoursOfService()->clocks()->get();

$forDriver = Samsara::hoursOfService()
    ->clocks()
    ->whereDriver('driver-id')
    ->get();
```

### HOS Violations

```php
$violations = Samsara::hoursOfService()->violations()->get();

$forDriver = Samsara::hoursOfService()
    ->violations()
    ->whereDriver('driver-id')
    ->between('2024-01-01', '2024-01-31')
    ->get();
```

### HOS Daily Logs

```php
$dailyLogs = Samsara::hoursOfService()
    ->dailyLogs()
    ->whereDriver('driver-id')
    ->between('2024-01-01', '2024-01-07')
    ->get();
```

## Creating Records

Only generic `all()` is supported. `create()` fails locally before HTTP.

## Updating Records

Generic `update()` fails locally before HTTP.

## Deleting Records

Generic `delete()` fails locally before HTTP.

## Filtering

Every sub-builder accepts the standard query builder methods. See [the query builder reference](../query-builder.html) for the full method list.

```php
$logs = Samsara::hoursOfService()
    ->logs()
    ->whereDriver(['driver-1', 'driver-2'])
    ->between('2024-01-01', '2024-01-07')
    ->whereTag('tag-id')
    ->get();
```

## Duty Status

`HosLog::getDutyStatus()` maps the documented `hosStatusType` values to `Samsara\Enums\DutyStatus`, including the upstream `sleeperBed` value. See the [enum reference](../enums.html).

| Case | Value | Description |
|------|-------|-------------|
| `OFF_DUTY` | `offDuty` | Driver is off duty. |
| `SLEEPER_BERTH` | `sleeperBed` | Driver is in the sleeper berth. |
| `DRIVING` | `driving` | Driver is driving. |
| `ON_DUTY` | `onDuty` | Driver is on duty (not driving). |
| `YARD_MOVE` | `yardMove` | Driver is performing a yard move. |
| `PERSONAL_CONVEYANCE` | `personalConveyance` | Driver is using the vehicle for personal conveyance. |

## HOS Log Types

The `Samsara\Enums\HosLogType` enum lists the categorical types Samsara assigns to HOS log entries. See the [enum reference](../enums.html) for the canonical mapping.

| Case | Value | Description |
|------|-------|-------------|
| `CERTIFICATION` | `certification` | Driver certification of logs. |
| `DIAGNOSTIC_MALFUNCTION` | `diagnosticMalfunction` | ELD diagnostic or malfunction event. |
| `DRIVER_INDICATION` | `driverIndication` | Driver indication of an event. |
| `DUTY_STATUS` | `dutyStatus` | Duty status change. |
| `INTERMEDIATE` | `intermediate` | Intermediate log entry. |
| `LOGIN_LOGOUT` | `loginLogout` | Driver login or logout. |
| `POWER_UP_DOWN` | `powerUpDown` | Vehicle power on/off event. |
| `REMARK` | `remark` | Driver remark. |
| `SHIPPING_DOCUMENT` | `shippingDocument` | Shipping document log entry. |

## Entities

### HosLog Entity

`HosLog` represents one duty-status entry nested in a `HosLogsForDriver` response.

```php
$logsForDriver = Samsara::hoursOfService()->logs()->first();
$log = $logsForDriver->getLogs()->first();

$log->getDutyStatus();          // ?DutyStatus
$log->isDriving();              // bool
$log->isOffDuty();              // bool
$log->isOnDuty();               // bool
$log->isPersonalConveyance();   // bool
$log->isSleeperBerth();         // bool — checks API string 'sleeperBed'
$log->isYardMove();             // bool
```

| Property | Type | Description |
|----------|------|-------------|
| `hosStatusType` | `?string` | Duty status string. Use `getDutyStatus()` for the typed enum. |
| `logStartTime` | `?string` | Log start time (RFC 3339). |
| `logEndTime` | `?string` | Log end time (RFC 3339). |
| `remark` | `?string` | Remark associated with the log entry. |
| `vehicle` | `?array` | Vehicle information — `{id?, name?}`. Read the ID with `$log->vehicle['id']`. |
| `codrivers` | `?array` | Codriver information. Each entry is `{id?, name?}`. |
| `logRecordedLocation` | `?array` | Location where the log was recorded — `{latitude?, longitude?, location?}`. |

### HosClock Entity

`clocks()->get()` returns `HosClocksForDriver` wrappers. Call `$row->getClocks()` for a typed `HosClock`.

`HosClock` represents the live remaining-time clock for a driver. All durations come back in **milliseconds**, not seconds.

```php
$clock = $row->getClocks();

$clock->getDriveRemainingHours();  // ?float
$clock->getShiftRemainingHours();  // ?float
$clock->getCycleRemainingHours();  // ?float
$clock->getCycleTomorrowHours();   // ?float
$clock->getTimeUntilBreakHours();  // ?float
```

| Property | Type | Description |
|----------|------|-------------|
| `drive` | `?array` | Drive clock — `{driveRemainingDurationMs?}`. |
| `shift` | `?array` | Shift clock — `{shiftRemainingDurationMs?}`. |
| `cycle` | `?array` | Cycle clock — `{cycleRemainingDurationMs?, cycleStartedAtTime?, cycleTomorrowDurationMs?}`. |
| `break` | `?array` | Break clock — `{timeUntilBreakDurationMs?}`. |

### HosViolation Entity

`violations()->get()` returns `HosViolationGroup` wrappers. Call `$group->getViolations()` for typed `HosViolation` objects.

```php
$violation = $group->getViolations()->first();

$violation->getDurationHours();              // ?float
$violation->getDurationMinutes();            // ?float
$violation->isCycleHoursViolation();         // bool
$violation->isRestBreakMissedViolation();    // bool
$violation->isShiftDrivingHoursViolation();  // bool
$violation->isShiftHoursViolation();         // bool
$violation->isUnsubmittedLogsViolation();    // bool
```

| Property | Type | Description |
|----------|------|-------------|
| `type` | `?string` | Violation type. |
| `description` | `?string` | Violation description. |
| `durationMs` | `?int` | Duration of the violation in milliseconds. |
| `violationStartTime` | `?string` | Violation start time (RFC 3339). |
| `driver` | `?array` | Driver information — `{id?, name?}`. |
| `day` | `?array` | Day information — `{date?, timezone?}`. |

### HosDailyLog Entity

`dailyLogs()->get()` returns `HosDailyLog` directly. `logs()` and `query()` return `HosLogsForDriver` wrappers; call `getLogs()` for typed `HosLog` entries.

| Property | Type | Description |
|----------|------|-------------|
| `startTime` | `?string` | Start of the daily log (RFC 3339). |
| `endTime` | `?string` | End of the daily log (RFC 3339). |
| `driver` | `?array` | Driver information — `{id?, name?}`. |
| `distanceTraveled` | `?array` | `{distanceMeters?}`. |
| `dutyStatusDurations` | `?array` | `{driveDurationMs?, onDutyDurationMs?, offDutyDurationMs?, sleeperBerthDurationMs?}`. |
| `pendingDutyStatusDurations` | `?array` | `{driveDurationMs?, onDutyDurationMs?}`. |
| `logMetaData` | `?array` | `{shippingId?}`. |

## Related Resources

- [Drivers](drivers.html) — the driver side of every HOS record.
- [Vehicles](vehicles.html) — vehicle records referenced from logs.
- [Safety Events](safety-events.html) — pair safety events with HOS context.
- [Query Builder](../query-builder.html) — filters and pagination.
- [Enums](../enums.html) — `DutyStatus` and `HosLogType`.
