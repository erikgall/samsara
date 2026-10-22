# Upgrading Samsara SDK

Version 2 is a contract-alignment release. It changes public method signatures, generic resource capabilities, DTO classes and payload fields, exception behavior, pagination defaults, query encoding, and several HTTP paths.

Do not upgrade a production application by changing only the Composer constraint.

Use the canonical [v1.1.0 to v2.0.0 upgrade guide](https://github.com/erikgall/samsara/blob/main/docs/upgrade-guide-v2.md). It contains:

- ordered, mandatory migration steps;
- before-and-after PHP for every developer-facing break;
- exact searches for removed calls, fields, values, and paths;
- HTTP-fake, cache, proxy, serializer, and persistence checks;
- an agent completion record and release checklist; and
- a [machine-checkable disposition ledger](https://github.com/erikgall/samsara/blob/main/docs/api-sync/v2-upgrade-disposition.json) covering the frozen 239-file implementation baseline plus all integrated quality, test, documentation, and publication paths.

`BREAKING_CHANGES.md` is the compact release record. The upgrade guide is the migration authority.

## Maintainer integration status

The parallel quality lanes have been integrated and classified. The ledger retains its frozen implementation comparison at `1750395c98c1724c40c82ba82b96b4866dd2106b`, then derives the full release scope from the live tag-to-worktree diff. Present records store the classified file's SHA-256 content identity, deletions carry an explicit deleted state, and only the self-referential ledger is excluded. A future added, edited, deleted, or restored path makes the maintenance check stale until its migration or no-action disposition is reviewed and regenerated.

## Final v2 API migration (waves 1 and 2)

- **Retry policy:** POST/PUT/PATCH/DELETE no longer retry connection failures, HTTP 408, or 5xx by default. Opt in with `retry_writes` / `SAMSARA_RETRY_WRITES` (default `false`). HTTP 429 remains retryable for every method within `attempts` and `retry_after_max_seconds`. GET requests no longer retry other 4xx responses (400, 401, 403, 404, 422); v1.1.0 retried every failure. `Retry-After` accepts only nonnegative integer seconds or an HTTP-date in the `Wed, 21 Oct 2026 13:00:00 GMT` form (RFC 850 and asctime forms are ignored); other values are ignored. `RateLimitException::getRetryAfter()` returns seconds for valid headers and `null` otherwise, and `getContext()['retry_after_header']` retains the raw header.
- **Client copies:** `useBaseUrl`, `useUsEndpoint`, `useEuEndpoint`, and `useCanadaEndpoint` are removed. Retain the copies returned by `withBaseUrl(string $url)` and `withRegion(string $region)` (`us`, `eu`, `ca`). An unset or blank `SAMSARA_REGION` falls back to `us`. Unknown nonempty regions throw `InvalidArgumentException`; `withRegion()` still requires `us`, `eu`, or `ca`.
- **Fakes:** `Samsara::fake([...])` accepts exact path-to-response stubs. A stub with a `data` key is used unchanged; otherwise it is wrapped in `data`. `fakeResponse()` follows the same rule. Replace `fakeDrivers`, `fakeVehicles`, and `fakeVehicleStats` with path stubs or `fakeResponse()`. Staging and recorded history are shared across `withToken()` / `withRegion()` clones.
- **Queries:** replace `Builder::getWithPagination()` with `paginate()`. `Cursor` is final with readonly `endCursor` and `hasNextPage` properties; its getters are removed. `Cursor::next()` returns the next cursor or `null`. `QueryBuilderInterface` is removed. `Builder` adds Laravel `Conditionable` (`when` / `unless`); `types()` and `withDecorations()` accept individual `VehicleStatType` cases and mixed enum/string arrays. Repeated or cyclic cursors throw `SamsaraException`. `first()` does not inject a limit when automatic page sizing is disabled.
- **Builder extensions:** The protected `parseTime()`, `toUnixMilliseconds()`, and `fromUnixMilliseconds()` helpers are removed from `InteractsWithTime`. Move any required parsing or millisecond conversion into the application subclass; `formatTime()` and `toUtcString()` remain for request formatting.
- **Factories and webhooks:** `SamsaraException::make()`, `VerifyWebhookSignature::make()`, and the `Makeable` trait are removed. Construct exceptions directly and register webhook middleware by class string or alias. Empty route/constructor secrets fall back to config. Missing-header exceptions expose the exact header through `getContext()['header']`. A blank route tolerance (`samsara.webhook:secret,`) now uses the 300-second default instead of `0`.
- **DTOs:** `new DataInput($attributes)` hydrates nested points just like `DataInput::make($attributes)`; `toArray()` exports those points as arrays. Driver auth-token and live-share expiry checks use Laravel's clock; freeze it with `Illuminate\Support\Carbon::setTestNow()`.
- **Resources:** `all()` is removed everywhere; use the documented endpoint builder and its required filters. CRUD is composed from `FindsEntities`, `CreatesEntities`, `UpdatesEntities`, and `DeletesEntities` traits so unsupported methods are absent, rather than callable methods throwing `UnsupportedOperationException`. `ResourceInterface` is removed, as is `supportedGenericOperations()`. Entity return contracts use `Samsara\Data\Entity` and concrete domain subclasses instead of `Fluent`; update custom overrides and type declarations.

### Before and after

The Before snippets describe the earlier API and are not executable on the final v2 code.

```php
// Before: mutates the shared client.
Samsara::useEuEndpoint();
Samsara::useBaseUrl('https://proxy.example.test');

// After: retain the selected copy.
$euClient = Samsara::withToken($carrier->samsara_token)->withRegion('eu');
$proxyClient = $euClient->withBaseUrl('https://proxy.example.test');
$drivers = $euClient->drivers()->query()->get();
```

```php
// Before: retries could replay accepted writes under the default policy.
$client = \Samsara\Samsara::make($token, ['attempts' => 3]);

// After: writes do not retry transport/408/5xx by default.
$client = \Samsara\Samsara::make($token, ['retry_writes' => false]);
// Explicit opt-in, only when replaying writes is safe.
$retryingClient = \Samsara\Samsara::make($token, ['retry_writes' => true]);
```

```php
// Before
$fake = Samsara::fake();
$fake->fakeDrivers([['id' => 'driver-1']]);
$drivers = $fake->drivers()->all();

// After
$fake = Samsara::fake(['/fleet/drivers' => [['id' => 'driver-1']]]);
$carrierClient = $fake->withToken('carrier-token')->withRegion('ca');
$fake->fakeResponse('/fleet/vehicles', [['id' => 'vehicle-1']]);
$drivers = $carrierClient->drivers()->query()->get();
$vehicles = $carrierClient->vehicles()->query()->get();
```

```php
// Before
$page = $query->getWithPagination();
$token = $page->cursor()->getEndCursor();
$more = $page->cursor()->hasNextPage();

// After
$page = $query->paginate();
$token = $page->cursor()->endCursor;
$more = $page->cursor()->hasNextPage;
```

```php
// Before
$exception = \Samsara\Exceptions\SamsaraException::make('Import failed');
$middleware = \Samsara\Webhooks\VerifyWebhookSignature::make(null, 600);

// After
$exception = new \Samsara\Exceptions\SamsaraException('Import failed');
Route::post('/webhooks/samsara', SamsaraWebhookController::class)
    ->middleware('samsara.webhook:,600');
```

The empty alias secret uses configuration. `VerifyWebhookSignature::class` and the bare `samsara.webhook` alias both keep the 300-second default. Explicit secrets embedded in aliases are written to disk by `route:cache`. Replace message-text assertions for missing headers with `InvalidSignatureException` and `getContext()['header']` (`X-Samsara-Signature` or `X-Samsara-Timestamp`).

```php
// Before: constructor hydration and serialization differed from make().
$input = \Samsara\Data\Industrial\DataInput::make($attributes);

// After: either entry point hydrates points; toArray() returns plain arrays.
$input = new \Samsara\Data\Industrial\DataInput($attributes);
$points = $input->toArray();
```

| Before | After |
| --- | --- |
| Implements `QueryBuilderInterface` or `ResourceInterface` | Remove the deleted interface; use the concrete builder/resource type or an application-owned contract. |
| Uses `Makeable` in an application class | Replace its factory use with that class's constructor; DTO `make()` remains available through `Samsara\Data\Entity`. |
| Calls protected `createBuilderWithEndpoint($endpoint)` in a resource subclass | Use `newQuery($endpoint)`; preserve endpoint-specific mapping and filters. |
| Overrides `supportedGenericOperations()` | Compose only the supported per-operation traits and preserve their entity return types. |
| Extends or mutates `Cursor` | Construct a new `Cursor($endCursor, $hasNextPage)`; the class is final and its properties readonly. |
| Reads nested `DataPoint` objects out of `toArray()` | Read objects from the DTO's point properties, or arrays from `toArray()`. |
| Tests expiry against `time()` | Freeze `Illuminate\Support\Carbon::setTestNow($instant)` and reset it with `setTestNow()` after the test. |
| Relies on `first()` sending `limit=1` on every endpoint | Assert no implicit `limit` on builders with `withoutDefaultPageSize()`. |
| Assumes cursor traversal terminates only by exhaustion | Handle `SamsaraException` for repeated or cyclic cursors. |

### Removed resource methods

| Resource | Removed methods in addition to `all()` |
| --- | --- |
| Drivers | `delete` |
| Equipment | `create`, `update`, `delete` |
| Vehicles | `create`, `delete` |
| Assets, Industrial, LiveSharingLinks | `find` |
| Gateways, CarrierProposedAssignments | `find`, `update` |
| Alerts, DriverTrailerAssignments, DriverVehicleAssignments, Forms, Issues, WorkOrders | `find`, `update`, `delete` |
| CameraMedia, FuelAndEnergy, Hubs, Idling, Ifta, RouteEvents, Settings, Speeding, Tachograph, TrailerAssignments, Beta, Sensors, Legacy, Organization, Preview, HoursOfService, Maintenance, SafetyEvents, Trips, VehicleLocations, VehicleStats | `find`, `create`, `update`, `delete` |

Names above omit the `Resource` suffix; `LiveSharingLinksResource` is reached through `liveShares()`, and organization reads through `me()`. Addresses, Contacts, Routes, Tags, Trailers, Users, and Webhooks retain `find`, `create`, `update`, and `delete`; their `all()` method is also removed.
