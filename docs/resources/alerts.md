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
- [Helper Methods](#helper-methods)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

Alerts cover two related concepts in the Samsara API: **alert configurations** define the rules that fire (a speed threshold, a geofence breach, a fuel-level drop), and **alert incidents** are the events those rules generate. The `Samsara::alerts()` resource exposes both. Reach for it when you need to manage what your fleet alerts on, or when you need to stream the incidents those alerts have produced.

Configurations use `/alerts/configurations`; incidents use `/alerts/incidents/stream`. Configuration updates patch the collection payload with `id`; deletion sends one `id` query parameter per call. The resource exposes `create()` and the builders and explicit methods below. Generic `all`, `find`, `update`, and `delete` methods are removed.

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

`incidents()` returns a no-implicit-limit builder of `AlertIncident` objects. Every request requires `startTime` and one to 50 `configurationIds`; an array of configuration IDs is encoded with repeated keys.

```php
$incidents = Samsara::alerts()
    ->incidents()
    ->where('configurationIds', ['config-id'])
    ->startTime(now()->subDays(7))
    ->get();

$recent = Samsara::alerts()
    ->incidents()
    ->where('configurationIds', ['config-id'])
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

The current create contract requires `actions`, `isEnabled`, `name`, `scope`, and `triggers`. It does not accept the legacy flat `alertType`, `threshold`, or `tagIds` shape.

## Updating Records

```php
$config = Samsara::alerts()->updateConfiguration('config-id', [
    'name' => 'Updated Speeding Alert',
    'isEnabled' => true,
    'scope' => ['all' => true],
    'triggers' => [/* current trigger payload */],
    'actions' => [/* current action payload */],
]);
```

The helper adds `id` to the collection PATCH body. Supported update fields are current configuration fields such as `actions`, `externalIds`, `isEnabled`, `name`, `operationalSettings`, `scope`, and `triggers`.

## Deleting Records

`deleteConfigurations()` accepts one string ID. Call it once for each configuration you want to remove.

```php
foreach (['config-id-1', 'config-id-2'] as $id) {
    Samsara::alerts()->deleteConfigurations($id);
}
```

## Filtering

Use only parameters declared by the selected endpoint. Configuration reads support IDs and current status/pagination parameters. Incident reads require both `startTime` and `configurationIds`; `endTime` and `after` are optional.

```php
$configurations = Samsara::alerts()
    ->configurations()
    ->where('ids', ['config-id'])
    ->get();

$incidents = Samsara::alerts()
    ->incidents()
    ->where('configurationIds', ['config-id'])
    ->between(now()->subDays(7), now())
    ->get();
```

## Helper Methods

Current configuration responses support the enabled-state helper:

```php
$config = Samsara::alerts()->configurations()->where('ids', ['config-id'])->first();

$config->isEnabled(); // bool
```

`isSpeedType()`, `isGeofenceType()`, `isIdleType()`, and `isFuelLevelType()` are legacy compatibility surface: they read the absent legacy `alertType` field and are not valid for current responses. Inspect `triggers`, `scope`, and `operationalSettings` instead. The package's `AlertType` enum is likewise legacy surface, not the current configuration request contract.

`incidents()->get()` instantiates `AlertIncident` automatically. The current resolved flag is boolean:

```php
$incident->isResolved();   // bool
```

Do not use deprecated `isTriggered()` or `isDismissed()` on current incident responses. The API no longer returns their legacy string `status`; there is no direct equivalent.

## Properties

The `AlertConfiguration` entity (`Samsara\Data\Alert\AlertConfiguration`) exposes the following typed properties.

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Alert configuration ID. |
| `name` | `?string` | Alert name. |
| `isEnabled` | `?bool` | Whether the alert is enabled. |
| `actions` | `?array` | Current notification actions. |
| `externalIds` | `?array<string, string>` | External identifier map. |
| `operationalSettings` | `?array` | Current operational settings. |
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
