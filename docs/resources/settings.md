---
title: Settings
nav_order: 33
description: Organization compliance, driver app, and safety settings.
permalink: /resources/settings
---

# Settings

- [Introduction](#introduction)
- [Reading Settings](#reading-settings)
- [Updating Settings](#updating-settings)
- [Method Reference](#method-reference)
- [Related Resources](#related-resources)

## Introduction

The five explicit methods on `SettingsResource` return raw decoded JSON (`array<string, mixed>`); there is no typed entity, query builder, or supported generic CRUD operation.

The helpers use `/fleet/settings/compliance`, `/fleet/settings/driver-app`, and `/fleet/settings/safety`.

## Reading Settings

```php
use Samsara\Facades\Samsara;

$compliance = Samsara::settings()->compliance();
$driverApp = Samsara::settings()->driverApp();
$safety = Samsara::settings()->safety();
```

Each call returns the upstream payload. Keys vary by settings group; treat them as documented by Samsara.

## Updating Settings

Compliance and driver-app settings are mutable; safety settings are read-only.

```php
$updated = Samsara::settings()->updateCompliance([
    'driverAutoDutyEnabled' => true,
    'forceReviewUnassignedHosEnabled' => true,
]);

$updated = Samsara::settings()->updateDriverApp([
    'orgVehicleSearch' => true,
    'trailerSelection' => true,
]);
```

> **Note:** There is no `updateSafety()` method. Safety configuration is read-only through this resource — manage it from the Samsara dashboard.

## Method Reference

| Method | Returns | Description |
|--------|---------|-------------|
| `compliance()` | `array` | Read compliance settings. |
| `updateCompliance(array $data)` | `array` | Patch compliance settings. |
| `driverApp()` | `array` | Read driver-app settings. |
| `updateDriverApp(array $data)` | `array` | Patch driver-app settings. |
| `safety()` | `array` | Read safety settings (read-only). |

## Related Resources

- [Hours of Service](hours-of-service.html) — driven by the compliance ruleset.
- [Safety Events](safety-events.html) — events governed by safety configuration.
- [Drivers](drivers.html) — driver-app behaviour applies to every driver.
- [Error Handling](../error-handling.html) — exceptions raised by the underlying request.
