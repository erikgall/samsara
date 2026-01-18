# Tags Resource

Manage tags for organizing and grouping resources like vehicles, drivers, and equipment.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all tags
$tags = Samsara::tags()->all();

// Find a tag
$tag = Samsara::tags()->find('tag-id');

// Create a tag
$tag = Samsara::tags()->create([
    'name' => 'West Coast Fleet',
]);

// Create a nested tag
$tag = Samsara::tags()->create([
    'name' => 'California',
    'parentTagId' => 'parent-tag-id',
]);

// Update a tag
$tag = Samsara::tags()->update('tag-id', [
    'name' => 'West Coast Operations',
]);

// Delete a tag
Samsara::tags()->delete('tag-id');
```

## Query Builder

```php
// Get all tags with query builder
$tags = Samsara::tags()
    ->query()
    ->get();

// Limit results
$tags = Samsara::tags()
    ->query()
    ->limit(50)
    ->get();
```

## Tag Entity

The `Tag` entity provides helper methods:

```php
$tag = Samsara::tags()->find('tag-id');

// Check if tag has a parent
$tag->hasParent();  // bool

// Basic properties
$tag->id;          // string
$tag->name;        // string
$tag->parentTagId; // ?string
$tag->externalIds; // ?array
```

## Available Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | Tag ID |
| `name` | string | Tag name |
| `parentTagId` | string | Parent tag ID (for hierarchical tags) |
| `externalIds` | array | External ID mappings |

## Hierarchical Tags

Tags can be organized hierarchically:

```php
// Create parent tag
$parentTag = Samsara::tags()->create([
    'name' => 'Regions',
]);

// Create child tags
$westTag = Samsara::tags()->create([
    'name' => 'West',
    'parentTagId' => $parentTag->id,
]);

$eastTag = Samsara::tags()->create([
    'name' => 'East',
    'parentTagId' => $parentTag->id,
]);

// Check if a tag is a child
if ($westTag->hasParent()) {
    echo "Parent: {$westTag->parentTagId}";
}
```

## Using Tags for Filtering

Tags are commonly used to filter other resources:

```php
// Filter vehicles by tag
$vehicles = Samsara::vehicles()
    ->query()
    ->whereTag('west-coast-fleet')
    ->get();

// Filter drivers by tag
$drivers = Samsara::drivers()
    ->query()
    ->whereTag('delivery-drivers')
    ->get();

// Filter safety events by tag
$events = Samsara::safetyEvents()
    ->query()
    ->whereTag('monitored-fleet')
    ->between(now()->subDays(7), now())
    ->get();
```
