---
title: Alerts
nav_order: 22
description: Manage alert configurations and stream alert incidents.
permalink: /resources/alerts
---

# Alerts

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
  - [Alert Configurations](#alert-configurations)
  - [Alert Incidents](#alert-incidents)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Filtering](#filtering)
- [Alert Types](#alert-types)
- [Helper Methods](#helper-methods)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

Alerts cover two related concepts in the Samsara API: **alert configurations** define the rules that fire (a speed threshold, a geofence breach, a fuel-level drop), and **alert incidents** are the events those rules generate. The `Samsara::alerts()` resource exposes both. Reach for it when you need to manage what your fleet alerts on, or when you need to stream the incidents those alerts have produced.

## Retrieving Records

### Alert Configurations

`configurations()` returns a query builder over `AlertConfiguration` records.

```php
use Samsara\Facades\Samsara;

$configurations = Samsara::alerts()->configurations()->get();

$config = Samsara::alerts()->configurations()->find('config-id');
```

You may also use `query()` directly — it is the same builder.

### Alert Incidents

`incidents()` returns a query builder over the incident stream. The records come back as generic `Samsara\Data\Entity` instances.

```php
$incidents = Samsara::alerts()->incidents()->get();

$recent = Samsara::alerts()
    ->incidents()
    ->between(now()->subDays(7), now())
    ->get();
```

## Creating Records

```php
$config = Samsara::alerts()->createConfiguration([
    'name'      => 'Speeding Alert',
    'alertType' => 'speed',
    'threshold' => 75,
    'tagIds'    => ['tag-123'],
]);
```

> **Note:** The `alertType` accepts the string value the Samsara API expects. The set of values the SDK exposes through the `AlertType` enum is documented under [Alert Types](#alert-types) below.

## Updating Records

```php
$config = Samsara::alerts()->updateConfiguration('config-id', [
    'name'      => 'Updated Speeding Alert',
    'threshold' => 80,
]);
```

## Deleting Records

`deleteConfigurations()` accepts an array of IDs and removes them in a single call.

```php
Samsara::alerts()->deleteConfigurations(['config-id-1', 'config-id-2']);
```

## Filtering

Both sub-builders accept the standard query builder methods. See [the query builder reference](../query-builder.md) for the full method list.

```php
$configurations = Samsara::alerts()
    ->configurations()
    ->whereTag('safety-alerts')
    ->limit(25)
    ->get();

$incidents = Samsara::alerts()
    ->incidents()
    ->whereTag('monitored-fleet')
    ->between(now()->subDays(7), now())
    ->get();
```

## Alert Types

The `Samsara\Enums\AlertType` enum lists the values the SDK ships for the `alertType` field. Cross-link to the [enum reference](../enums.md) for the full mapping.

| Case | Value | Description |
|------|-------|-------------|
| `BATTERY` | `battery` | Battery level alert. |
| `CARGO` | `cargo` | Cargo sensor alert. |
| `CUSTOM` | `custom` | Custom alert configuration. |
| `DOOR` | `door` | Door open/closed alert. |
| `ENGINE_FAULT` | `engineFault` | Engine fault code alert. |
| `FUEL` | `fuel` | Fuel-level alert. |
| `GEOFENCE` | `geofence` | Geofence entry/exit alert. |
| `HOS_VIOLATION` | `hosViolation` | Hours-of-service violation alert. |
| `HUMIDITY` | `humidity` | Humidity sensor alert. |
| `IDLE` | `idle` | Idle alert. |
| `MAINTENANCE` | `maintenance` | Maintenance reminder alert. |
| `PANIC` | `panic` | Panic button alert. |
| `POWER` | `power` | Power loss alert. |
| `SAFETY_EVENT` | `safetyEvent` | Safety event alert. |
| `SPEEDING` | `speeding` | Speeding alert. |
| `TEMPERATURE` | `temperature` | Temperature sensor alert. |

## Helper Methods

The `AlertConfiguration` entity exposes type-check helpers that read the `alertType` field directly:

```php
$config = Samsara::alerts()->configurations()->find('config-id');

$config->isEnabled();        // bool
$config->isSpeedType();      // bool — checks for 'speed'
$config->isGeofenceType();   // bool — checks for 'geofence'
$config->isIdleType();       // bool — checks for 'idle'
$config->isFuelLevelType();  // bool — checks for 'fuelLevel'
```

The `AlertIncident` entity exposes status helpers:

```php
$incident->isTriggered();  // bool
$incident->isResolved();   // bool
$incident->isDismissed();  // bool
```

## Properties

The `AlertConfiguration` entity (`Samsara\Data\Alert\AlertConfiguration`) exposes the following typed properties.

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Alert configuration ID. |
| `name` | `?string` | Alert name. |
| `alertType` | `?string` | Alert type — see [Alert Types](#alert-types). |
| `description` | `?string` | Alert description. |
| `enabled` | `?bool` | Whether the alert is enabled. |
| `notificationSettings` | `?array` | `{email?, sms?}` — channel toggles. |
| `tagIds` | `?array<int, string>` | Tag IDs the alert is scoped to. |

The `AlertIncident` entity (`Samsara\Data\Alert\AlertIncident`) is returned by the incidents stream:

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Alert incident ID. |
| `alertConfigurationId` | `?string` | Configuration that triggered the incident. |
| `status` | `?string` | `triggered`, `resolved`, or `dismissed`. |
| `triggeredAtTime` | `?string` | Trigger time (RFC 3339). |
| `resolvedAtTime` | `?string` | Resolution time (RFC 3339). |
| `driver` | `?array` | Associated driver — `{id, name?}`. |
| `vehicle` | `?array` | Associated vehicle — `{id, name?}`. |
| `location` | `?array` | Incident location — `{latitude?, longitude?}`. |

## Related Resources

- [Vehicles](vehicles.md) — vehicle records referenced from incidents.
- [Drivers](drivers.md) — driver records referenced from incidents.
- [Webhooks](webhooks.md) — receive alert incidents in real time.
- [Query Builder](../query-builder.md) — filters, pagination, and time-bounded queries.
- [Enums](../enums.md) — `AlertType` and other typed enums.
