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

Camera media exposes the dash camera footage Samsara records on safety events and on demand. Reach for this resource when you need to review video around an incident — a hard brake, a collision, or any safety event captured by a vehicle's camera. Footage is not returned synchronously: submit a retrieval request, then poll until each returned media item is available or reaches another terminal state.

The explicit helpers implement `GET /cameras/media` and `GET|POST /cameras/media/retrieval`. No generic CRUD capability is supported.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$response = Samsara::cameraMedia()->get([
    'vehicleIds' => 'vehicle-id',
    'startTime' => $start,
    'endTime' => $end,
]);

$media = $response['data']['media'];
```

`get(array $parameters): array` preserves the complete response envelope, including nested `data.media` and top-level `pagination`. The `inputs`, `mediaTypes`, and `triggerReasons` arrays are serialized as repeated query keys.

For fluent pagination, use `query()`. It selects the nested media collection while preserving the API's top-level cursor metadata:

```php
$page = Samsara::cameraMedia()
    ->query()
    ->between($start, $end)
    ->whereVehicle('vehicle-id')
    ->paginate();

foreach ($page as $media) {
    // Generic Entity rows from data.media.
}
```

The endpoint does not declare `limit`, so `query()` never adds the configured page size. Keep using `get()` when you need the complete raw envelope.

## Requesting Footage

Submit a retrieval request for one vehicle, time range, and media type. Depending on the installed hardware, add `inputs` or `cameraRoles` to select camera views.

```php
$retrieval = Samsara::cameraMedia()->retrieve([
    'vehicleId'    => 'vehicle-123',
    'startTime'    => '2024-01-15T08:00:00Z',
    'endTime'      => '2024-01-15T08:00:30Z',
    'mediaType'    => 'videoHighRes',
    'inputs'       => ['dashcamRoadFacing'],
]);

$retrievalId = $retrieval['retrievalId'];
```

`vehicleId`, `startTime`, `endTime`, and `mediaType` are required. `retrieve()` returns the unwrapped response data with `retrievalId` and `quotaStatus`, not a typed entity.

### The Request-Then-Poll Pattern

Camera media retrieval is asynchronous. Poll `getRetrieval()` with `retrievalId`; its unwrapped response contains `media`, and each media item has its own `status`. Read the signed URL only when that item is `available`.

```php
$result = Samsara::cameraMedia()->getRetrieval($retrievalId);

foreach ($result['media'] ?? [] as $media) {
    if ($media['status'] === 'available') {
        $mediaUrl = $media['urlInfo']['url'];
    }
}
```

Other item statuses are `pending`, `invalid`, `failed`, and `unavailable`. Signed URLs expire; poll the GET endpoint again for a fresh URL after expiration.

The same pattern applies to PDF exports on [forms](forms.html) and to CSV exports on the IFTA resource.

## Retrieval Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `vehicleId` | `string` | Required Samsara vehicle ID. |
| `startTime` | `string` | Required start time (RFC 3339). |
| `endTime` | `string` | Required end time (RFC 3339); duration must satisfy the selected media type. |
| `mediaType` | `string` | Required: `image`, `videoHighRes`, `videoLowRes`, or `hyperlapse`. |
| `inputs` | `array<string>` | Optional camera inputs such as `dashcamRoadFacing`; at least one of `inputs` or `cameraRoles` is needed to select an installed view. |
| `cameraRoles` | `array<string>` | Optional camera roles for supported auxiliary-camera hardware. |

## Properties

This resource has no domain-specific entity type. `query()` maps media rows to generic `Entity` objects; `get()` returns the full raw envelope; `retrieve()` and `getRetrieval()` return the raw `data` payload when present.

## Related Resources

- [Safety Events](safety-events.html) — use an event's vehicle and time window to construct the retrieval request; do not send the event ID.
- [Vehicles](vehicles.html) — the camera serial is exposed on the vehicle entity.
- [Forms](forms.html) — the same request-then-poll pattern applies to PDF exports.
- [Error Handling](../error-handling.html) — exceptions raised by HTTP failures.
