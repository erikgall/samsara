# Breaking Changes

This file lists verified consumer actions for the integrated staged-v2 behavior. Engineering evidence belongs in `DEVELOPMENT_CHANGELOG.md`.

For an executable, agent-oriented migration sequence with before/after code, exact searches, endpoint-path tables, verification steps, and a content-verified final release ledger, use the canonical [v1.1.0 to v2.0.0 upgrade guide](UPGRADE.md). This file is the compact release record; the upgrade guide is the migration authority.

## Unreleased — staged v2

Resources now expose only supported methods. Review every removed call below before adopting the final v2 API.

### Final v2 API changes (waves 1 and 2)

- **Retry policy:** POST/PATCH/DELETE no longer retry connection failures, HTTP 408, or 5xx by default. Opt in with `retry_writes` / `SAMSARA_RETRY_WRITES` (default `false`). HTTP 429 remains retryable for every method within `attempts` and `retry_after_max_seconds`. HTTP-date `Retry-After` is honored; `RateLimitException::getRetryAfter()` returns seconds, and `getContext()['retry_after_header']` retains the raw header.
- **Client copies:** `useBaseUrl`, `useUsEndpoint`, `useEuEndpoint`, and `useCanadaEndpoint` are removed. Retain the copies returned by `withBaseUrl(string $url)` and `withRegion(string $region)` (`us`, `eu`, `ca`). Unknown regions throw `InvalidArgumentException`.
- **Fakes:** `Samsara::fake([...])` accepts exact path-to-response stubs containing the inner `data` value. Replace `fakeDrivers`, `fakeVehicles`, and `fakeVehicleStats` with path stubs or `fakeResponse()`. Staging and recorded history are shared across `withToken()` / `withRegion()` clones.
- **Queries:** replace `Builder::getWithPagination()` with `paginate()`. `Cursor` is final with readonly `endCursor` and `hasNextPage` properties; its getters are removed. `QueryBuilderInterface` is removed. `Builder` adds Laravel `Conditionable` (`when` / `unless`); `types()` and `withDecorations()` accept individual `VehicleStatType` cases and mixed enum/string arrays. Repeated or cyclic cursors throw `SamsaraException`. `first()` does not inject a limit when automatic page sizing is disabled.
- **Factories and webhooks:** `SamsaraException::make()`, `VerifyWebhookSignature::make()`, and the `Makeable` trait are removed. Construct exceptions directly and register webhook middleware by class string or alias. Empty route/constructor secrets fall back to config. Missing-header exceptions expose the exact header through `getContext()['header']`.
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

The empty alias secret uses configuration. With the default 300-second tolerance, use `VerifyWebhookSignature::class`. Explicit secrets embedded in aliases are written to disk by `route:cache`. Replace message-text assertions for missing headers with `InvalidSignatureException` and `getContext()['header']` (`X-Samsara-Signature` or `X-Samsara-Timestamp`).

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

### Integrated quality-pass compatibility notes

All quality lanes are included in the release ledger. Applications with exact edge assertions should note that generic item IDs are now encoded as one path segment, mutable date inputs are no longer modified during UTC formatting, inconsistent cursor metadata returns no next page, and malformed webhook timestamps consistently raise `InvalidSignatureException`. See `MIG-19` for exact behavior and tests. Token-cloning and lifecycle-scoping changes are recorded under `MIG-23`.

### Required DTO and enum migrations

#### Update the sleeper-berth enum value

`DutyStatus::SLEEPER_BERTH->value` changes from `sleeperBerth` to the upstream `sleeperBed` value. Code that compares enum cases remains stable. Update persisted strings, serialized fixtures, validation lists, and direct string comparisons to `sleeperBed`. `HosLog::getDutyStatus()` now returns `DutyStatus::SLEEPER_BERTH` for current sleeper-berth rows.

`SafetyEventType` now models the pinned behavior-label schema: eleven historical case names are removed, and `NEAR_COLLISION->value` changes from `nearCollision` to the upstream `nearCollison` spelling. Search all twelve historical values under `MIG-08`, including persisted strings, enum casts, queues, caches, validation rules, and serialized fixtures.

#### Replace removed DTO helpers

The staged-v2 contract reconciliation removed **17 public helpers** whose source fields are absent, renamed, semantically different, or owned by another endpoint. They are not restored by the additive helper wave.

