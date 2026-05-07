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

The `Samsara::settings()` resource exposes the organization-wide settings that govern compliance, the driver app experience, and safety monitoring. All five methods on `SettingsResource` return raw decoded JSON (`array<string, mixed>`); there is no typed entity, no query builder, and no traditional CRUD — only paired GET/PATCH calls per settings group.

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
    'hosRuleset' => 'usa_70_8',
    'restBreakRequired' => true,
]);

$updated = Samsara::settings()->updateDriverApp([
    'allowManualDutyStatus' => true,
    'requireSignatureOnDelivery' => true,
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

- [Hours of Service](hours-of-service.md) — driven by the compliance ruleset.
- [Safety Events](safety-events.md) — events governed by safety configuration.
- [Drivers](drivers.md) — driver-app behaviour applies to every driver.
- [Error Handling](../error-handling.md) — exceptions raised by the underlying request.
