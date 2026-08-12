---
title: Upgrade to v2
layout: default
nav_order: 1.5
description: Explicit migration guide from Samsara SDK v1.1.0 to v2.0.0.
permalink: /upgrade-guide-v2
---

# Upgrade from v1.1.0 to v2.0.0

Version 2 is a breaking contract-alignment release. This guide is the canonical migration authority for developers and coding agents. Follow it in order. Do not infer replacements from method names or bypass a rejected SDK operation with an undocumented raw request.

{: .warning }
> Changing the Composer constraint is not the migration. Complete the searches, code changes, fixture updates, and verification record in this guide before deploying v2.

## What this guide proves

The release audit compares tag `v1.1.0` with audited `dev` head `1750395c98c1724c40c82ba82b96b4866dd2106b`:

- 239 implementation-baseline files were reviewed exactly once at that audited head.
- 5 publication artifacts—the root entry point, canonical guide, final disposition ledger, checker, and Composer command registration—extend the final tag-to-`dev` scope to 244 paths.
- 86 files require a consumer upgrade action.
- 90 files supply tests, contract evidence, or tooling for those actions.
- 12 files are additive and require no migration by themselves.
- 1 file is internal-only.
- 55 files are documentation-only.
- The pinned OpenAPI and runtime ledgers cover 372 official declarations, 195 package operations, 41 concrete resource gates, 240 public request variants, and 170 source files with zero runtime-capture failures.

The machine-readable [v2 disposition ledger](api-sync/v2-upgrade-disposition.json) records all 244 final release paths and their migration section or no-action reason, while retaining the frozen 239-file implementation comparison as a separate proof boundary. `composer check:v2-upgrade-guide` fails if either scope, the counts, section references, or required evidence are incomplete.

## Agent execution contract

An upgrading agent must:

1. Inventory every Samsara call, import, caught exception, serialized DTO field, stored enum value, HTTP fake, cassette, proxy rule, outbound allowlist, cache/signature key, and test fixture in the consuming repository.
2. Apply every relevant `MIG-*` section below; `not used` is valid only when a recorded search returns zero occurrences.
3. Preserve the documented request method, path, query encoding, request body, return type, and nested DTO unwrap.
4. Add or update a focused HTTP-fake test for every Samsara operation used by the application.
5. Run static analysis and the full application test suite.
6. Complete the migration record at the end of this guide. Do not report completion with blank evidence or unreviewed occurrences.

## Required upgrade order

- [ ] `MIG-01`: confirm platform and dependency compatibility.
- [ ] `MIG-02`: inventory and replace unsupported generic CRUD calls.
- [ ] `MIG-03`: update custom query builders and resource subclasses.
- [ ] `MIG-04`: migrate exception handling.
- [ ] `MIG-05`: update pagination expectations.
- [ ] `MIG-06`: update exact query encoding expectations.
- [ ] `MIG-07` and `MIG-08`: migrate DTO classes, wrappers, fields, and helpers.
- [ ] `MIG-09`: migrate the persisted duty-status string.
- [ ] `MIG-10` through `MIG-17`: migrate every resource family the application uses.
- [ ] `MIG-18`: simplify SDK fake installation.
- [ ] `MIG-19`: review additive features without inventing migration work.
- [ ] Complete the verification and completion record.

<a id="mig-01"></a>
## MIG-01 — Platform and dependency preflight

The v2 diff adds no newer Composer platform floor beyond v1.1.0. The package still requires PHP 8.2 or later and Laravel 12 or 13. Applications still on PHP 8.1, Laravel 10, or Laravel 11 must upgrade those dependencies first or remain on a compatible package release.

After a v2 tag exists, run:

```bash
composer why-not erikgall/samsara ^2.0
composer require erikgall/samsara:^2.0 --with-all-dependencies
```

Commit the changed lockfile only after all remaining migrations pass. Verify the runtime and framework actually used by CI and production, not only the local shell.

<a id="mig-02"></a>
## MIG-02 — Generic CRUD is capability-gated

Inherited `all()`, `find()`, `create()`, `update()`, and `delete()` no longer imply that an upstream operation exists. Each resource explicitly enables only the operations represented by its contract. Unsupported calls throw `UnsupportedOperationException` locally before any HTTP request.

First locate files that use this package, then inspect generic CRUD calls only when their receiver is a Samsara resource accessor. The following is a candidate inventory, not a command to rewrite every generic method in the repository:

```bash
rg -l --glob='*.php' 'Samsara\\|Samsara::|erikgall/samsara' \
  | xargs rg -n -- '->(all|find|create|update|delete)\('
```