| DTO                 | Removed helpers                                                                                                           | Migration                                                                                                                                                                                                                                                                 |
| ------------------- | ------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `EquipmentLocation` | `getFormattedAddress()`, `getReverseGeoLocation()`                                                                        | Location snapshots have no address fields. For equipment stats GPS, use `EquipmentStats::getGps()` and then `EquipmentGpsLocation::getAddressName()` or `getReverseGeoLocation()`. No formatted-address equivalent exists because the stats address shape is `{id,name}`. |
| `Route`             | `getDistanceKilometers()`, `getDistanceMiles()`, `getDurationHours()`, `getDurationMinutes()`, `isEdited()`, `isPinned()` | The current `/fleet/routes` row lacks those fields. Use the new actual/planned distance helpers on each `RouteStop`; there is no current duration, edited, or pinned replacement.                                                                                         |
| `RouteStop`         | `hasHubLocation()`                                                                                                        | The current stop payload has no `hubLocationId`; use `hasSingleUseLocation()` only when that documented object is relevant.                                                                                                                                               |
| `Trip`              | `getDistanceKilometers()`, `getDistanceMiles()`, `getDrivingTimeHours()`, `getDrivingTimeMinutes()`                       | The current `/trips/stream` row has no distance or driving-duration field. Use `isCompleted()` / `isInProgress()` only for documented state checks; derive telemetry elsewhere if the application needs distance.                                                         |
| `User`              | `isAdmin()`, `isDriver()`                                                                                                 | `authType` now means `default` or `saml`, not authorization. Use `getRoleIds()` and inspect role assignments; use the Drivers resource for driver records.                                                                                                                |
| `Webhook`           | `isActive()`, `isPaused()`                                                                                                | Current webhook responses have no status field. Remove these checks or derive lifecycle state in application-owned data.                                                                                                                                                  |

`EquipmentStats::getGps()` now returns `?EquipmentGpsLocation` instead of `?EquipmentLocation`. Update concrete type declarations and imports. The split is required because stats GPS uses `headingDegrees`, `speedMilesPerHour`, `address`, and `reverseGeo`, while equipment location snapshots use `heading` and `speed` and contain no address data.

#### Update dynamic DTO field access

These entities extend an open fluent data object, so the following are payload/schema migrations rather than declared PHP-property removals. Update array projections, serializers, fixtures, and direct dynamic-field reads.

