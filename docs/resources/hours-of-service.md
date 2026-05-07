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

> **Note:** This resource is typed as `HosLog`. The `clocks()`, `violations()`, and `dailyLogs()` sub-builders return entities mapped to `HosLog` rather than to their own `HosClock`, `HosViolation`, and `HosDailyLog` classes. The properties documented in [Entities](#entities) describe the API response shape — read fields with `$row->get('drive')`, `$row->get('cycle')`, etc., or instantiate the typed entity yourself when you need its helpers.

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

> **Note:** HOS records are written by the Samsara driver app and ELD hardware. This resource does not support `create()`.

## Updating Records

> **Note:** This resource does not support `update()`.

## Deleting Records

> **Note:** This resource does not support `delete()`.

## Filtering

Every sub-builder accepts the standard query builder methods. See [the query builder reference](../query-builder.md) for the full method list.

```php
$logs = Samsara::hoursOfService()
    ->logs()
    ->whereDriver(['driver-1', 'driver-2'])
    ->between('2024-01-01', '2024-01-07')
    ->whereTag('tag-id')
    ->get();
```

## Duty Status

The `Samsara\Enums\DutyStatus` enum maps to the `hosStatusType` field on `HosLog`. See the [enum reference](../enums.md) for the canonical mapping.

| Case | Value | Description |
|------|-------|-------------|
| `OFF_DUTY` | `offDuty` | Driver is off duty. |
| `SLEEPER_BERTH` | `sleeperBerth` | Driver is in the sleeper berth. |
| `DRIVING` | `driving` | Driver is driving. |
| `ON_DUTY` | `onDuty` | Driver is on duty (not driving). |
| `YARD_MOVE` | `yardMove` | Driver is performing a yard move. |
| `PERSONAL_CONVEYANCE` | `personalConveyance` | Driver is using the vehicle for personal conveyance. |

## HOS Log Types

The `Samsara\Enums\HosLogType` enum lists the categorical types Samsara assigns to HOS log entries. See the [enum reference](../enums.md) for the canonical mapping.

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

`HosLog` represents a single duty-status log entry.

```php
$log = Samsara::hoursOfService()->logs()->first();

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

> **Note:** This builder is typed as `HosLog` due to a single `$entity` declaration on the resource. The properties below describe the API response shape; the typed `HosClock` helpers are not invoked by `clocks()->get()` automatically. Read the values with `$row->get('drive')['driveRemainingDurationMs']`, or instantiate `HosClock` yourself with the response array if you need the helpers.

`HosClock` represents the live remaining-time clock for a driver. All durations come back in **milliseconds**, not seconds.

```php
use Samsara\Data\HoursOfService\HosClock;

$clock = new HosClock($row->toArray());

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

> **Note:** This builder is typed as `HosLog`. The properties below describe the API response shape; the typed `HosViolation` helpers are not invoked by `violations()->get()` automatically. Read the violation type with `$row->get('type')` or instantiate `HosViolation` yourself when you need the helpers.

```php
use Samsara\Data\HoursOfService\HosViolation;

$violation = new HosViolation($row->toArray());

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

> **Note:** This builder is typed as `HosLog`. The properties below describe the API response shape returned by `dailyLogs()->get()`.

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

- [Drivers](drivers.md) — the driver side of every HOS record.
- [Vehicles](vehicles.md) — vehicle records referenced from logs.
- [Safety Events](safety-events.md) — pair safety events with HOS context.
- [Query Builder](../query-builder.md) — filters and pagination.
- [Enums](../enums.md) — `DutyStatus` and `HosLogType`.
