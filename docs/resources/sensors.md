---
title: Sensors
layout: default
parent: Resources
nav_order: 21
description: "Access legacy v1 sensor endpoints"
permalink: /resources/sensors
---

# Sensors Resource (Legacy)

Access legacy v1 sensor endpoints for temperature, humidity, door status, and cargo monitoring.

> **Note:** This resource uses the legacy v1 API endpoints.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// List all sensors
$sensors = Samsara::sensors()->list([
    'groupId' => 'group-123',
]);
```

## Sensor Data Methods

### Temperature

```php
$temperatures = Samsara::sensors()->temperature([
    'groupId' => 'group-123',
    'sensors' => [
        ['id' => 'sensor-1'],
        ['id' => 'sensor-2'],
    ],
]);
```

### Humidity

```php
$humidity = Samsara::sensors()->humidity([
    'groupId' => 'group-123',
    'sensors' => [
        ['id' => 'sensor-1'],
    ],
]);
```

### Door Status

```php
$doorStatus = Samsara::sensors()->door([
    'groupId' => 'group-123',
    'sensors' => [
        ['id' => 'sensor-1'],
    ],
]);
```

### Cargo Status

```php
$cargoStatus = Samsara::sensors()->cargo([
    'groupId' => 'group-123',
    'sensors' => [
        ['id' => 'sensor-1'],
    ],
]);
```

### Sensor History

```php
$history = Samsara::sensors()->history([
    'groupId' => 'group-123',
    'sensors' => [
        [
            'id' => 'sensor-1',
            'series' => ['temperature', 'humidity'],
        ],
    ],
    'startMs' => 1609459200000,
    'endMs' => 1609545600000,
    'stepMs' => 3600000,
]);
```

## Common Use Cases

### Monitor Refrigerated Trailer Temperature

```php
// Get current temperature readings
$temperatures = Samsara::sensors()->temperature([
    'groupId' => $groupId,
    'sensors' => [
        ['id' => $temperatureSensorId],
    ],
]);

foreach ($temperatures['sensors'] as $sensor) {
    $tempF = $sensor['ambientTemperature'];

    if ($tempF > 40) {
        // Alert: Temperature too high for cold storage
        Log::warning("Sensor {$sensor['id']} temperature alert: {$tempF}F");
    }
}
```

### Track Door Open/Close Events

```php
$doorStatus = Samsara::sensors()->door([
    'groupId' => $groupId,
    'sensors' => array_map(fn ($id) => ['id' => $id], $doorSensorIds),
]);

foreach ($doorStatus['sensors'] as $sensor) {
    if ($sensor['doorClosed'] === false) {
        Log::info("Door sensor {$sensor['id']} reports door is OPEN");
    }
}
```