| DTO                  | Old fields or assumptions                                                                                                  | Current replacement                                                                                                                                                                    |
| -------------------- | -------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `AlertConfiguration` | `description`, `enabled`, `notificationSettings`, `tagIds`, legacy `alertType`                                             | Use `isEnabled` plus current `actions`, `externalIds`, `operationalSettings`, `scope`, and `triggers`. Current responses have no `alertType`; do not use the four legacy type helpers. |
| `AlertIncident`      | `id`, `alertConfigurationId`, `triggeredAtTime`, flat driver/vehicle/location, string status                               | Use `configurationId`, `happenedAtTime`, `conditions`, `incidentUrl`, `isResolved`, and resolution/update timestamps.                                                                  |
| `EquipmentLocation`  | `headingDegrees`, `speedMilesPerHour`, `address`, `reverseGeo`                                                             | Use snapshot fields `heading` and `speed`; address and reverse geocode belong only to `EquipmentGpsLocation`.                                                                          |
| `AssetLocation`      | flat `latitude`, `longitude`, `headingDegrees`, `speedMilesPerHour`, `time`, and `address`                                 | Use nested `location` and `speed`, plus `happenedAtTime`. The nested `asset` shape now uses `id` and optional `externalIds`.                                                           |
| `Document`           | `documentTypeId`, `documentTypeUid`, `submittedAtUtc`; `pending` / `approved` / `rejected` state values                    | Use nested `documentType`; current states are `submitted`, `required`, or `archived`. Current rows can also expose conditional sections, route, and route-stop data.                   |
| `DocumentType`       | `uid`, `isDriverEditable`, `requireDriverSignature`, `fieldDefinitions`, `conditionSets`; string `orgId`                   | Use `conditionalFieldSections` and `fieldTypes`; treat `orgId` as an integer.                                                                                                          |
| `Attribute`          | generic entity/type descriptions and legacy `{id,value}` rows                                                              | Entity type is `driver` or `asset`; use current attribute types and `numberValues`, `stringValues`, `unit`, or `{id,stringValue}` value rows.                                          |
| `AttributeValue`     | `attributeId`, `value`                                                                                                     | Use `stringValue`. `numberValue` remains only as a deprecated compatibility property and is not returned by the current API.                                                           |
| `FormSubmission`     | `formTemplateId`, `driver`, `vehicle`, `draft` / `submitted` status                                                        | Use nested `formTemplate`, `asset`, assignment/submission objects, and the seven current status values documented on the Forms page.                                                   |
| `FormTemplate`       | `name`, numeric `revision`, `fieldDefinitions`                                                                             | Use `title`, `revisionId`, and `fields`, plus current sections and approval metadata.                                                                                                  |
| `LiveShare`          | flat `url`, `status`, `expiresAt`, `createdAtTime`, `assets`, `recipients`                                                 | Use `liveSharingUrl`, `expiresAtTime`, and the current nested configuration objects.                                                                                                   |
| `Route`              | type/hub/plan/dispatch IDs, cost, route distance/duration, edited/pinned flags, legacy timestamps/quantities               | Use current route identity, notes, timezone, scheduled/actual route timestamps, driver/vehicle, external IDs, tags, stops, links, and settings.                                        |
| `Trip`               | `startTime`, `endTime`, `distanceMeters`, `drivingTimeMs`, `driver`, `vehicle`                                             | Use `tripStartTime`, `tripEndTime`, `completionStatus`, timestamps, `asset`, and start/end locations.                                                                                  |
| `User`               | flat role IDs, `tagIds`, user timestamps, role meaning inferred from `authType`                                            | Use nested `roles[*].role`, optional role tag/expiry, and `authType` only for `default` / `saml` authentication.                                                                       |
| `Webhook`            | `secret`, `status`, creation/update timestamps                                                                             | Use `secretKey`, `version`, event types, and current header/name fields.                                                                                                               |
| `ServiceTask`        | `estimatedLaborHours`, `estimatedPartsCostUsd`, `actualLaborHours`, `actualPartsCostUsd`, `createdAtTime`, `updatedAtTime` | Use `estimatedLaborTimeMinutes`, structured `estimatedPartsCost`, `category`, and `subcategory`. The current service-task row has no actual-cost/time or timestamp replacements.       |
| `DefectType`         | `name`                                                                                                                     | Use `label`; current rows also expose `sectionType`, `severity`, and `createdAtTime`.                                                                                                  |
| `Organization`       | `address`, `settings`, and legacy carrier-setting shape                                                                    | Use the current `carrierSettings` shape (`carrierName`, integer `dotNumber`, and `mainOfficeAddress`). No general organization address/settings replacement exists in this row.        |
| `UserRole`           | `description`, `permissions`                                                                                               | Current role rows expose only `id` and `name`.                                                                                                                                         |
| `VisionCamera`       | `id`, `serialNo`, `streamingUrl`                                                                                           | Use integer `cameraId`, `cameraName`, `ethernetIp`, and `wifiIp`.                                                                                                                      |
| `VisionRun`          | string `id`, `startTime`, `endTime`, string `programId`                                                                    | Use `cameraId` or `deviceId`, millisecond `startedAtMs` / `endedAtMs`, integer `programId`, and current program/summary/result objects.                                                |
| `IndustrialAsset`    | `dataInputs`                                                                                                               | Use current `dataOutputs`, `locationDataInput`, `runningStatusDataInput`, parent/custom metadata, and running/location fields.                                                         |
| `Asset`              | `serial`, `assetType`, `purchasePrice`, `gateway`, and `location`                                                          | Use `serialNumber` and `type`. The removed price/gateway/location fields have no current replacement on this row.                                                                      |
| `Defect`             | `defectType`, `mechanicNotesUpdatedAtTime`                                                                                 | Use `defectTypeId` and `defectSafetyStatus`; the current row uses `updatedAtTime` for its last update.                                                                                 |
| `HosViolation`       | `day.date`, `day.timezone`                                                                                                 | Use nullable `day.startTime` and `day.endTime`.                                                                                                                                        |
| `DataPoint`          | numeric-only `float\|null` value and nested `dataInput`                                                                                                                                  | Treat `value` as `mixed` and validate/cast it for the selected input before arithmetic or serialization. Current rows expose optional `fftSpectra` and `gpsLocation`; they no longer include `dataInput`. |
| `GpsLocation`        | integer-only `headingDegrees`                                                                                              | Accept nullable `float` and test fractional headings.                                                                                                                                  |
| `RouteStop`          | `hubLocationId` and the narrow legacy shape                                                                                | Use current stop state, addresses, windows, documents/forms/issues, times/distances/orders, and `singleUseLocation`; there is no hub-location field.                                   |
| `Trailer`            | `assetSerialNumber`                                                                                                        | Use `trailerSerialNumber`.                                                                                                                                                             |
| `Vehicle`            | scalar `grossVehicleWeight`                                                                                                | Treat `grossVehicleWeight` as the current structured response value; list responses expose their own vehicle-weight fields.                                                            |

