---
name: samsara-sdk-development
description: Use this skill when building or changing a Laravel application's integration with erikgall/samsara v2, including installation, configuration, authentication, regions, resource calls, query filters, pagination, typed or generic response data, SDK exceptions, fakes, fixtures, and webhook signature verification. Do not use it to migrate a v1 application to v2; use upgrade-samsara-sdk-v2 for that migration.
---

# Samsara SDK Development

Work from the installed SDK, not from memory or a similarly named Samsara API client. The package wraps selected upstream operations and deliberately does not expose every API endpoint.

## Workflow

1. Confirm the installed version and package root:

    ```bash
    composer show erikgall/samsara
    composer show erikgall/samsara --path
    ```

2. Inspect the consuming application's existing Samsara calls, configuration, service boundaries, and tests before choosing a pattern.
3. Read the relevant local package documentation and source. Use the package root returned above; do not assume it is `vendor/erikgall/samsara`.
4. Select an existing resource accessor and its documented helper or query builder. Check the exact generic capability before calling `all()`, `find()`, `create()`, `update()`, or `delete()`.
5. Preserve the documented HTTP method, path, query encoding, body, response wrapper, and return type. Do not replace a rejected SDK operation with a raw request unless the application deliberately owns and tests that upstream contract.
6. Add or update a focused test with `Samsara::fake()` or the SDK's HTTP factory. Assert the important endpoint and parameters.
7. Run the consuming repository's targeted tests, formatter, static analysis, and broader checks required by the change.

## Read the Relevant Reference

- Read [integration.md](references/integration.md) for installation, configuration, client construction, regions, and selecting a resource.
- Read [querying-and-responses.md](references/querying-and-responses.md) before changing filters, list encoding, pagination, return-shape handling, or exception handling.
- Read [testing-and-webhooks.md](references/testing-and-webhooks.md) before writing SDK tests or accepting Samsara webhooks.
- For a version 1.1 to version 2 migration, stop and activate `upgrade-samsara-sdk-v2` instead.

## Version 2 Contracts

- Generic CRUD is capability-gated per resource. `UnsupportedOperationException` means the call is not represented by that SDK resource; it is not a transient API error.
- A resource accessor is not necessarily a query builder. Call the documented builder-returning helper such as `query()`, `current()`, `history()`, `feed()`, `logs()`, or `dvirs()` before builder methods.
- Results are intentionally mixed. Depending on the operation, the SDK may return a typed DTO, a generic `Entity`, an `EntityCollection`, a `CursorPaginator`, a `LazyCollection`, a raw array, a scalar, or `null`.
- Dedicated list filters and contract-declared repeated parameters do not share the same wire encoding. Use the existing typed helper and assert the encoded request instead of assembling query strings manually.
- A supported `find()` returns `null` for HTTP 404. Other failed SDK operations map to `Samsara\Exceptions` after retries are exhausted.
- `paginate()` and `lazy()` may apply configured `samsara.per_page`; endpoint helpers whose upstream contract has no `limit` suppress that default.

## Minimal Pattern

```php
use Samsara\Facades\Samsara;

$stats = Samsara::vehicleStats()
    ->current()
    ->whereVehicle(['vehicle-1', 'vehicle-2'])
    ->types(['gps', 'engineStates'])
    ->get();
```

Verify this pattern against the exact operation in the installed package. Do not generalize one resource's helpers or result type to another resource.
