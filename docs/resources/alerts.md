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

Configurations use `/alerts/configurations`; incidents use `/alerts/incidents/stream`. Configuration updates patch the collection payload with `id`; deletion sends one `id` query parameter per call. The resource owns generic `all` and `create`; other generic verbs are rejected locally in favor of the explicit methods below.

## Retrieving Records

### Alert Configurations

`configurations()` returns a query builder over `AlertConfiguration` records.

```php
use Samsara\Facades\Samsara;

$configurations = Samsara::alerts()->configurations()->get();

$config = $configurations->findById('config-id');
```

You may also use `query()` directly — it is the same builder.

### Alert Incidents

`incidents()` returns a no-implicit-limit builder of `AlertIncident` objects. An array passed as `configurationIds` is encoded with repeated keys.

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
    'name' => 'Speeding Alert',
    'isEnabled' => true,
    'scope' => ['all' => true],
    'triggers' => [/* current trigger payload */],
    'actions' => [/* current action payload */],
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

`deleteConfigurations()` accepts one string ID. Call it once for each configuration you want to remove.

```php
foreach (['config-id-1', 'config-id-2'] as $id) {
    Samsara::alerts()->deleteConfigurations($id);
}
```

## Filtering

Both sub-builders accept the standard query builder methods. See [the query builder reference](../query-builder.html) for the full method list.

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

The `Samsara\Enums\AlertType` enum lists the values the SDK ships for its `alertType` field. Samsara's current alert-configuration schema instead uses `actions`, `isEnabled`, `scope`, and `triggers`; treat this enum as package surface, not a complete current-upstream trigger contract.

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
$config = Samsara::alerts()->configurations()->where('ids', ['config-id'])->first();

$config->isEnabled();        // bool
$config->isSpeedType();      // bool — checks for 'speed'
$config->isGeofenceType();   // bool — checks for 'geofence'
$config->isIdleType();       // bool — checks for 'idle'
$config->isFuelLevelType();  // bool — checks for 'fuelLevel'
```

The legacy helper strings and `AlertType` enum values are not the current trigger contract. Prefer `triggers`, `actions`, and `scope` for current configurations.

`incidents()->get()` instantiates `AlertIncident` automatically. The current resolved flag is boolean:

```php
$incident->isResolved();   // bool
```

## Properties

The `AlertConfiguration` entity (`Samsara\Data\Alert\AlertConfiguration`) exposes the following typed properties.

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Alert configuration ID. |
| `name` | `?string` | Alert name. |
| `alertType` | `?string` | Alert type — see [Alert Types](#alert-types). |
| `description` | `?string` | Alert description. |
| `isEnabled` | `?bool` | Whether the alert is enabled. |
| `actions` | `?array` | Current notification actions. |
| `scope` | `?array` | Alert scope. |
| `triggers` | `?array` | Current trigger definitions. |

The incident builder returns `Samsara\Data\Alert\AlertIncident`:

| Property | Type | Description |
|----------|------|-------------|
| `configurationId` | `?string` | Configuration that triggered the incident. |
| `isResolved` | `?bool` | Current resolution flag. |
| `happenedAtTime` | `?string` | Incident time (RFC 3339). |
| `resolvedAtTime` | `?string` | Resolution time (RFC 3339). |
| `updatedAtTime` | `?string` | Last update time. |
| `incidentUrl` | `?string` | Cloud dashboard URL. |
| `conditions` | `?array` | Conditions captured for the incident. |

## Related Resources

- [Vehicles](vehicles.html) — vehicle records referenced from incidents.
- [Drivers](drivers.html) — driver records referenced from incidents.
- [Webhooks](webhooks.html) — receive alert incidents in real time.
- [Query Builder](../query-builder.html) — filters, pagination, and time-bounded queries.
- [Enums](../enums.html) — `AlertType` and other typed enums.