Applications that copied or extended the package's 13 shipped response fixtures
must migrate their fixture records too. The exact address, driver, DVIR,
equipment, HOS, route, safety-event, trailer, user, vehicle-stat, vehicle,
webhook, and tag replacements are listed under `MIG-08` in the canonical
upgrade guide. Re-run consumer factories, snapshots, serializers, and HTTP-fake
assertions after replacing those shapes.

### Required core migrations

#### Rename the request-attempt configuration

- Replace the `retry` config key with `attempts` and `SAMSARA_RETRY` with `SAMSARA_ATTEMPTS`.
- `attempts` is the maximum total request count, including the initial request. `1` makes one request without retries; `3` permits the initial request plus up to two retries.
- Values below `1` now throw `InvalidArgumentException` during client construction instead of reaching the HTTP client as an invalid retry configuration.
- Update published config files, deployment variables, secret/config stores, test overrides, and calls to `getConfig('retry')`.

#### Bound synchronous rate-limit waits and validate collection envelopes

- Add `retry_after_max_seconds` / `SAMSARA_RETRY_AFTER_MAX_SECONDS` to published configuration. The default ceiling is 30 seconds.
- A 429 whose `Retry-After` exceeds the ceiling is no longer retried inside the current process; the SDK throws `RateLimitException` immediately so the caller can reschedule work. Smaller delta-seconds and HTTP-date delays remain honored.
- Successful collection reads require the collection key to decode to a PHP list. A missing key returns an empty collection. A null or scalar value, a non-list array, or a non-JSON or empty body throws `SamsaraException`. An empty JSON object `{}` decodes as an empty list and is accepted. List items are not validated; a null or scalar item raises `TypeError` during hydration.
- Review response fakes and exception handling under `MIG-21`.

#### Use Laravel's shared HTTP factory for the container binding

- The package binding now resolves `Illuminate\\Http\\Client\\Factory` from Laravel and injects it into the SDK client. `Http::fake()`, `Http::preventStrayRequests()`, global middleware, and global options now cover container/facade-resolved Samsara traffic.
- Standalone `new Samsara(...)` remains supported and creates its own factory when none is injected. Tests for standalone clients may pass an optional third constructor argument or call `setHttpFactory()`.
- Review custom HTTP instrumentation and fakes under `MIG-22`; remove duplicate SDK-only factory setup where Laravel's shared factory is intended.

#### Isolate tenant credentials and lifecycle state

- `withToken()` now returns an isolated clone with an empty resource cache instead of mutating the current client. Retain or chain its return value; a standalone `Samsara::withToken($token);` statement no longer changes later facade calls.
- The Laravel binding is scoped instead of process-wide singleton state, so Octane requests and queue-job lifecycles receive fresh clients.
- `withoutToken()` returns an isolated tokenless clone. `Samsara::make()` inherits the configured region and request settings unless explicitly overridden.
- Review multi-tenant middleware and jobs under `MIG-23`; every tenant execution path must supply its own credentials and region.

#### Harden webhook rejection behavior

- Invalid, missing-header, expired, and future-dated webhook requests now render HTTP 401 through Laravel instead of surfacing as HTTP 500 or accepting a future timestamp.
- `InvalidSignatureException` context no longer contains the valid expected signature; it retains only attacker-supplied received data where applicable.
- The documented `samsara.webhook` alias is registered. Webhook handlers must remain idempotent because a valid request can still be replayed inside the accepted timestamp window.
- Update feature tests and exception-context logging under `MIG-24`.

