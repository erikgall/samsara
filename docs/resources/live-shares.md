# Live Sharing Links Resource

Manage live sharing links for external visibility into fleet data.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all live shares
$liveShares = Samsara::liveShares()->all();

// Find a live share
$liveShare = Samsara::liveShares()->find('live-share-id');

// Create a live share
$liveShare = Samsara::liveShares()->create([
    'name' => 'Customer Fleet View',
    'assets' => [
        ['id' => 'vehicle-123', 'type' => 'vehicle'],
        ['id' => 'vehicle-456', 'type' => 'vehicle'],
    ],
    'recipients' => [
        ['email' => 'customer@example.com'],
    ],
    'expiresAt' => '2024-12-31T23:59:59Z',
]);

// Update a live share
$liveShare = Samsara::liveShares()->update('live-share-id', [
    'name' => 'Updated Fleet View',
]);

// Delete a live share
Samsara::liveShares()->delete('live-share-id');
```

## Query Builder

```php
// Get all live shares with query builder
$liveShares = Samsara::liveShares()
    ->query()
    ->get();

// Limit results
$liveShares = Samsara::liveShares()
    ->query()
    ->limit(25)
    ->get();
```

## LiveShare Entity

The `LiveShare` entity provides helper methods:

```php
$liveShare = Samsara::liveShares()->find('live-share-id');

// Check status
$liveShare->isActive();   // bool
$liveShare->isExpired();  // bool

// Basic properties
$liveShare->id;            // string
$liveShare->name;          // ?string
$liveShare->url;           // ?string
$liveShare->status;        // ?string ('active', 'expired')
$liveShare->expiresAt;     // ?string
$liveShare->createdAtTime; // ?string
$liveShare->assets;        // ?array
$liveShare->recipients;    // ?array
```

## Available Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | Live share ID |
| `name` | string | Live share name |
| `url` | string | Shareable URL |
| `status` | string | Status ('active' or 'expired') |
| `expiresAt` | string | Expiration timestamp (RFC 3339) |
| `createdAtTime` | string | Creation timestamp (RFC 3339) |
| `assets` | array | Shared assets with id and type |
| `recipients` | array | Share recipients with email |

## Common Use Cases

### Share Vehicle Location with Customers

```php
// Create a time-limited share for a delivery vehicle
$share = Samsara::liveShares()->create([
    'name' => "Delivery Tracking - Order #12345",
    'assets' => [
        ['id' => $vehicle->id, 'type' => 'vehicle'],
    ],
    'recipients' => [
        ['email' => $customer->email],
    ],
    'expiresAt' => now()->addHours(24)->toIso8601String(),
]);

// Get the shareable URL
$trackingUrl = $share->url;
```

### Clean Up Expired Shares

```php
$liveShares = Samsara::liveShares()->all();

$liveShares->each(function ($share) {
    if ($share->isExpired()) {
        Samsara::liveShares()->delete($share->id);
    }
});
```
