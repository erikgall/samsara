---
title: Error Handling
layout: default
nav_order: 5
description: "Exception handling and retry logic for the Samsara SDK"
permalink: /error-handling
---

# Error Handling

- [Introduction](#introduction)
- [Exception Hierarchy](#exception-hierarchy)
- [Catching Exceptions](#catching-exceptions)
- [Exception Reference](#exception-reference)
  - [AuthenticationException](#authenticationexception)
  - [AuthorizationException](#authorizationexception)
  - [NotFoundException](#notfoundexception)
  - [UnsupportedOperationException](#unsupportedoperationexception)
  - [ValidationException](#validationexception)
  - [RateLimitException](#ratelimitexception)
  - [ServerException](#serverexception)
  - [InvalidSignatureException](#invalidsignatureexception)
  - [ConnectionException](#connectionexception)
- [Exception Context](#exception-context)
- [Retry Logic](#retry-logic)
- [Connection Errors](#connection-errors)
- [Laravel Exception Handler](#laravel-exception-handler)

## Introduction

When the Samsara API returns an error, the SDK throws a typed exception you can catch by status family or by specific failure mode. Every exception extends `Samsara\Exceptions\SamsaraException`, so you may catch broadly or narrow down to the case you care about. The reference below maps each exception to the API response that triggers it, shows the context every exception carries, and walks through wiring SDK errors into Laravel's exception handler.

## Exception Hierarchy

```text
SamsaraException (base)
├── AuthenticationException (401)
├── AuthorizationException (403)
├── NotFoundException (404)
├── UnsupportedOperationException
├── ValidationException (422)
├── RateLimitException (429)
├── ServerException (5xx)
├── InvalidSignatureException (webhook verification)
└── ConnectionException (reserved)
```

Network failures (DNS errors, TCP timeouts) are not part of this hierarchy. They propagate as `Illuminate\Http\Client\ConnectionException` from the underlying HTTP client. See [Connection Errors](#connection-errors).

## Catching Exceptions

For most application code, catching the base exception is enough — every SDK error rolls up to it.

```php
use Samsara\Facades\Samsara;
use Samsara\Exceptions\SamsaraException;

try {
    $driver = Samsara::drivers()->find('driver-id');
} catch (SamsaraException $e) {
    Log::error('Samsara API error: '.$e->getMessage(), $e->getContext());
}
```

When you need to react differently per failure mode, branch on the specific cases. Two or three branches plus a fallback covers almost every real-world handler:

```php
use Samsara\Exceptions\AuthenticationException;
use Samsara\Exceptions\RateLimitException;
use Samsara\Exceptions\SamsaraException;

try {
    $driver = Samsara::drivers()->create($payload);
} catch (AuthenticationException $e) {
    Log::error('Samsara token rejected', $e->getContext());
    throw $e;
} catch (RateLimitException $e) {
    sleep($e->getRetryAfter() ?? 30);
    $driver = Samsara::drivers()->create($payload);
} catch (SamsaraException $e) {
    Log::error('Samsara API error', $e->getContext());
    throw $e;
}
```

## Exception Reference

### AuthenticationException

An `AuthenticationException` is thrown whenever Samsara returns a `401` — typically because your API token is missing, malformed, or has been revoked. Verify `SAMSARA_API_KEY` in your environment file before retrying.

```php
use Samsara\Exceptions\AuthenticationException;

try {
    $drivers = Samsara::drivers()->all();
} catch (AuthenticationException $e) {
    // Refresh credentials, then retry.
}
```

### AuthorizationException

Thrown when Samsara returns a `403`. The token is valid, but the authenticated user lacks the permissions required for the request.

```php
use Samsara\Exceptions\AuthorizationException;

try {
    Samsara::drivers()->delete('driver-id');
} catch (AuthorizationException $e) {
    Log::warning('Permission denied: '.$e->getMessage());
}
```

### NotFoundException

Thrown when Samsara returns a `404` for any method except `find()`. `find()` swallows 404s and returns `null` so you can branch on the absence of a record without a `try/catch`.

```php
use Samsara\Exceptions\NotFoundException;

$driver = Samsara::drivers()->find('missing-id');
// $driver is null, no exception.

try {
    Samsara::drivers()->delete('missing-id');
} catch (NotFoundException $e) {
    // 404 from a non-find() method.
}
```

### UnsupportedOperationException

Unlike the rest of the hierarchy, `UnsupportedOperationException` is not tied to an HTTP status code. The SDK throws it before sending a request when the targeted resource fundamentally does not support the operation. The most common case is creating or deleting a vehicle through `/fleet/vehicles`, which the Samsara API does not allow.

```php
use Samsara\Exceptions\UnsupportedOperationException;

try {
    Samsara::vehicles()->create(['name' => 'Truck 001']);
} catch (UnsupportedOperationException $e) {
    // The exception message includes guidance on the correct approach.
    echo $e->getMessage();
}
```

### ValidationException

Thrown when Samsara returns a `422`. Call `getErrors()` to read the API's `errors` payload.

```php
use Samsara\Exceptions\ValidationException;

try {
    Samsara::drivers()->create([]);
} catch (ValidationException $e) {
    foreach ($e->getErrors() as $field => $messages) {
        Log::warning("{$field}: ".implode(', ', $messages));
    }
}
```

> **Note:** `getErrors()` returns whatever the API placed in the `errors` key of the response body. Samsara's 422 responses sometimes return an empty array and place the human-readable detail in the message instead. Always inspect `$e->getMessage()` alongside `$e->getErrors()`.

### RateLimitException

Thrown when Samsara returns a `429`. `getRetryAfter()` returns the integer parsed from the `Retry-After` header, or `null` when the API did not include one.

```php
use Samsara\Exceptions\RateLimitException;

try {
    $drivers = Samsara::drivers()->all();
} catch (RateLimitException $e) {
    $wait = $e->getRetryAfter() ?? 30;
    sleep($wait);
}
```

### ServerException

Thrown for any `5xx` response. The HTTP status is preserved on the exception via `getCode()`, which is helpful when you log or alert on these failures.

```php
use Samsara\Exceptions\ServerException;

try {
    $drivers = Samsara::drivers()->all();
} catch (ServerException $e) {
    Log::critical('Samsara API failure', [
        'message' => $e->getMessage(),
        'status'  => $e->getCode(),
    ]);
}
```

### InvalidSignatureException

Thrown by `Samsara\Webhooks\WebhookSignatureVerifier` when an inbound webhook fails verification — missing headers, an expired timestamp, a configuration error, or a signature mismatch. Catch it in your webhook controller and respond with `401`.

```php
use Samsara\Exceptions\InvalidSignatureException;
use Samsara\Webhooks\WebhookSignatureVerifier;

try {
    WebhookSignatureVerifier::make(config('samsara.webhook_secret'))
        ->verifyFromRequest(
            $request->getContent(),
            $request->header('X-Samsara-Signature'),
            $request->header('X-Samsara-Timestamp'),
        );
} catch (InvalidSignatureException $e) {
    abort(401, $e->getMessage());
}
```

See [Webhooks](resources/webhooks.md) for the full verification flow.

### ConnectionException

`Samsara\Exceptions\ConnectionException` is reserved for a future SDK release that wraps low-level network failures. The SDK does not currently throw it. Today, network errors propagate as `Illuminate\Http\Client\ConnectionException` from the underlying HTTP client — see [Connection Errors](#connection-errors).

## Exception Context

Every SDK exception (except `UnsupportedOperationException`, which never reaches the network) carries a `getContext()` array describing the failed request.

```php
try {
    Samsara::drivers()->find('driver-id');
} catch (SamsaraException $e) {
    $context = $e->getContext();
    // [
    //     'status'   => 404,
    //     'endpoint' => '/fleet/drivers',
    //     'body'     => [...],
    // ]
}
```

Pass the context straight to your logger to capture the failed endpoint, status, and response body in a single record.

## Retry Logic

The SDK retries transient HTTP failures automatically. Configure the retry count in `config/samsara.php`:

```php
'retry' => 3,
```

For application-level retry with exponential backoff — for example, when a long-running job hits a rate limit — wrap the call in a small helper:

```php
use Samsara\Exceptions\RateLimitException;
use Samsara\Exceptions\ServerException;

function fetchWithRetry(callable $callback, int $maxAttempts = 3): mixed
{
    $attempt = 0;

    while ($attempt < $maxAttempts) {
        try {
            return $callback();
        } catch (RateLimitException $e) {
            sleep($e->getRetryAfter() ?? (2 ** $attempt));
            $attempt++;
        } catch (ServerException $e) {
            sleep(2 ** $attempt);
            $attempt++;
        }
    }

    throw new \RuntimeException('Max retry attempts exceeded');
}

$drivers = fetchWithRetry(fn () => Samsara::drivers()->all());
```

## Connection Errors

DNS failures, TCP timeouts, and other transport-level errors come through Laravel's HTTP client as `Illuminate\Http\Client\ConnectionException`. They are not part of the SDK's exception hierarchy today.

```php
use Illuminate\Http\Client\ConnectionException;

try {
    $drivers = Samsara::drivers()->all();
} catch (ConnectionException $e) {
    Log::error('Cannot reach Samsara API: '.$e->getMessage());
}
```

A future release of the SDK may wrap these failures in `Samsara\Exceptions\ConnectionException`. Until then, catch the Laravel exception explicitly when you need to react to network problems.

## Laravel Exception Handler

Laravel 12 and 13 register exception behavior in `bootstrap/app.php` via the `withExceptions(...)` callback. Catch the SDK exceptions there to centralize the response your application returns when Samsara fails.

```php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Samsara\Exceptions\AuthenticationException;
use Samsara\Exceptions\RateLimitException;
use Samsara\Exceptions\SamsaraException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e) {
            return response()->json([
                'error' => 'Fleet API authentication failed',
            ], 500);
        });

        $exceptions->render(function (RateLimitException $e) {
            return response()->json([
                'error'       => 'Fleet API rate limit exceeded',
                'retry_after' => $e->getRetryAfter(),
            ], 503);
        });

        $exceptions->render(function (SamsaraException $e) {
            Log::error('Samsara API error', [
                'message' => $e->getMessage(),
                'context' => $e->getContext(),
            ]);

            return response()->json(['error' => 'Fleet API error'], 500);
        });
    })->create();
```

The closures are matched by type-hint, so order them from most specific to least specific. The base `SamsaraException` handler catches every other case.