#### Update legacy v1 verbs and message semantics

- `assetLocations()`, `assetReefers()`, `fleetAssets()`, `hosAuthenticationLogs()`, `maintenanceList()`, `trips()`, and `visionCameras()` now use their pinned GET operations instead of POST.
- `messages()` now means GET/read. Replace calls that used it to send with `sendMessage()`, which owns `POST /v1/fleet/messages`.
- `dispatchRoutes()` is removed because its collection path is absent from the pinned contract; only an unrelated item DELETE exists.
- Update HTTP fakes, proxy rules, and calls under `MIG-25`.

#### Stage exact SamsaraFake request paths

- `SamsaraFake` always prevents stray requests and no longer supplies a zero-stub fallback response.
- Fake helpers and `fakeResponse()` match an exact URL path while ignoring query parameters. A collection path no longer covers item or subresource paths; suffixes from unrelated endpoints cannot collide.
- `assertRequested()` and `assertRequestedWithParams()` use the same exact-path rule, and adding a later fake no longer erases already recorded requests.
- Update tests under the expanded `MIG-18` guidance.

#### Catch SDK exceptions for every wrapped request

- Builder terminals, supported generic methods, and custom resource helpers now use the same typed error boundary, including after retries are exhausted.
- Replace Builder-specific catches for `Illuminate\Http\Client\RequestException` with the SDK exception hierarchy.
- Catch `ValidationException` for HTTP 400 and 422; use `getCode()` only when the distinction matters.
- `find()` still returns `null` for 404. Unsupported generic methods are removed. Explicit unsupported helpers still throw `UnsupportedOperationException` before HTTP.

#### Account for the configured page size

- `paginate()` and `lazy()` now apply configured `per_page` when the query has no explicit limit.
- Explicit terminal sizes, `limit()`, and `where('limit', ...)` retain precedence.
- Update request fakes and signatures that previously expected no `limit`.
- Endpoint helpers whose official cursor contract has no `limit` suppress only the implicit default; do not add a limit unless the endpoint and your use case allow it.

#### Audit pass-through request bodies and required filters

These are current-contract audits for pass-through arrays, not new PHP signature changes. The calls continue to compile with stale keys, but the API does not accept the old shapes:

| Operation         | Stale assumption                                           | Current contract                                                                              |
| ----------------- | ---------------------------------------------------------- | --------------------------------------------------------------------------------------------- |
| Live Share create | flat `assets`, `recipients`, `expiresAt`                   | required `name`, `type`, the matching nested link-config object, and optional `expiresAtTime` |
| DVIR create       | nested `vehicle`, `preTrip`, client timestamps/defect rows | required `authorId`, `safetyStatus`, `type: mechanic`, and either `vehicleId` or `trailerId`  |
| Work Order create | `vehicleId`, `serviceTasks[*].id`                          | `assetId`, `serviceTaskInstances[*].serviceTaskId`, and a current title-cased task status     |

Revalidate read filters too: Trips requires `startTime` and comma-separated asset `ids`; Idling requires both timestamps; Issues and Work Order streams require `startTime`; Speeding requires `assetIds` and `startTime`; assignment feeds require `filterBy` or `driverIds` as documented; HOS daily logs use date keys. The shared Builder does not validate endpoint parameters. On no-limit operations, `lazy($chunkSize)` adds an undocumented `limit`; call `lazy()` without an explicit size. `first()` respects disabled automatic page sizing.

#### Update exact query encoding

- `types`, `expand`, `withDecorations`, `whereAttribute`, `whereDriver`, `whereParentTag`, `whereTag`, and `whereVehicle` encode arrays as comma-separated values.
- Resource-declared form/`explode=true` arrays encode as repeated keys.
- Arbitrary arrays passed to `where()` retain Laravel's standard encoding.
- Top-level boolean query values now encode as `true` / `false`, not `1` / `0`.
- A scalar passed to `whereAttribute`, `whereDriver`, `whereParentTag`, `whereTag`, or `whereVehicle` now produces a scalar value such as `driverIds=d1`, not v1's one-element bracket array.
- Update URL assertions, caches, signatures, and hand-built workarounds. Keep passing logical arrays to dedicated helpers.

