# Camera Media Resource

Retrieve camera media from dash cameras and other vehicle-mounted cameras.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get camera media
$media = Samsara::cameraMedia()->get();
```

## Media Retrieval

Request and retrieve specific camera footage:

```php
// Request media retrieval
$retrieval = Samsara::cameraMedia()->retrieve([
    'cameraSerial' => 'camera-serial-123',
    'startTime' => '2024-01-15T08:00:00Z',
    'endTime' => '2024-01-15T08:30:00Z',
]);

// Get the retrieval ID
$retrievalId = $retrieval['id'];

// Check retrieval status
$status = Samsara::cameraMedia()->getRetrieval($retrievalId);

// The response includes the media URL when ready
if ($status['status'] === 'completed') {
    $mediaUrl = $status['url'];
}
```

## Retrieval Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `cameraSerial` | string | Camera serial number |
| `startTime` | string | Start time (ISO 8601) |
| `endTime` | string | End time (ISO 8601) |
| `eventId` | string | Safety event ID (alternative to time range) |
