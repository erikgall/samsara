---
title: Error Handling
layout: default
nav_order: 5
description: "Exception handling and retry logic for the Samsara SDK"
permalink: /error-handling
---

# Error Handling

The Samsara SDK provides a comprehensive exception hierarchy for handling API errors.

## Exception Hierarchy

```
SamsaraException (base)
├── AuthenticationException (401)
├── AuthorizationException (403)
├── NotFoundException (404)
├── ValidationException (422)
├── RateLimitException (429)
└── ServerException (5xx)
```

## Catching Exceptions

### Basic Error Handling

```php
use Samsara\Facades\Samsara;
use Samsara\Exceptions\SamsaraException;

try {
    $driver = Samsara::drivers()->find('driver-id');
} catch (SamsaraException $e) {
    // Handle any API error
    Log::error('Samsara API error: ' . $e->getMessage());
}
```

### Specific Exception Handling

```php
use Samsara\Facades\Samsara;
use Samsara\Exceptions\AuthenticationException;
use Samsara\Exceptions\AuthorizationException;
use Samsara\Exceptions\NotFoundException;
use Samsara\Exceptions\ValidationException;
use Samsara\Exceptions\RateLimitException;
use Samsara\Exceptions\ServerException;
use Samsara\Exceptions\SamsaraException;

try {
    $driver = Samsara::drivers()->create($data);
} catch (AuthenticationException $e) {
    // Invalid or expired API token (401)
    Log::error('Invalid API token');

} catch (AuthorizationException $e) {
    // Insufficient permissions (403)
    Log::error('Permission denied');

} catch (NotFoundException $e) {
    // Resource not found (404)
    Log::warning('Resource not found');

} catch (ValidationException $e) {
    // Invalid request data (422)
    $errors = $e->getErrors();
    Log::warning('Validation failed', ['errors' => $errors]);

} catch (RateLimitException $e) {
    // Too many requests (429)
    $retryAfter = $e->getRetryAfter();
    Log::warning("Rate limited. Retry after {$retryAfter} seconds");

} catch (ServerException $e) {
    // Server error (5xx)
    Log::error('Samsara server error: ' . $e->getMessage());

} catch (SamsaraException $e) {
    // Other API errors
    Log::error('API error: ' . $e->getMessage());
}
```

## Exception Details

### AuthenticationException (401)

Thrown when the API token is invalid or expired.

```php
try {
    $drivers = Samsara::drivers()->all();
} catch (AuthenticationException $e) {
    // Check your SAMSARA_API_KEY in .env
    echo $e->getMessage(); // "Invalid API token"
}
```

### AuthorizationException (403)

Thrown when the authenticated user lacks permission.

```php
try {
    $driver = Samsara::drivers()->delete('driver-id');
} catch (AuthorizationException $e) {
    echo $e->getMessage(); // "You don't have permission..."
}
```

### NotFoundException (404)

Thrown when a requested resource doesn't exist.

```php
try {
    $driver = Samsara::drivers()->find('invalid-id');
} catch (NotFoundException $e) {
    // Note: find() returns null for 404, doesn't throw
}

// For other methods that throw on 404:
try {
    $driver = Samsara::drivers()->delete('invalid-id');
} catch (NotFoundException $e) {
    echo $e->getMessage(); // "Driver not found"
}
```

### ValidationException (422)

Thrown when request data fails validation.

```php
try {
    $driver = Samsara::drivers()->create([
        // Missing required fields
    ]);
} catch (ValidationException $e) {
    // Get validation errors
    $errors = $e->getErrors();

    // [
    //     'name' => ['Name is required'],
    //     'phone' => ['Invalid phone format'],
    // ]

    foreach ($errors as $field => $messages) {
        echo "{$field}: " . implode(', ', $messages);
    }
}
```

### RateLimitException (429)

Thrown when you exceed the API rate limit.

```php
try {
    $drivers = Samsara::drivers()->all();
} catch (RateLimitException $e) {
    // Get retry-after value (seconds)
    $retryAfter = $e->getRetryAfter();

    if ($retryAfter) {
        sleep($retryAfter);
        // Retry the request
    }
}
```

### ServerException (5xx)

Thrown for server-side errors.

```php
try {
    $drivers = Samsara::drivers()->all();
} catch (ServerException $e) {
    // Log and alert, likely a temporary issue
    Log::critical('Samsara API is down', [
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
    ]);
}
```

## Exception Context

All exceptions include context information:

```php
try {
    $driver = Samsara::drivers()->find('driver-id');
} catch (SamsaraException $e) {
    $context = $e->getContext();

    // [
    //     'status' => 404,
    //     'endpoint' => '/fleet/drivers',
    //     'body' => [...],
    // ]
}
```

## Retry Logic

The SDK includes automatic retry for transient failures. Configure in `config/samsara.php`:

```php
'retry' => 3, // Number of retries
```

For manual retry with exponential backoff:

```php
use Samsara\Facades\Samsara;
use Samsara\Exceptions\RateLimitException;
use Samsara\Exceptions\ServerException;

function fetchWithRetry(callable $callback, int $maxAttempts = 3): mixed
{
    $attempt = 0;

    while ($attempt < $maxAttempts) {
        try {
            return $callback();
        } catch (RateLimitException $e) {
            $retryAfter = $e->getRetryAfter() ?? pow(2, $attempt);
            sleep($retryAfter);
            $attempt++;
        } catch (ServerException $e) {
            sleep(pow(2, $attempt));
            $attempt++;
        }
    }

    throw new \RuntimeException('Max retry attempts exceeded');
}

// Usage
$drivers = fetchWithRetry(fn() => Samsara::drivers()->all());
```

## Laravel Exception Handler

Handle Samsara exceptions globally in `app/Exceptions/Handler.php`:

```php
use Samsara\Exceptions\SamsaraException;
use Samsara\Exceptions\AuthenticationException;
use Samsara\Exceptions\RateLimitException;

public function register(): void
{
    $this->renderable(function (AuthenticationException $e) {
        return response()->json([
            'error' => 'Fleet API authentication failed',
        ], 500);
    });

    $this->renderable(function (RateLimitException $e) {
        return response()->json([
            'error' => 'Fleet API rate limit exceeded',
            'retry_after' => $e->getRetryAfter(),
        ], 503);
    });

    $this->renderable(function (SamsaraException $e) {
        Log::error('Samsara API error', [
            'message' => $e->getMessage(),
            'context' => $e->getContext(),
        ]);

        return response()->json([
            'error' => 'Fleet API error',
        ], 500);
    });
}
```

## Connection Errors

Network issues throw Laravel's `ConnectionException`:

```php
use Illuminate\Http\Client\ConnectionException;
use Samsara\Facades\Samsara;

try {
    $drivers = Samsara::drivers()->all();
} catch (ConnectionException $e) {
    // Network timeout, DNS failure, etc.
    Log::error('Cannot connect to Samsara API: ' . $e->getMessage());
}
```