Repeated-key parameters now include:

- Vehicle list `attributes`.
- Asset list `attributes` and asset-depreciation `assetIds`.
- Alert configuration `ids` and incident `configurationIds`.
- Camera Media `inputs`, `mediaTypes`, and `triggerReasons`.
- DVIR `safetyStatus`.
- Service-task and work-order `ids`.
- Work-order-stream `workOrderStatuses`, `assetIds`, and `assignedUserIds`.

#### Adopt endpoint-specific DTOs

Concrete result classes now follow the selected operation and persist across `get`, pagination, and lazy traversal. Update concrete type declarations, `instanceof` checks, serializers, and helper calls. See the domain tables below.

#### Update test setup using `Samsara::fake()`

`Samsara::fake()` now installs the fake as the facade root, the `Samsara\Samsara` binding, and the `samsara` alias. Tests that manually swapped or rebound the returned fake should remove duplicate setup and verify their isolation assumptions.

#### Canada is additive

Canadian organizations may set `SAMSARA_REGION=ca` or retain `withRegion('ca')`. Existing US and EU consumers require no change.

### Fleet and platform migrations

#### Drivers

| Old call/assumption               | Required replacement                                                                                                                                                                                                                                              |
| --------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `createAuthToken(string): string` | `createAuthToken(array): DriverAuthToken`; send required 12+ character `code` plus one of integer `driverId`, `externalId`, or `username`, then read `$result->token` and `$result->expirationTime`. Do not confuse this production body with Preview's `id` key. |
| Untyped QR create                 | `createQrCode(array): DriverQrCode`.                                                                                                                                                                                                                              |
| `getQrCodes()` with no IDs        | `getQrCodes(array\|string $driverIds): EntityCollection<DriverQrCode>`.                                                                                                                                                                                                                                                         |
| `deleteQrCode(string $qrCodeId)`  | `deleteQrCode(int $driverId): bool`; the ID is sent in the DELETE body.                                                                                                                                                                                           |
| Untyped/item-path remote sign-out | `remoteSignOut(string $id): DriverRemoteSignOutResult`.                                                                                                                                                                                                     |
| `drivers()->delete($id)`          | No supported driver-delete operation; remove the call.                                                                                                                                                                                                            |

Driver auth uses `POST /fleet/drivers/auth-token`; QR operations use `/drivers/qr-codes`; remote sign-out uses `POST /fleet/drivers/remote-sign-out`.

#### Fleet lookup and collection reads

- Driver, trailer, and vehicle external lookup now encodes the item identifier as `{key}:{value}`. Update exact URL fakes.
- Equipment external lookup is unsupported; retain the Samsara equipment ID and call `find()`.
- Replace `routes()->all()` with `routes()->query()->between($start, $end)->get()`.
- Replace Trip `all()` or an unbounded bare query with `trips()->query()->startTime($start)->where('ids', 'vehicle-1,vehicle-2')->get()`; `ids` contains asset IDs and is required.
- Replace `vehicleStats()->all()` with `current()->types(...)`, `feed()`, or `history()`.
- Live-share `find()` is unsupported. Query the collection; update/delete now send `/live-shares?id={id}`.

#### Fleet telemetry DTOs

| Operation                       | Result class              |
| ------------------------------- | ------------------------- |
| Equipment location snapshot     | `EquipmentLocationResult` |
| Equipment location feed/history | `EquipmentLocationSeries` |
| Equipment stats snapshot        | `EquipmentStats`          |
| Equipment stats feed/history    | `EquipmentStatsSeries`    |
| Vehicle location snapshot       | `VehicleLocationResult`   |
| Vehicle location feed/history   | `VehicleLocationSeries`   |
| Vehicle stats snapshot          | `VehicleStats`            |
| Vehicle stats feed/history      | `VehicleStatsSeries`      |
| Route audit-log feed            | `RouteAuditLog`           |

Trailer DTOs expose `trailerSerialNumber`. Vehicle DTOs expose list fields `vehicleWeight`, `vehicleWeightInKilograms`, and `vehicleWeightInPounds` plus item field `grossVehicleWeight`. Rename consumer projections accordingly.

### Safety and operations migrations

