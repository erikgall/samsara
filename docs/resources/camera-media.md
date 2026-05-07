---
title: Camera Media
nav_order: 27
description: Request and retrieve dash camera footage from your fleet.
permalink: /resources/camera-media
---

# Camera Media

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Requesting Footage](#requesting-footage)
  - [The Request-Then-Poll Pattern](#the-request-then-poll-pattern)
- [Retrieval Parameters](#retrieval-parameters)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

Camera media exposes the dash camera footage Samsara records on safety events and on demand. Reach for this resource when you need to review video around an incident — a hard brake, a collision, or any safety event captured by a vehicle's camera. Footage is not returned synchronously: you submit a retrieval request, then poll for the status until the file is ready.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$media = Samsara::cameraMedia()->get();
```

The collection contains generic `Entity` instances describing the available media — read fields directly with `$item->mediaUrl`, `$item->cameraSerial`, etc.

## Requesting Footage

Submit a retrieval request with either a time range or a safety event ID.

```php
$retrieval = Samsara::cameraMedia()->retrieve([
    'cameraSerial' => 'camera-serial-123',
    'startTime'    => '2024-01-15T08:00:00Z',
    'endTime'      => '2024-01-15T08:30:00Z',
]);

$retrievalId = $retrieval['id'];
```

`retrieve()` returns the raw response array with the new retrieval's identifier, not a typed entity.

### The Request-Then-Poll Pattern

Camera media exports are asynchronous. The Samsara API returns a retrieval ID immediately, and you poll `getRetrieval()` until the status flips to `completed`.

```php
$status = Samsara::cameraMedia()->getRetrieval($retrievalId);

if ($status['status'] === 'completed') {
    $mediaUrl = $status['url'];
}
```

The same pattern applies to PDF exports on [forms](forms.md) and to CSV exports on the IFTA resource.

## Retrieval Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `cameraSerial` | `string` | Camera serial number. |
| `startTime` | `string` | Start time (RFC 3339). Pair with `endTime`. |
| `endTime` | `string` | End time (RFC 3339). Pair with `startTime`. |
| `eventId` | `string` | Safety event ID. Use as an alternative to a time range. |

## Properties

This resource does not declare a typed entity. `get()` returns generic `Samsara\Data\Entity` instances; `retrieve()` and `getRetrieval()` return raw `array<string, mixed>` payloads from the API.

## Related Resources

- [Safety Events](safety-events.md) — pair an event ID with `retrieve()` to grab its footage.
- [Vehicles](vehicles.md) — the camera serial is exposed on the vehicle entity.
- [Forms](forms.md) — the same request-then-poll pattern applies to PDF exports.
- [Error Handling](../error-handling.md) — exceptions raised by HTTP failures.
