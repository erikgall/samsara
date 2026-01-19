---
title: Upgrading
layout: default
nav_order: 7
description: "Migration guide from v1.x to v2.0"
permalink: /upgrading
---

# Upgrading from v1.x to v2.0

Version 2.0 is a complete rewrite of the SDK, replacing the Saloon-based implementation with Laravel's HTTP client.

## Breaking Changes

### Dependencies

**Removed:**
- `saloonphp/saloon`
- `saloonphp/laravel-plugin`

**Added:**
- `illuminate/http`
- `illuminate/collections`
- `illuminate/support`

### PHP Version

- Minimum PHP version is now **8.1** (for enum support)
- Minimum Laravel version is now **10.x** (also supports 11.x and 12.x)

### Namespace Changes

All classes have been reorganized:

| v1.x | v2.0 |
|------|------|
| `Samsara\Resource\*` | `Samsara\Resources\*` |
| `Samsara\Requests\*` | Removed (methods on Resources) |
| `Samsara\Entities\*` | `Samsara\Data\*` |

### Client Initialization

**v1.x (Saloon):**
```php
use Samsara\Samsara;

$samsara = new Samsara($token);
$connector = $samsara->connector();
```

**v2.0 (Laravel HTTP):**
```php
use Samsara\Samsara;

$samsara = Samsara::make($token);
// or
$samsara = new Samsara($token);
```

### Making Requests

**v1.x (Saloon Requests):**
```php
use Samsara\Requests\Fleet\ListDrivers;

$request = new ListDrivers();
$response = $connector->send($request);
$drivers = $response->json()['data'];
```

**v2.0 (Resource Methods):**
```php
use Samsara\Facades\Samsara;

$drivers = Samsara::drivers()->all();
// Returns EntityCollection of Driver entities
```

### Entity Classes

**v1.x:**
Entities were simple arrays or custom classes.

**v2.0:**
All entities extend `Illuminate\Support\Fluent`:

```php
$driver = Samsara::drivers()->find('driver-id');

// Access properties
$driver->name;
$driver->id;

// Use helper methods
$driver->isActive();
$driver->getDisplayName();

// Convert to array
$driver->toArray();
```

### Query Building

**v1.x:**
Query parameters were passed as arrays to request classes.

**v2.0:**
Fluent query builder:

```php
$drivers = Samsara::drivers()
    ->query()
    ->whereTag('tag-123')
    ->limit(50)
    ->get();
```

### Pagination

**v1.x:**
Manual cursor handling.

**v2.0:**
Built-in pagination support:

```php
// Automatic pagination
$paginator = Samsara::vehicles()->query()->paginate();

foreach ($paginator as $vehicle) {
    // Process vehicle
}

if ($paginator->hasMorePages()) {
    $nextPage = $paginator->nextPage();
}

// Lazy loading for memory efficiency
Samsara::vehicleStats()
    ->types(['gps'])
    ->lazy()
    ->each(function ($stat) {
        // Process each stat
    });
```

### Error Handling

**v1.x:**
Saloon exceptions.

**v2.0:**
Custom exception hierarchy:

```php
use Samsara\Exceptions\AuthenticationException;
use Samsara\Exceptions\NotFoundException;
use Samsara\Exceptions\ValidationException;
use Samsara\Exceptions\RateLimitException;

try {
    $driver = Samsara::drivers()->find('id');
} catch (AuthenticationException $e) {
    // 401 error
} catch (NotFoundException $e) {
    // 404 error
} catch (ValidationException $e) {
    $errors = $e->getErrors();
} catch (RateLimitException $e) {
    $retryAfter = $e->getRetryAfter();
}
```

### Configuration

**v1.x:**
```php
// config/samsara.php
return [
    'key' => env('SAMSARA_KEY'),
];
```

**v2.0:**
```php
// config/samsara.php
return [
    'api_key' => env('SAMSARA_API_KEY'),
    'region' => env('SAMSARA_REGION', 'us'),
    'timeout' => env('SAMSARA_TIMEOUT', 30),
    'retry' => env('SAMSARA_RETRY', 3),
];
```

Update your `.env`:
```env
# Old
SAMSARA_KEY=your-token

# New
SAMSARA_API_KEY=your-token
```

### Testing

**v1.x:**
Saloon's `MockClient`.

**v2.0:**
`SamsaraFake` class:

```php
use Samsara\Facades\Samsara;

$fake = Samsara::fake();

$fake->fakeDrivers([
    ['id' => 'driver-1', 'name' => 'John Doe'],
]);

$drivers = $fake->drivers()->all();
```

## Migration Steps

1. **Update composer.json:**
   ```bash
   composer require erikgall/samsara:^2.0
   ```

2. **Update configuration:**
   ```bash
   php artisan vendor:publish --provider="Samsara\SamsaraServiceProvider" --force
   ```

3. **Update environment variables:**
   ```env
   SAMSARA_API_KEY=your-token
   SAMSARA_REGION=us
   ```

4. **Update code:**
   - Replace Saloon request classes with resource methods
   - Update entity access to use Fluent properties
   - Update exception handling
   - Update tests to use SamsaraFake

5. **Run tests:**
   ```bash
   php artisan test
   ```

## New Features in v2.0

- **40+ Resource Endpoints** - Comprehensive API coverage
- **Query Builder** - Fluent filtering and pagination
- **Entity Classes** - Strongly-typed with helper methods
- **Lazy Collections** - Memory-efficient data processing
- **EU Region Support** - `useEuEndpoint()` method
- **Better Error Handling** - Specific exception types
- **Testing Support** - SamsaraFake with fixtures

## Getting Help

- [Documentation](./getting-started.md)
- [GitHub Issues](https://github.com/erikgall/samsara/issues)