| Old call/assumption                                                                                | Required replacement                                                                                                                                                                                                                                            |
| -------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `deleteConfigurations(array $ids)`                                                                 | Call `deleteConfigurations(string $id)` once per ID.                                                                                                                                                                                                            |
| Camera `get()` returned mapped rows                                                                | Call `get(array $parameters): array` and read the preserved `data` and `pagination` envelope.                                                                                                                                                                   |
| Camera retrieval used `cameraSerial` / `eventId`, response `id`, or top-level `completed` / `url`  | Send required `vehicleId`, `startTime`, `endTime`, and `mediaType`; poll with `retrievalId`; inspect each `media[*].status` and read `media[*].urlInfo.url` only when `available`.                                                                              |
| `forms()->exportPdf(array)`                                                                        | `exportPdf(string $id): array`.                                                                                                                                                                                                                       |
| `forms()->templates()` returned a collection/parent DTO                                            | Add a terminal call; it is now `Builder<FormTemplate>`.                                                                                                                                                                                                         |
| `workOrders()->serviceTasks()` returned parent DTOs                                                | It is now `Builder<ServiceTask>`.                                                                                                                                                                                                                               |
| `trailerAssignments()->forTrailer(string)`                                                         | `forTrailer(int $trailerId, array $params = []): Trailer`.                                                                                                                                                                                                      |
| HOS rows were treated as `HosLog` everywhere                                                       | Use `HosLogsForDriver`, `HosClocksForDriver`, `HosDailyLog`, and `HosViolationGroup`; unwrap with their typed helpers.                                                                                                                                          |
| Safety audit-log rows were `SafetyEvent`                                                           | Use `SafetyEventActivity::getSafetyEvent()` for the nested event.                                                                                                                                                                                               |
| Alert fields `enabled` or string incident status                                                   | Use boolean `isEnabled` and `isResolved`.                                                                                                                                                                                                                       |
| `AlertIncident::isTriggered()` / `isDismissed()`                                                   | Current incidents have no legacy string status. Use `isResolved()` only for actual resolution checks; otherwise inspect conditions or application-owned state.                                                                                                  |
| Alert create/update used flat `type`, `threshold`, or `tagIds`                                     | Create with current required `actions`, `isEnabled`, `name`, `scope`, and `triggers`; update with current configuration fields and collection-body `id`.                                                                                                        |
| Alert incidents were read without bounded configuration filters                                    | Every `/alerts/incidents/stream` read requires `startTime` plus one to 50 repeated `configurationIds`; `endTime` and `after` are optional.                                                                                                                      |
| Form submission create used flat `formTemplateId`, `driverId`, or generic `{id,value}` fields      | Send required `formTemplate: {id,...}` and `status: notStarted`; use `assignedTo: {id,type}` and `{id,type,<type>Value}` field objects.                                                                                                                         |
| Form submissions used a bare GET or generic driver/tag filters                                     | Supply required comma-separated `ids` (and optional comma-separated `include`) or use the time-bounded submissions stream.                                                                                                                                      |
| Form PDF polling used `$export['id']`, `status`, or `url`                                          | Poll `getPdfExport($export['pdfId'])`; read `jobStatus` and `pdfUrl` when `jobStatus === 'done'`.                                                                                                                                                               |
| Fuel report windows used `startTime`/`endTime`                                                     | Reports use `startDate`/`endDate`; efficiency streams continue to use `startTime`/`endTime`.                                                                                                                                                                    |
| Fuel purchase used gallon, scalar-price, total, purchase-time, flat location, or `fuelType` fields | Convert gallons to the required `fuelQuantityLiters` string; use `transactionLocation`, total `{amount,currency}` `transactionPrice`, `transactionReference`, and `transactionTime`; map fuel classification to current `iftaFuelType` values such as `Diesel`. |
| IFTA detail export used quarter examples                                                           | Use `startHour`/`endHour`, split every range into jobs of at most one month, and combine the output files; jurisdiction/vehicle reports retain their own report parameters.                                                                                     |
| IFTA detail polling used `id`, lowercase `status`, or a flat `downloadUrl`                         | Poll with `jobId`, compare case-sensitive `jobStatus`, and read each completed output from `files[*].downloadUrl`.                                                                                                                                              |