Check every result against the [exact generic capability matrix](api-sync/documentation-coverage.html#exact-generic-capability-matrix). A few common replacements are:

```php
// Before: the endpoint is a bounded stream, not an unfiltered collection.
$trips = Samsara::trips()->all();

// After: construct the documented stream query.
$trips = Samsara::trips()
    ->query()
    ->startTime($start)
    ->get();
```

```php
// Before: current vehicle stats are not generic CRUD.
$stats = Samsara::vehicleStats()->all();

// After.
$stats = Samsara::vehicleStats()
    ->current()
    ->types(['gps', 'engineStates'])
    ->get();
```

```php
// Before: live-share item lookup is not represented upstream.
$share = Samsara::liveShares()->find($id);

// After: query the collection using documented filters and locate the row.
$share = Samsara::liveShares()->query()->get()
    ->first(fn ($row) => (string) $row->id === $id);
```

Do not replace a rejected operation with a raw request unless the application deliberately owns and tests that upstream contract. At minimum, add one test proving an unsupported call throws before `Http::assertNothingSent()`.

<a id="mig-03"></a>
## MIG-03 — Custom query builders and resource subclasses

This section applies when application or package-extension code implements `QueryBuilderInterface`, extends `Builder`, or extends `Resource`.

Every third-party `QueryBuilderInterface` implementation must add:

```php
public function repeatQueryParameters(string ...$parameters): static
{
    $this->repeatedQueryParameters = array_values(array_unique([
        ...$this->repeatedQueryParameters,
        ...$parameters,
    ]));

    return $this;
}
```

Match the implementation's internal state; the important public contract is the exact signature and fluent `static` return.

`Builder` also now owns these public fluent methods:

- `mapUsing(callable $mapper): static`;
- `repeatQueryParameters(string ...$parameters): static`;
- `usingEntity(string $entityClass): static`; and
- `withoutDefaultPageSize(): static`.

Inspect every custom `Builder` subclass for same-name methods. Remove them or align their visibility and signatures exactly; an incompatible inherited-method collision fails when PHP loads the class.

`Resource` now owns these extension points:

- final protected `executeSdkGet(...)`;
- final protected `executeSdkRequest(...)`;
- protected `supportedGenericOperations(): array`;
- public `getDefaultPerPage(): int`; and
- public `throwIfError(Response $response): void`.

Remove or rename conflicting subclass methods. Do not attempt to override either final execution method. Override `supportedGenericOperations()` only when the subclass intentionally supports exact generic operations:

```php
protected function supportedGenericOperations(): array
{
    return ['all', 'create'];
}
```

Verify with reflection/interface tests, PHPStan, and a boot test that instantiates every custom builder and resource subclass. A fatal method collision is a deployment failure, not a recoverable runtime exception.

<a id="mig-04"></a>
## MIG-04 — SDK exception boundary

Builder terminals, supported generic operations, and explicit resource helpers now use the same SDK error mapping after retries are exhausted.

```php
// Before.
use Illuminate\Http\Client\RequestException;

try {
    $rows = Samsara::trips()->query()->startTime($start)->get();
} catch (RequestException $exception) {
    // ...
}

// After.
use Samsara\Exceptions\AuthenticationException;
use Samsara\Exceptions\RateLimitException;
use Samsara\Exceptions\SamsaraException;
use Samsara\Exceptions\UnsupportedOperationException;
use Samsara\Exceptions\ValidationException;

try {
    $rows = Samsara::trips()->query()->startTime($start)->get();
} catch (ValidationException $exception) {
    // Both HTTP 400 and 422. Read getCode() only if that distinction matters.
} catch (AuthenticationException|RateLimitException $exception) {
    // Domain-specific recovery.
} catch (UnsupportedOperationException $exception) {
    // Fix the call; no request was sent.
} catch (SamsaraException $exception) {
    // Other mapped Samsara API failures.
}
```

`find()` retains one deliberate exception: HTTP 404 returns `null`. Verify fake responses for 400, 422, 404 `find()`, an exhausted retry, and an unsupported operation.

<a id="mig-05"></a>
## MIG-05 — Pagination now honors configured page size

`getWithPagination()`, `paginate()`, and `lazy()` now add configured `samsara.per_page` as `limit` when the query does not already have one. Precedence is:

1. terminal argument, such as `paginate(25)` or `lazy(25)`;
2. `limit(25)`;
3. `where('limit', 25)`;
4. configured `per_page`.

```php
// With samsara.per_page=100, this now sends ?limit=100.
$page = Samsara::drivers()->query()->paginate();

// Sends ?limit=25.
$page = Samsara::drivers()->query()->paginate(25);
```

Some official cursor endpoints declare `after` but no `limit`. Their resource builders call `withoutDefaultPageSize()`, so `getWithPagination()`, `paginate()`, and `lazy()` must not add an implicit limit. Audit every operation used from these resource families: Alerts; Driver Trailer and Driver Vehicle Assignments; Forms; Fuel and Energy; Issues; Speeding; Tachograph; Work Orders; Beta; Routes; Equipment; Industrial Assets and Industrial data; Gateways; Hours of Service; Safety Events; Trips; Vehicle Locations; and Vehicle Stats. The opt-out is operation-specific, so inspect the selected helper rather than assuming every endpoint on a resource has the same pagination contract. Do not compensate by adding a limit unless that endpoint explicitly documents it.

Update exact URL fakes, request signatures, VCR cassettes, and cache keys. Test `getWithPagination()`, `paginate()`, `lazy()`, explicit terminal size, `limit()`, `where('limit')`, and every no-limit endpoint used by the application.

<a id="mig-06"></a>
## MIG-06 — Exact query encoding

Dedicated list helpers now match upstream encoding:

- `types`, `expand`, `withDecorations`, `whereAttribute`, `whereDriver`, `whereParentTag`, `whereTag`, and `whereVehicle` encode arrays as comma-separated values.
- Resource-declared OpenAPI form/`explode=true` lists encode as repeated query keys.
- Arbitrary arrays passed to `where()` retain Laravel's standard bracket encoding.

```php
// Input remains a PHP array.
Samsara::vehicleStats()->current()
    ->whereVehicle(['v1', 'v2'])
    ->types(['gps', 'engineStates'])
    ->get();

// Wire query contains:
// vehicleIds=v1%2Cv2&types=gps%2CengineStates
```

Single-value dedicated helpers also changed from v1's one-element bracket arrays to scalar query values:

```php
Samsara::drivers()->query()->whereDriver('driver-1')->get();

// v1 wire: driverIds%5B0%5D=driver-1
// v2 wire: driverIds=driver-1
```

The same scalar rule applies to `whereAttribute()`, `whereParentTag()`, `whereTag()`, and `whereVehicle()`. Update exact fakes, signatures, and cache keys for both scalar and array inputs.

Repeated-key migrations include:

| Resource operation | Repeated keys |
|---|---|
| Vehicle list | `attributes` |
| Alert configurations/incidents | `ids`, `configurationIds` |
| Camera Media | `inputs`, `mediaTypes`, `triggerReasons` |
| Maintenance DVIR | `safetyStatus` |
| Service tasks | `ids` |
| Work Orders stream | `workOrderStatuses`, `assetIds`, `assignedUserIds` |

Assert the raw URL or raw query string. Comparing only a decoded PHP parameter map can hide CSV-versus-repeated-key defects.

<a id="mig-07"></a>
## MIG-07 — Endpoint-specific DTOs and wrappers

Result classes now follow the selected operation and remain stable through `get()`, `paginate()->items()`, next-page fetches, and `lazy()`.

| Operation | v2 row class | Required unwrap or access |
|---|---|---|
| Equipment `locations()` snapshot | `EquipmentLocationResult` | `$row->getLocation()` |
| Equipment locations feed/history | `EquipmentLocationSeries` | `$row->getLocations()` |
| Equipment `stats()` snapshot | `EquipmentStats` | typed stats getters |
| Equipment stats feed/history | `EquipmentStatsSeries` | typed value-series getters |
| Vehicle locations current | `VehicleLocationResult` | `$row->getLocation()` |
| Vehicle locations feed/history | `VehicleLocationSeries` | `$row->getLocations()` |
| Vehicle stats current | `VehicleStats` | typed stat getters |
| Vehicle stats feed/history | `VehicleStatsSeries` | typed value-series getters |
| Asset locations/speed stream | `AssetLocation` | current asset fields |
| Alert incident stream | `AlertIncident` | current incident fields |
| Forms submissions/templates | `FormSubmission`, `FormTemplate` | current form fields |
| HOS logs/clocks/daily logs/violations | `HosLogsForDriver`, `HosClocksForDriver`, `HosDailyLog`, `HosViolationGroup` | `getLogs()`, `getClocks()`, daily-log fields, `getViolations()` |
| Route audit feed | `RouteAuditLog` | `$row->getRoute()` |
| Safety event audit feed | `SafetyEventActivity` | `$row->getSafetyEvent()` |
| Maintenance defects | `Defect` | current defect fields |
| Industrial inputs/points | `DataInput` | nested `DataPoint` objects |
| Beta driver safety scores | `SafetyScore` | current score fields |
| Beta trailer stats | `TrailerStats` | current trailer-stat fields |

Current snapshot example:

```php
// Before: code assumed each row was the location.
$location = Samsara::vehicleLocations()->current()->get()->first();
echo $location->latitude;

// After: the row describes the vehicle plus its nested location.
$result = Samsara::vehicleLocations()->current()->get()->first();
$location = $result?->getLocation();

if ($location !== null) {
    echo $location->latitude;
}
```

Series example:

```php
$series = Samsara::vehicleLocations()->feed()->get()->first();

foreach ($series?->getLocations() ?? [] as $location) {
    echo $location->time;
}
```

HOS and audit examples:

```php
$logsForDriver = Samsara::hoursOfService()->logs()->get()->first();
foreach ($logsForDriver?->getLogs() ?? [] as $log) {
    $status = $log->getDutyStatus();
}

$activity = Samsara::safetyEvents()->auditLogs()->get()->first();
$event = $activity?->getSafetyEvent();
```

Replace concrete imports, `instanceof` checks, serializer registrations, casts, fixture factories, and direct property reads. Test the class and nested getter on the first page, next page, and lazy traversal for every affected operation the application uses.

<a id="mig-08"></a>
## MIG-08 — DTO fields, removed helpers, and changed behavior

### Removed public helpers

Search these exact method calls and classify each occurrence by receiver/type before replacing it:

```bash
rg -n --glob='*.php' -- 'getFormattedAddress\(|getReverseGeoLocation\(|getDistanceKilometers\(|getDistanceMiles\(|getDurationHours\(|getDurationMinutes\(|isEdited\(|isPinned\(|hasHubLocation\(|getDrivingTimeHours\(|getDrivingTimeMinutes\(|isAdmin\(|isDriver\('
rg -n --glob='*.php' -- '->(isActive|isPaused)\('
```

| DTO | Removed helper | Replacement |
|---|---|---|
| `EquipmentLocation` | `getFormattedAddress()` | No snapshot equivalent. Stats GPS address is `{id,name}`; use `EquipmentStats::getGps()?->getAddressName()`. |
| `EquipmentLocation` | `getReverseGeoLocation()` | Use only on stats GPS: `EquipmentStats::getGps()?->getReverseGeoLocation()`. |
| `Route` | distance km/miles and duration hours/minutes helpers | Current route rows have no route-level distance/duration. Use actual/planned distance helpers on documented stops or application telemetry. |
| `Route` | `isEdited()`, `isPinned()` | No current `/fleet/routes` fields or replacement. Remove the branch or use application-owned state. |
| `RouteStop` | `hasHubLocation()` | No current `hubLocationId`; use `hasSingleUseLocation()` only for that documented object. |
| `Trip` | distance km/miles and driving-time hours/minutes helpers | `/trips/stream` has no distance or driving-duration fields. Derive from another telemetry source if required. |
| `User` | `isAdmin()`, `isDriver()` | `authType` is `default` or `saml`. Inspect `$user->getRoleIds()` and role assignments; use the Drivers resource for drivers. |
| `Webhook` | `isActive()`, `isPaused()` | Current responses have no lifecycle status. Remove the check or store application-owned state. |

`getReverseGeoLocation()` is intentionally present in the search because it was removed from `EquipmentLocation`. Retain or adopt it only when the receiver is the v2 `EquipmentGpsLocation` returned by stats GPS; replace it when the receiver is an equipment location snapshot.

The second search is receiver-sensitive: migrate only calls on `Webhook`. `Driver::isActive()` and `LiveShare::isActive()` remain valid v2 helpers and must not be removed.

Two Alert DTOs retain legacy helpers whose source fields are absent from current responses. The incident helpers are deprecated; all six silently return false on current payloads, so search them even though the methods still exist:

```bash
rg -n --glob='*.php' -- '->(isTriggered|isDismissed|isFuelLevelType|isGeofenceType|isIdleType|isSpeedType)\('
```

- Replace `AlertIncident::isTriggered()` or `isDismissed()` with `isResolved()` only when resolution is the actual business condition. There is no current triggered/dismissed equivalent; inspect `conditions` or store application-owned workflow state.
- Do not use the four `AlertConfiguration` type helpers for current responses. The current schema has no `alertType`; inspect `triggers`, `scope`, and `operationalSettings` for the documented condition your application needs.

`EquipmentStats::getGps()` now returns `?EquipmentGpsLocation`, not `?EquipmentLocation`. Update imports, property assumptions, and type declarations.

### Dynamic DTO payload fields

These DTOs allow dynamic fields, so stale code may not fail until runtime. Search property strings in projections, arrays, serializers, templates, test fixtures, and cached JSON—not only PHP property syntax.

| DTO | Remove or reinterpret | Use in v2 |
|---|---|---|
| `AlertConfiguration` | `description`, `enabled`, `notificationSettings`, `tagIds`, legacy `alertType` | `isEnabled`, `actions`, `externalIds`, `operationalSettings`, `scope`, `triggers`; there is no current `alertType` response field |
| `AlertIncident` | `id`, `alertConfigurationId`, `triggeredAtTime`, flat driver/vehicle/location, string status | `configurationId`, `happenedAtTime`, `conditions`, `incidentUrl`, `isResolved`, resolution/update times |
| `EquipmentLocation` | `headingDegrees`, `speedMilesPerHour`, `address`, `reverseGeo` | snapshot `heading`, `speed`; stats-only fields live on `EquipmentGpsLocation` |
| `FormSubmission` | `formTemplateId`, flat `driver`, flat `vehicle`, `draft`/`submitted` assumptions | nested `formTemplate`, `asset`, assignment/submission objects, current status values |
| `FormTemplate` | `name`, numeric `revision`, `fieldDefinitions` | `title`, `revisionId`, `fields`, sections/approval metadata |
| `LiveShare` | `url`, `status`, `expiresAt`, `createdAtTime`, flat assets/recipients | `liveSharingUrl`, `expiresAtTime`, nested configuration objects |
| `Route` | type/hub/plan/dispatch IDs, cost, route distance/duration, legacy flags/times/quantities | current identity, timestamps, driver/vehicle, tags, stops, links, settings |
| `Trip` | `startTime`, `endTime`, distance/driving time, flat driver/vehicle | `tripStartTime`, `tripEndTime`, `completionStatus`, `asset`, locations |
| `User` | flat role/tag IDs, timestamps, role meaning from `authType` | nested `roles[*].role`, optional role tag/expiry, auth type only for default/SAML |
| `Webhook` | `secret`, `status`, created/updated times | `secretKey`, `version`, event types, headers/name |
| `Asset` | `serial`, `assetType` | `serialNumber`, `type` |
| `Defect` | `defectType` | `defectTypeId`, `defectSafetyStatus` |
| `HosViolation` | `day.date`, `day.timezone` | `day.startTime`, `day.endTime`; both remain optional strings in a nullable day object |
| `DataPoint` | numeric-only `float|null` value assumption | `value` is `mixed`; validate/cast by the selected data input before arithmetic or serialization |
| `GpsLocation` | integer-only `headingDegrees` | nullable `float`; remove strict integer declarations and test fractional headings |
| `RouteStop` | `hubLocationId` and the narrow legacy stop shape | current `state`, address/windows/documents/forms/issues/times/distances/orders and `singleUseLocation`; no hub-location field |
| `Trailer` | `assetSerialNumber` | `trailerSerialNumber` |
| `Vehicle` | integer `grossVehicleWeight` | structured `grossVehicleWeight`; list-specific `vehicleWeight*` fields |

### Form status and Tag control flow

`FormSubmission::isDraft()` and `isSubmitted()` are deprecated because current contract statuses are broader than that binary model. Replace them with an explicit status switch that handles every application-relevant status and a default branch:

```php
$state = match ($submission->status) {
    'notStarted', 'inProgress' => 'open',
    'completed', 'approved' => 'complete',
    'archived', 'needsReview', 'changesRequested' => 'review',
    default => 'unknown',
};
```

Confirm the exact statuses used by your selected Forms endpoint and fixtures.

`Tag::hasParent()` now returns true when nested `parentTag` exists even if `parentTagId` is absent. Review branches that relied on the old false result and test parent by ID, nested parent only, null, and missing fields.

<a id="mig-09"></a>
## MIG-09 — Duty status value

The enum case name remains `DutyStatus::SLEEPER_BERTH`; its serialized value changes:

```php
// v1.1.0
'sleeperBerth'

// v2
'sleeperBed'
```

Search and migrate database rows, JSON, queued payloads, configuration, cache entries, fixtures, validation lists, analytics dimensions, and string comparisons:

```bash
rg -n --hidden --glob='!vendor/**' --glob='!node_modules/**' --glob='!contracts/**' --glob='!docs/**' --glob='!UPGRADE.md' --glob='!BREAKING_CHANGES.md' --glob='!DEVELOPMENT_CHANGELOG.md' --glob='!CHANGELOG.md' 'sleeperBerth'
```

Do not rename the PHP enum case. Verify the old-string count is zero and hydrate/serialize a current HOS log through `DutyStatus::SLEEPER_BERTH`.

<a id="mig-10"></a>
## MIG-10 — Driver methods

| v1.1.0 | v2 |
|---|---|
| `createAuthToken(string $id): string` | `createAuthToken(array $data): DriverAuthToken` |
| untyped QR create | `createQrCode(array $data): DriverQrCode` |
| `getQrCodes()` | `getQrCodes(array\|string $driverIds): EntityCollection<DriverQrCode>` |
| `deleteQrCode(string $qrCodeId)` | `deleteQrCode(int $driverId): bool` |
| item-path/untyped sign-out | `remoteSignOut(string $driverId): DriverRemoteSignOutResult` |
| `drivers()->delete($id)` | unsupported; remove it |

```php
// Before.
$token = Samsara::drivers()->createAuthToken($driverId);

// After. Build the current request object required by your Samsara organization.
$result = Samsara::drivers()->createAuthToken([
    'code' => $oneTimeDriverCode,
    'driverId' => 123,
]);
$token = $result->token;
$expiresAt = $result->expirationTime;
```

The auth `code` is required and must contain at least 12 characters. Identify the driver with an integer `driverId`, an `externalId` formatted as `key:value`, or a `username`. QR creation and deletion also require an integer `driverId`. The separate Preview auth-token endpoint uses `id`; do not copy that Preview-only key into the production `/fleet/drivers/auth-token` body.

Driver auth uses `POST /fleet/drivers/auth-token`; QR operations use `/drivers/qr-codes`; remote sign-out uses `POST /fleet/drivers/remote-sign-out`. QR deletion sends the integer driver ID in the DELETE body. Test exact method, path, query/body, and typed return.

<a id="mig-11"></a>
## MIG-11 — Alerts

| Operation | v1.1.0 wire | v2 wire |
|---|---|---|
| Incident read | `GET /alerts/incidents` | `GET /alerts/incidents/stream` returning `Builder<AlertIncident>` |
| Configuration update | `PATCH /alerts/configurations/{id}` | `PATCH /alerts/configurations` with `id` in body |
| Configuration delete | ID array/body | `DELETE /alerts/configurations?id={id}` one string ID per call |
| Configuration body | flat `type`, `threshold`, and `tagIds` examples | current create requires `actions`, `isEnabled`, `name`, `scope`, and `triggers`; update accepts current fields plus `id` |
| Incident filters | unbounded incident read | every stream read requires `startTime` and one to 50 repeated `configurationIds`; `endTime` and `after` are optional |

```php
// Before.
Samsara::alerts()->deleteConfigurations(['a1', 'a2']);

// After.
foreach (['a1', 'a2'] as $id) {
    Samsara::alerts()->deleteConfigurations($id);
}

$incidents = Samsara::alerts()
    ->incidents()
    ->where('configurationIds', ['a1', 'a2'])
    ->between($start, $end)
    ->get();
```

`ids` and incident `configurationIds` are repeated query keys. Update HTTP fakes and assert both required incident filters, raw repeated-key encoding, current `isEnabled`/`isResolved` payload fields, and the `AlertIncident` result class.

Do not carry forward a flat `threshold`, `alertType`, or `tagIds`. Express the condition in the current `triggers`/`scope` structures and the response action in `actions`; use `operationalSettings` only when the selected upstream trigger documents it. Configuration filtering supports current endpoint parameters such as `ids`, not generic tag filters. Incident filtering supports its documented time window and `configurationIds`.

<a id="mig-12"></a>
## MIG-12 — Camera Media

| v1.1.0 | v2 |
|---|---|
| `/camera-media` | `/cameras/media` |
| `/camera-media/retrieve/{id}` | `/cameras/media/retrieval?retrievalId={id}` |
| mapped rows from `get()` | raw array preserving `data` and `pagination` |
| retrieval body used `cameraSerial` or `eventId` | required `vehicleId`, `startTime`, `endTime`, and `mediaType`; add supported `inputs` or `cameraRoles` as needed |
| create response `id`; poll top-level `status === completed` / `url` | create response `retrievalId`; poll `media[*].status`, and read `media[*].urlInfo.url` only when status is `available` |

The three helpers intentionally return different response shapes:

| v2 method | Request | Return handling |
|---|---|---|
| `get(array $parameters): array` | `GET /cameras/media` | full raw response; preserve `data` and `pagination` |
| `getRetrieval(string $id): array` | `GET /cameras/media/retrieval?retrievalId={id}` | `data` unwrapped when present, otherwise the raw response |
| `retrieve(array $data): array` | `POST /cameras/media/retrieval` | `data` unwrapped when present, otherwise the raw response |

Do not discard the list envelope:

```php
$response = Samsara::cameraMedia()->get($filters);
$rows = $response['data'] ?? [];
$pagination = $response['pagination'] ?? null;

$created = Samsara::cameraMedia()->retrieve([
    'vehicleId' => $vehicleId,
    'startTime' => $start,
    'endTime' => $end,
    'mediaType' => 'videoHighRes',
    'inputs' => ['dashcamRoadFacing'],
]);

$retrieval = Samsara::cameraMedia()->getRetrieval($created['retrievalId']);
foreach ($retrieval['media'] ?? [] as $media) {
    if ($media['status'] === 'available') {
        $url = $media['urlInfo']['url'];
    }
}
```

`inputs`, `mediaTypes`, and `triggerReasons` use repeated query keys on the list operation. Test the exact URL and full envelope for `get()`, the exact current retrieval body, `retrievalId` polling query, and the unwrapped `data` behavior for `getRetrieval()` and `retrieve()`.

<a id="mig-13"></a>
## MIG-13 — Forms

| Operation | v1.1.0 | v2 |
|---|---|---|
| Submission create body | flat `formTemplateId`, `driverId`, and generic `{id,value}` fields | required `formTemplate: {id,...}` and `status: notStarted`; use `assignedTo: {id,type}` and typed field values such as `{id,type:text,textValue:{value:...}}` |
| Submission read | unfiltered or generic driver/tag filters | `GET /form-submissions` requires comma-separated `ids`; optional `include` is also comma-separated |
| PDF export request | `exportPdf(array)` body | `exportPdf(string $submissionId)` and `POST /form-submissions/pdf-exports?id=` |
| PDF export read | item path | `getPdfExport(string $pdfId)` and `GET /form-submissions/pdf-exports?pdfId=` |
| PDF response handling | poll with `$export['id']`; read `status` / `url` | poll with `$export['pdfId']`; read `jobStatus` and, when `done`, `pdfUrl` |
| Submission update | `PATCH /form-submissions/{id}` | collection PATCH with `id` in payload |
| Templates | immediate collection/parent type | `templates(): Builder<FormTemplate>`; call a terminal |

```php
// Before.
$templates = Samsara::forms()->templates();
$submission = Samsara::forms()->createSubmission([
    'formTemplateId' => $templateId,
    'driverId' => $driverId,
    'fields' => [['id' => $fieldId, 'value' => 'Response']],
]);
$export = Samsara::forms()->exportPdf(['id' => $submissionId]);
$pdf = Samsara::forms()->getPdfExport($export['id']);
$status = $pdf['status'];
$url = $pdf['url'];

// After.
$templates = Samsara::forms()->templates()->get();
$submission = Samsara::forms()->createSubmission([
    'formTemplate' => ['id' => $templateId],
    'status' => 'notStarted',
    'assignedTo' => ['id' => $driverId, 'type' => 'driver'],
    'fields' => [[
        'id' => $fieldId,
        'type' => 'text',
        'textValue' => ['value' => 'Response'],
    ]],
]);
$export = Samsara::forms()->exportPdf($submissionId);
$pdf = Samsara::forms()->getPdfExport($export['pdfId']);
$status = $pdf['jobStatus'];
$url = $status === 'done' ? $pdf['pdfUrl'] : null;
```

`formTemplate` and `status` are required for creation. `assignedTo` requires both `id` and `type`; each supplied field requires `id`, `type`, and its type-specific value object. The direct submissions GET requires `ids`, so replace bare reads and unsupported driver/tag filters with a comma-separated ID filter or use the time-bounded stream endpoint for change feeds.

Update submission/template fields under `MIG-08`. Verify exact method/path/query/body, the `pdfId` poll, all current PDF response keys, and `FormTemplate` hydration.

<a id="mig-14"></a>
## MIG-14 — Fleet lookups and collection reads

External ID lookup for drivers, trailers, and vehicles now uses one path identifier encoded as `{key}:{value}`. Update fakes from multi-segment or query assumptions:

```php
$vehicle = Samsara::vehicles()->findByExternalId('vin', $vin);
// Expected item identifier: vin:{value}
```

Equipment external lookup is not supported; retain the Samsara equipment ID and call `find()`.

Other mandatory changes:

- Replace `routes()->all()` with `routes()->query()->between($start, $end)->get()`.
- Replace Trip `all()` and unbounded reads with a `startTime()` and other documented filters.
- Replace `vehicleStats()->all()` with `current()->types(...)`, `feed()`, or `history()`.
- Live-share `find()` is unsupported; update/delete now use `/live-shares?id={id}`.
- Asset `find()` is unsupported; asset update/delete use `/assets?id={id}`.
- Trailer fields use `trailerSerialNumber`; vehicle list fields use `vehicleWeight`, `vehicleWeightInKilograms`, and `vehicleWeightInPounds`.

Common capability removals that must be searched explicitly include:

- `vehicles()->create()` and `vehicles()->delete()`; vehicles support `all`, `find`, and `update` only;
- `equipment()->create()`, `update()`, and `delete()`; equipment supports `all` and `find` only;
- `gateways()->find()` and `gateways()->update()`; gateways support `all`, `create`, and `delete` only;
- `routes()->all()`; routes support item `find`, `create`, `update`, and `delete`, while collection reads require the bounded builder;
- all generic CRUD on Trips, Vehicle Stats, Maintenance, Safety Events, Camera Media, Fuel and Energy, IFTA, Settings, Speeding, Tachograph, Trailer Assignments, Beta, Preview, and Legacy; and
- `find()` on Assets and Live Shares, plus `delete()` on Drivers.

The [exact capability matrix](api-sync/documentation-coverage.html#exact-generic-capability-matrix) is authoritative for every accessor not enumerated here.

Verify exact encoded URLs and local `UnsupportedOperationException` behavior for rejected lookups.

<a id="mig-15"></a>
## MIG-15 — Safety, assignments, maintenance, and work orders

| v1.1.0 assumption | v2 action |
|---|---|
| `trailerAssignments()->forTrailer(string)` | `forTrailer(int $trailerId, array $params = []): Trailer` |
| HOS rows were a single DTO shape | use operation-specific wrappers and their getters from `MIG-07`; `HosClocksForDriver::getClocks()` returns one `HosClock` object, not a collection |
| Safety audit rows were `SafetyEvent` | use `SafetyEventActivity::getSafetyEvent()` |
| Maintenance inherited CRUD | generic CRUD is rejected; use explicit builders/helpers |
| DVIR path `/dvirs` | `POST /fleet/dvirs`; `dvirs()` and `defects()` are builders |
| Work Orders inherited all CRUD | only generic `all` and `create`; use builders for reads |
| Service tasks returned parent DTOs | `serviceTasks(): Builder<ServiceTask>` |

Work Order/service-task IDs and filters use the repeated keys listed in `MIG-06`. Official `PATCH /fleet/dvirs/{id}` is not implemented. The package retains legacy `/fleet/safety-events`; modern `/safety-events` operations are additive backlog. Do not change production paths until explicit package methods exist.

Test every used operation's method, path, query/body, row class, and nested unwrap.

<a id="mig-16"></a>
## MIG-16 — Changed HTTP paths and methods

Even when PHP call sites remain valid, update exact-path fakes, VCR cassettes, outbound allowlists, proxies, telemetry grouping, cache keys, request signatures, and mocks. Search for every old literal used by your application:

| Family/operation | v1.1.0 method and path | v2 method and path |
|---|---|---|
| Asset depreciation | `GET /assets/depreciation/stream` | `GET /assets/depreciation` |
| Asset update/delete | item paths | `PATCH` / `DELETE /assets?id={id}` |
| Alert incidents | `GET /alerts/incidents` | `GET /alerts/incidents/stream` |
| Alert update | `PATCH /alerts/configurations/{id}` | `PATCH /alerts/configurations`, ID in body |
| Alert delete | ID array/body | `DELETE /alerts/configurations?id={id}`, one ID per call |
| Camera list | `GET /camera-media` | `GET /cameras/media` |
| Camera retrieval poll | `GET /camera-media/retrieve/{id}` | `GET /cameras/media/retrieval?retrievalId={id}` |
| Camera retrieval request | legacy camera-media path | `POST /cameras/media/retrieval` |
| Form PDF request | array/body assumptions | `POST /form-submissions/pdf-exports?id={submissionId}` |
| Form PDF poll | item path | `GET /form-submissions/pdf-exports?pdfId={pdfId}` |
| Form submission update | `PATCH /form-submissions/{id}` | `PATCH /form-submissions`, ID in body |
| Fuel purchase create | `POST /fuel-purchases` | `POST /fuel-purchase` |
| Driver efficiency stream | `GET /fuel-energy/driver-efficiency/stream` | `GET /driver-efficiency/drivers` |
| Driver fuel/energy report | `GET /fuel-energy/driver-reports` | `GET /fleet/reports/drivers/fuel-energy` |
| Vehicle efficiency stream | `GET /fuel-energy/vehicle-efficiency/stream` | `GET /driver-efficiency/vehicles` |
| Vehicle fuel/energy report | `GET /fuel-energy/vehicle-reports` | `GET /fleet/reports/vehicles/fuel-energy` |
| IFTA CSV request | `POST /ifta/csv-exports` | `POST /ifta-detail/csv` |
| IFTA CSV poll | `GET /ifta/csv-exports/{id}` | `GET /ifta-detail/csv/{id}` |
| IFTA jurisdiction report | `GET /ifta/jurisdiction-report` | `GET /fleet/reports/ifta/jurisdiction` |
| IFTA vehicle report | `GET /ifta/vehicle-report` | `GET /fleet/reports/ifta/vehicle` |
| Fleet settings reads/writes | `/settings/compliance`, `/settings/driver-app`, `/settings/safety` | `/fleet/settings/compliance`, `/fleet/settings/driver-app`, `/fleet/settings/safety` |
| Route Events | `GET /route-events` | `GET /route-events/stream` |
| Driver tachograph activity | `GET /tachograph/driver-activity-history` | `GET /fleet/drivers/tachograph-activity/history` |
| Driver tachograph files | `GET /tachograph/driver-files/history` | `GET /fleet/drivers/tachograph-files/history` |
| Vehicle tachograph files | `GET /tachograph/vehicle-files/history` | `GET /fleet/vehicles/tachograph-files/history` |
| Maintenance DVIR | `POST /dvirs` | `POST /fleet/dvirs` |
| Industrial point list | `GET /industrial/data-points` | `GET /industrial/data-inputs/data-points` |
| Industrial point feed | `GET /industrial/data-points/feed` | `GET /industrial/data-inputs/data-points/feed` |
| Industrial point history | `GET /industrial/data-points/history` | `GET /industrial/data-inputs/data-points/history` |
| Live Share update/delete | item path | `PATCH` / `DELETE /live-shares?id={id}` |
| Beta AEMP | `GET /aemp/fleet` builder | `GET /beta/aemp/Fleet/{pageNumber}` raw array |
| Beta industrial job create | `POST /industrial/jobs` flat body | `POST /beta/industrial/jobs` with `job` envelope |
| Beta industrial job update/delete | `/industrial/jobs/{id}` | `/beta/industrial/jobs?id={id}` |
| Beta HOS ELD events | `GET /fleet/hos/eld-events` | `GET /beta/fleet/hos/drivers/eld-events` |
| Beta qualification records | `GET /qualifications/records` | `GET /qualification-records` |
| Beta qualification types | `GET /qualifications/types` | `GET /qualification-types` |
| Beta training assignments | `GET /training/assignments` | `GET /training-assignments/stream` |
| Beta training courses | `GET /training/courses` immediate collection | `GET /training-courses` builder |
| Preview driver auth | `POST /fleet/drivers/{id}/auth-token` | `POST /preview/fleet/drivers/create-auth-token` |
| Preview vehicle lock | `POST /fleet/vehicles/{id}/lock` | `PUT /preview/fleet/vehicles/{id}/lock` |
| Preview vehicle unlock | `POST /fleet/vehicles/{id}/unlock` | `DELETE /preview/fleet/vehicles/{id}/lock` |
| Driver auth token | legacy ID argument/item semantics | `POST /fleet/drivers/auth-token` with request payload |
| Driver QR create/get/delete | legacy paths or QR-code item ID | `/drivers/qr-codes`; delete body uses integer `driverId` |
| Driver remote sign-out | item path | `POST /fleet/drivers/remote-sign-out`, `driverId` in body |

Path updates are not the only report migration:

- Fuel/Energy driver and vehicle report calls now use `startDate` and `endDate`. Replace old `startTime`/`endTime` keys and verify the sent query.
- Driver and vehicle efficiency builders still use `startTime` and `endTime`; do not copy report date keys into those stream calls.
- Fuel purchase bodies now require `fuelQuantityLiters` as a string, `transactionLocation`, `transactionReference`, `transactionTime`, and `transactionPrice` as an `{amount,currency}` object. Convert legacy gallon quantities to liters; put the total transaction amount and ISO 4217 currency in `transactionPrice`; and, when fuel classification is needed, replace legacy `fuelType` with the current `iftaFuelType` enum such as `Diesel`.
- IFTA detail CSV creation now uses `startHour` and `endHour`, and each job may cover at most one month. Replace a legacy quarter request with three or more non-overlapping jobs whose individual windows are no longer than one month, then combine their output files in application code. Jurisdiction and vehicle reports keep their own documented report parameters; do not copy the detail-export payload into them. Add an exact-body test for every split window and a negative test or validation proving that a longer window is rejected before production use.
- IFTA detail jobs expose `jobId` and case-sensitive `jobStatus` values (`Requested`, `Processing`, `Completed`, `Failed`). Poll `getDetailCsv($job['jobId'])`; when complete, iterate `files[*].downloadUrl`. Replace legacy top-level `id`, lowercase `status`, and flat `downloadUrl` reads.

Search:

```bash
rg -n --hidden --glob='!vendor/**' --glob='!node_modules/**' --glob='!contracts/**' --glob='!docs/**' --glob='!UPGRADE.md' --glob='!BREAKING_CHANGES.md' --glob='!DEVELOPMENT_CHANGELOG.md' --glob='!CHANGELOG.md' -- '/camera-media|/fuel-purchases|/fuel-energy/|(^|[^A-Za-z0-9/_-])/(ifta/csv-exports|ifta/jurisdiction-report|ifta/vehicle-report|settings/(compliance|driver-app|safety)|route-events|alerts/incidents|dvirs)([^A-Za-z0-9/_-]|$)|/tachograph/|/industrial/data-points|/assets/depreciation/stream|/qualifications/|/training/'
```

The required end state is zero old literals in executable source, configuration, fakes, cassettes, proxies, and tests unless an isolated compatibility suite explicitly proves v1. Historical migration documents and the pinned OpenAPI snapshot are excluded because they intentionally preserve old examples/contracts. For each used resource, assert the exact v2 method, path, raw query, body, and response mapping.

<a id="mig-17"></a>
## MIG-17 — Industrial, Beta, and Preview helpers

### Industrial

- `Asset::$assetType` becomes `type`; `Asset::$serial` becomes `serialNumber`.
- Asset location/speed stream rows are `AssetLocation`.
- Asset depreciation and input builders return generic `Entity` unless a typed entity is explicitly declared.
- Industrial point feeds use the paths in `MIG-16` and return `DataInput` with nested `DataPoint` objects.
- `sensors()->list($data)` becomes parameterless `sensors()->list()`.

### Beta

| v1.1.0 | v2 |
|---|---|
| AEMP returned `Builder` | `aempFleet(string $pageNumber): array` raw Fleet wrapper |
| flat industrial-job body | top-level `job` envelope |
| job item paths | collection path with query `id` |
| job delete returned bool | response `array` |
| flat reading-create payload/result | top-level `data` envelope; `bool` result |
| immediate device/type/definition/config/course collection | `Builder`; call `get`, `paginate`, or `lazy` |

```php
$job = Samsara::beta()->createIndustrialJob([
    'job' => [
        'name' => 'Inspection',
        // current job fields...
    ],
]);
// The caller supplies the required {"job": {...}} envelope unchanged.

$created = Samsara::beta()->createReading([
    'data' => [[
        // current reading fields...
    ]],
]);
// The caller supplies the required {"data": [{...}]} envelope; the method returns bool.

$courses = Samsara::beta()->trainingCourses()->get();
```

Driver safety score rows now use `SafetyScore`; trailer-current-stat rows use `TrailerStats`. Verify class, return `bool` versus `array`, builder terminal, and exact path.

### Preview

| v1.1.0 | v2 |
|---|---|
| `createDriverAuthToken(string $driverId, array $data = []): array` | `createDriverAuthToken(array $data): array`; put the current identifier and request fields in the one payload argument |
| `lockVehicle(string): array` via POST | `lockVehicle(string): bool` via PUT |
| `unlockVehicle(string): array` via POST item action | `unlockVehicle(string): bool` via DELETE of the lock resource |

```php
// Before.
$payload = Samsara::preview()->createDriverAuthToken($driverId, $data);
$lockResponse = Samsara::preview()->lockVehicle($vehicleId);
$locked = (bool) ($lockResponse['success'] ?? false);

// After.
$payload = Samsara::preview()->createDriverAuthToken($data);
$locked = Samsara::preview()->lockVehicle($vehicleId);
$unlocked = Samsara::preview()->unlockVehicle($vehicleId);
```

Preview endpoints are contract-stage and described upstream as nonfunctional. Do not use them for business-critical workflows.

The Preview auth-token body uses integer `id`; the production Driver auth-token body in `MIG-10` uses integer `driverId`. Test these two endpoint-specific payloads separately.

<a id="mig-18"></a>
## MIG-18 — `Samsara::fake()` installs all bindings

`Samsara::fake()` now installs the same fake as the facade root, `Samsara\Samsara` container binding, and `samsara` alias.

```php
// Before: redundant in v2.
$fake = Samsara::fake();
Samsara::swap($fake);
$this->app->instance(\Samsara\Samsara::class, $fake);
$this->app->instance('samsara', $fake);

// After.
$fake = Samsara::fake();
```

Add one test asserting all three resolutions are the exact same instance. Remove manual swaps/rebinds so tests do not hide stale roots.

<a id="mig-19"></a>
## MIG-19 — Additive changes require no migration

The following are optional adoption, not mandatory work:

- Canadian organizations may set `SAMSARA_REGION=ca` or call `useCanadaEndpoint()`.
- Twenty-eight contract-valid convenience helpers were added across DTOs.
- Driver, Vehicle, and Gateway annotations improve static property discovery.
- `AssetLocation` and other endpoint-specific DTOs are additive files but may be required return migrations when an existing operation now selects them; those operations are already covered in `MIG-07`.
- 183 unique official-only OpenAPI operations remain unimplemented and require no consumer migration.
- 68 reviewed helper proposals remain deferred; agents must not invent those methods or consumer migrations.

Use the [DTO helper catalog](api-sync/dto-helper-catalog.html) for accepted helpers, explicit no-helper decisions, and deferred proposals.

<a id="required-consumer-verification"></a>
## Required consumer verification

Run repository-appropriate equivalents of:

```bash
# Removed methods, stale Alert helpers, and old enum value.
rg -n --hidden --glob='!vendor/**' --glob='!node_modules/**' --glob='!contracts/**' --glob='!docs/**' --glob='!UPGRADE.md' --glob='!BREAKING_CHANGES.md' --glob='!DEVELOPMENT_CHANGELOG.md' --glob='!CHANGELOG.md' -- 'sleeperBerth|getFormattedAddress\(|getReverseGeoLocation\(|getDistanceKilometers\(|getDistanceMiles\(|getDurationHours\(|getDurationMinutes\(|isEdited\(|isPinned\(|hasHubLocation\(|getDrivingTimeHours\(|getDrivingTimeMinutes\(|isAdmin\(|isDriver\(|isTriggered\(|isDismissed\(|isFuelLevelType\(|isGeofenceType\(|isIdleType\(|isSpeedType\('

# Receiver-sensitive: only Webhook isActive()/isPaused() calls are stale.
rg -n --hidden --glob='!vendor/**' --glob='!node_modules/**' --glob='!contracts/**' --glob='!docs/**' -- '->(isActive|isPaused)\('

# Old HTTP literals.
rg -n --hidden --glob='!vendor/**' --glob='!node_modules/**' --glob='!contracts/**' --glob='!docs/**' --glob='!UPGRADE.md' --glob='!BREAKING_CHANGES.md' --glob='!DEVELOPMENT_CHANGELOG.md' --glob='!CHANGELOG.md' -- '/camera-media|/fuel-purchases|/fuel-energy/|(^|[^A-Za-z0-9/_-])/(ifta/csv-exports|ifta/jurisdiction-report|ifta/vehicle-report|settings/(compliance|driver-app|safety)|route-events|alerts/incidents|dvirs)([^A-Za-z0-9/_-]|$)|/tachograph/|/industrial/data-points|/assets/depreciation/stream|/qualifications/|/training/'

# Framework exception catches near SDK operations.
rg -n --glob='*.php' 'Illuminate\\Http\\Client\\RequestException|RequestException'

# Project gates: replace with the consuming repository's commands.
vendor/bin/phpstan analyse
vendor/bin/phpunit
```

Focused HTTP tests must cover, for every SDK operation the application uses:

- exact HTTP method and path;
- exact raw query encoding and ID placement;
- exact request body envelope;
- expected typed class or raw-array/bool return;
- nested wrapper getter when present;
- pagination first page, next page, and lazy traversal when used;
- mapped 400, 422, 404-find, retry exhaustion, and unsupported-call cases that affect application control flow; and
- nullable, missing, and empty nested DTO fields used by the application.

Run one sandbox smoke request for every used accessor before production rollout. Do not use production mutations as smoke tests.

## Rollback procedure

Prepare rollback before deploying v2:

1. Record the pre-upgrade application commit and retain its `composer.json` and `composer.lock` as one rollback unit.
2. Snapshot or otherwise make recoverable every persisted value migrated from `sleeperBerth` to `sleeperBed`, plus queues, caches, and serialized DTO payloads that may outlive a deployment.
3. Test restoring the pre-upgrade code and lockfile in a non-production environment. Never downgrade only the package while leaving v2 consumer code active.
4. Before running v1 code again, convert persisted `sleeperBed` values back to `sleeperBerth`, drain or translate v2-shaped queued payloads, and invalidate caches containing endpoint-specific v2 DTO shapes.
5. Re-run the v1 application suite and one read-only sandbox smoke for every restored accessor. Record the rollback evidence with the migration completion table.

If production has already written v2-only state and no tested reverse migration exists, stop the rollback and roll forward with a corrected v2 deployment instead of mixing incompatible code and data.

## Agent completion record

Copy this table into the consuming repository's upgrade pull request. One row per `MIG-*` section is the minimum; split rows by subsystem when the repository is large.

| Section | Search performed | Occurrences before | Migrated | Remaining | Test or proof |
|---|---|---:|---:|---:|---|
| MIG-01 | runtime, Composer, lockfile |  |  |  |  |
| MIG-02 | generic CRUD calls |  |  |  |  |
| MIG-03 | custom builders/resources |  |  |  |  |
| MIG-04 | exception catches |  |  |  |  |
| MIG-05 | pagination/fakes |  |  |  |  |
| MIG-06 | query encoding |  |  |  |  |
| MIG-07 | concrete DTOs/wrappers |  |  |  |  |
| MIG-08 | fields/helpers/control flow |  |  |  |  |
| MIG-09 | persisted enum string |  |  |  |  |
| MIG-10 | Driver methods |  |  |  |  |
| MIG-11 | Alerts |  |  |  |  |
| MIG-12 | Camera Media |  |  |  |  |
| MIG-13 | Forms |  |  |  |  |
| MIG-14 | Fleet lookups/reads |  |  |  |  |
| MIG-15 | Safety/assignments/maintenance/work orders |  |  |  |  |
| MIG-16 | path/method literals and infrastructure |  |  |  |  |
| MIG-17 | Industrial/Beta/Preview |  |  |  |  |
| MIG-18 | fake setup |  |  |  |  |
| MIG-19 | optional adoption only |  |  |  |  |

## Definition of done for a consuming application

- [ ] Composer resolves v2 on the production PHP/Laravel versions.
- [ ] Every Samsara generic CRUD call is allowed by the capability matrix or replaced.
- [ ] Every custom builder/resource extension compiles against v2.
- [ ] No stale exception catch, removed helper call, old persisted enum value, or old wire-path literal remains without an explicit compatibility reason.
- [ ] DTO imports, unwraps, serializers, casts, projections, fixtures, and nullable cases match v2.
- [ ] Every used operation has an exact HTTP contract test.
- [ ] Static analysis, focused integration tests, and the full application suite pass.
- [ ] A sandbox smoke covers every used accessor.
- [ ] Rollback is tested: the previous package constraint and lockfile can be restored without mixing v2 DTO data or `sleeperBed` persistence into code that only understands v1.
- [ ] The completion record has no unexplained remaining occurrences.

<a id="package-maintainer-evidence"></a>
## Package-maintainer evidence

- Compact release record: [Breaking Changes](https://github.com/erikgall/samsara/blob/dev/BREAKING_CHANGES.md)
- Engineering history: [Development Changelog](https://github.com/erikgall/samsara/blob/dev/DEVELOPMENT_CHANGELOG.md)
- Capability and operation audit: [Documentation Coverage](api-sync/documentation-coverage.html)
- Contract drift and runtime evidence: [Contract Drift](api-sync/contract-drift.md)
- DTO helper audit: [DTO Helper Catalog](api-sync/dto-helper-catalog.html)
- Exhaustive changed-file record: [v2 disposition ledger](api-sync/v2-upgrade-disposition.json)

If those artifacts conflict, this guide governs consumer migration steps, while the pinned OpenAPI snapshot and executable package tests govern wire behavior.
