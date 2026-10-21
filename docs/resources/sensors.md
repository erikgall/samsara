---
title: Sensors
nav_order: 21
description: Legacy v1 endpoints for temperature, humidity, door, and cargo sensors.
permalink: /resources/sensors
---

# Sensors

- [Introduction](#introduction)
- [Listing Sensors](#listing-sensors)
- [Reading Sensor Values](#reading-sensor-values)
- [Sensor History](#sensor-history)
- [Method Reference](#method-reference)
- [Common Use Cases](#common-use-cases)
- [Related Resources](#related-resources)

## Introduction

The `Samsara::sensors()` resource calls Samsara's legacy v1 sensor endpoints — `/v1/sensors/...` — for temperature, humidity, door, and cargo monitoring. These endpoints predate the v2 API: every method except `list()` returns the raw decoded JSON (`array<string, mixed>`) so your application can read whatever shape Samsara hands back. Reach for it only when monitoring legacy industrial sensors that have not been migrated to v2.

> **Warning:** These endpoints proxy v1 routes. Treat the response shapes as Samsara documents them today; the SDK does not normalize them.

## Listing Sensors

`list()` is the only method that returns typed entities — an `EntityCollection<int, Entity>` populated with generic `Fluent` instances (the resource has no `$entity` declaration):

```php
use Samsara\Facades\Samsara;

$sensors = Samsara::sensors()->list();

foreach ($sensors as $sensor) {
    logger()->info('Sensor', $sensor->toArray());
}
```

## Reading Sensor Values

The four reading methods (`temperature`, `humidity`, `door`, `cargo`) all post to `/v1/sensors/{kind}` and return raw arrays:

```php
$temperatures = Samsara::sensors()->temperature(['sensors' => [123, 456]]);
$humidity = Samsara::sensors()->humidity(['sensors' => [123]]);
$doorStatus = Samsara::sensors()->door(['sensors' => [123]]);
$cargoStatus = Samsara::sensors()->cargo(['sensors' => [123]]);
```

Read keys from the response array directly (`$temperatures['sensors']`); no helper methods are available because the response is not wrapped in a typed entity.

## Sensor History

`history()` accepts `startMs`, `endMs`, `stepMs`, and a top-level `series` array of `widgetId`/`field` pairs:

```php
$history = Samsara::sensors()->history([
    'series' => [
        ['widgetId' => 123, 'field' => 'ambientTemperature'],
        ['widgetId' => 123, 'field' => 'humidity'],
    ],
    'startMs' => now()->subDay()->getTimestampMs(),
    'endMs' => now()->getTimestampMs(),
    'stepMs' => 3_600_000,
]);
```

## Method Reference

| Method | Returns | Description |
|--------|---------|-------------|
| `list()` | `EntityCollection<int, Entity>` | List sensors. |
| `temperature(array $data)` | `array` | Latest temperature readings. |
| `humidity(array $data)` | `array` | Latest humidity readings. |
| `door(array $data)` | `array` | Door open/closed status. |
| `cargo(array $data)` | `array` | Cargo presence status. |
| `history(array $data)` | `array` | Time-series readings for one or more series. |

## Common Use Cases

### Watch Refrigerated Trailer Temperature

```php
$temperatureSensorId = 123;
$readings = Samsara::sensors()->temperature([
    'sensors' => [$temperatureSensorId],
]);

foreach ($readings['sensors'] as $sensor) {
    $fahrenheit = ($sensor['ambientTemperature'] / 1000) * 9 / 5 + 32;
    if ($fahrenheit > 40) {
        Log::warning("Sensor {$sensor['id']} temperature alert: {$fahrenheit}F");
    }
}
```

### Track Door Open and Close

```php
$doorSensorIds = [123, 456];
$doorStatus = Samsara::sensors()->door([
    'sensors' => $doorSensorIds,
]);

foreach ($doorStatus['sensors'] as $sensor) {
    if ($sensor['doorClosed'] === false) {
        Log::info("Door sensor {$sensor['id']} reports door is OPEN");
    }
}
```

## Related Resources

- [Industrial](industrial.html) — v2 industrial assets, data inputs, and data points.
- [Assets](assets.html) — non-vehicle trackers managed via v2.
- [Error Handling](../error-handling.html) — exceptions raised by the underlying request.