Maintenance generic CRUD is no longer callable. `createDvir()` posts to `/fleet/dvirs`; `dvirs()` and `defects()` return builders, with `Defect` selected for defect rows. Use `findDvir()`, `resolveDvir()`, `findDefect()`, `updateDefect()`, and `defectTypes()` for the dedicated current operations.

Work Orders supports `create()` and read builders. Query by repeated `ids`; generic `find`, `update`, and `delete` are removed because their inherited signatures do not match the collection contract. Use `updateWorkOrder()` and `deleteWorkOrder()` for the dedicated collection mutations.

The package still implements legacy `/fleet/safety-events` and its audit feed. The modern `/safety-events` family is deferred; do not change production paths until the SDK exposes and documents that additive replacement.

### Industrial and unstable migrations

#### Assets and Industrial

- Rename `Asset::$assetType` to `type` and `Asset::$serial` to `serialNumber` in projections and serialized output.
- Remove `assets()->find($id)`; it is unsupported.
- Update asset mutation fakes to `PATCH|DELETE /assets?id={id}`.
- Industrial data points now use `/industrial/data-inputs/data-points[/feed|/history]` and return `DataInput` with nested `DataPoint` objects.
- `sensors()->list($data)` is now `sensors()->list()`.

#### Beta helpers

| Old behavior                                                  | Required replacement                                                                                                                                 |
| ------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------- |
| AEMP returned `Builder`                                       | `aempFleet(string $pageNumber): array`; handle the raw Fleet wrapper.                                                                                |
| Flat industrial job body/item path                            | Nest under `job`; update/delete use `/beta/industrial/jobs?id={id}`.                                                                                 |
| Industrial job delete returned `bool`                         | Handle the returned response `array`.                                                                                                                |
| Reading create accepted a flat payload/returned response data | Pass a top-level `data` envelope; handle `bool`.                                                                                                     |
| Immediate device/type/definition/config/course collections    | `devices`, `qualificationsTypes`, `readingsDefinitions`, `reportsConfigs`, and `trainingCourses` return `Builder`; add `get`, `paginate`, or `lazy`. |

Qualification, training, HOS ELD-event, and industrial-job helpers now use their current documented paths. Update exact-path HTTP fakes.

#### Preview helpers

- `createDriverAuthToken(string $driverId, array $data = []): array` becomes `createDriverAuthToken(array $data): array` on `/preview/fleet/drivers/create-auth-token`.
- `lockVehicle()` changes from POST/array to PUT/bool.
- `unlockVehicle()` changes from POST/array to DELETE/bool.

- `createDriverAuthToken(array): array` is payload-only and posts to `/preview/fleet/drivers/create-auth-token`.
- `lockVehicle(string): bool` sends PUT to `/preview/fleet/vehicles/{id}/lock`.
- `unlockVehicle(string): bool` sends DELETE to the same path.

Samsara describes Preview endpoints as nonfunctional and unsuitable for business-critical applications. These methods are contract-stage only.

### Exact generic capability action

Unsupported inherited CRUD methods and every `all()` method are removed. Audit every consumer call to `all`, `find`, `create`, `update`, and `delete` against the [capability matrix](https://github.com/erikgall/samsara/blob/dev/docs/api-sync/documentation-coverage.md#exact-generic-capability-matrix). Do not substitute a raw request unless your application intentionally owns that unsupported or official-only contract.

### Deferred official-only operations

Operations present only in the 2026-08-10 first-party contract remain additive backlog, not implemented behavior. This includes the modern Safety Events family, Attributes, Documents, coaching-session operations, training-assignment mutations, and additional Beta/Preview operations. No consumer migration is required until a later release explicitly adds one.

## 1.1.0 — 2026-05-07

### PHP and Laravel support floor

- **Affected consumers:** Applications on PHP 8.1, Laravel 10, or Laravel 11.
- **Old support:** Version 1.0.x supported PHP 8.1 and Laravel 10–12.
- **New support:** Version 1.1.0 requires PHP 8.2 or later and Laravel 12 or 13.
- **Migration:** Upgrade the application runtime to PHP 8.2 or later and Laravel to version 12 or 13 before upgrading the SDK. Consumers that cannot upgrade must remain on a compatible 1.0.x release and apply their own security and compatibility review.
- **Verification:** Run `composer why-not erikgall/samsara ^1.1` in the consuming application, resolve reported platform or framework constraints, then run that application's full test suite.
