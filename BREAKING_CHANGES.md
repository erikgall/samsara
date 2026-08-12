# Breaking Changes

This file lists verified consumer actions for the integrated staged-v2 behavior. Engineering evidence belongs in `DEVELOPMENT_CHANGELOG.md`.

For an executable, agent-oriented migration sequence with before/after code, exact searches, endpoint-path tables, verification steps, and a 338-path final release ledger, use the canonical [v1.1.0 to v2.0.0 upgrade guide](UPGRADE.md). This file is the compact release record; the upgrade guide is the migration authority.

## Unreleased — staged v2

The final audit maps 195 package operations: 187 match the pinned first-party contract and 8 are explicit legacy helpers. Six matched operations retain only additive pagination-ergonomics gaps; no matched operation retains a classified wire, request-body, response-loss, or error-mapping break. Exact capability gates now reject 133 unsupported public operation calls locally, so consumers must apply the migrations below instead of relying on inherited CRUD. The 183 unique official-only operations are additive backlog and do not require a consumer migration until implemented.

### Integrated quality-pass compatibility notes

All quality lanes are included in the release ledger. No new signature removal or intentional break was added by that pass. Applications with exact edge assertions should note that generic item IDs are now encoded as one path segment, mutable date inputs are no longer modified during UTC formatting, inconsistent cursor metadata returns no next page, and malformed webhook timestamps consistently raise `InvalidSignatureException`. See `MIG-19` for exact behavior and tests.

### Required DTO and enum migrations

#### Update the sleeper-berth enum value

`DutyStatus::SLEEPER_BERTH->value` changes from `sleeperBerth` to the upstream `sleeperBed` value. Code that compares enum cases remains stable. Update persisted strings, serialized fixtures, validation lists, and direct string comparisons to `sleeperBed`. `HosLog::getDutyStatus()` now returns `DutyStatus::SLEEPER_BERTH` for current sleeper-berth rows.

#### Replace removed DTO helpers

The staged-v2 contract reconciliation removed **17 public helpers** whose source fields are absent, renamed, semantically different, or owned by another endpoint. They are not restored by the additive helper wave.

| DTO | Removed helpers | Migration |
|---|---|---|
| `EquipmentLocation` | `getFormattedAddress()`, `getReverseGeoLocation()` | Location snapshots have no address fields. For equipment stats GPS, use `EquipmentStats::getGps()` and then `EquipmentGpsLocation::getAddressName()` or `getReverseGeoLocation()`. No formatted-address equivalent exists because the stats address shape is `{id,name}`. |
| `Route` | `getDistanceKilometers()`, `getDistanceMiles()`, `getDurationHours()`, `getDurationMinutes()`, `isEdited()`, `isPinned()` | The current `/fleet/routes` row lacks those fields. Use the new actual/planned distance helpers on each `RouteStop`; there is no current duration, edited, or pinned replacement. |
| `RouteStop` | `hasHubLocation()` | The current stop payload has no `hubLocationId`; use `hasSingleUseLocation()` only when that documented object is relevant. |
| `Trip` | `getDistanceKilometers()`, `getDistanceMiles()`, `getDrivingTimeHours()`, `getDrivingTimeMinutes()` | The current `/trips/stream` row has no distance or driving-duration field. Use `isCompleted()` / `isInProgress()` only for documented state checks; derive telemetry elsewhere if the application needs distance. |
| `User` | `isAdmin()`, `isDriver()` | `authType` now means `default` or `saml`, not authorization. Use `getRoleIds()` and inspect role assignments; use the Drivers resource for driver records. |
| `Webhook` | `isActive()`, `isPaused()` | Current webhook responses have no status field. Remove these checks or derive lifecycle state in application-owned data. |

`EquipmentStats::getGps()` now returns `?EquipmentGpsLocation` instead of `?EquipmentLocation`. Update concrete type declarations and imports. The split is required because stats GPS uses `headingDegrees`, `speedMilesPerHour`, `address`, and `reverseGeo`, while equipment location snapshots use `heading` and `speed` and contain no address data.

#### Update dynamic DTO field access

These entities extend an open fluent data object, so the following are payload/schema migrations rather than declared PHP-property removals. Update array projections, serializers, fixtures, and direct dynamic-field reads.

| DTO | Old fields or assumptions | Current replacement |
|---|---|---|
| `AlertConfiguration` | `description`, `enabled`, `notificationSettings`, `tagIds`, legacy `alertType` | Use `isEnabled` plus current `actions`, `externalIds`, `operationalSettings`, `scope`, and `triggers`. Current responses have no `alertType`; do not use the four legacy type helpers. |
| `AlertIncident` | `id`, `alertConfigurationId`, `triggeredAtTime`, flat driver/vehicle/location, string status | Use `configurationId`, `happenedAtTime`, `conditions`, `incidentUrl`, `isResolved`, and resolution/update timestamps. |
| `EquipmentLocation` | `headingDegrees`, `speedMilesPerHour`, `address`, `reverseGeo` | Use snapshot fields `heading` and `speed`; address and reverse geocode belong only to `EquipmentGpsLocation`. |
| `FormSubmission` | `formTemplateId`, `driver`, `vehicle`, `draft` / `submitted` status | Use nested `formTemplate`, `asset`, assignment/submission objects, and the seven current status values documented on the Forms page. |
| `FormTemplate` | `name`, numeric `revision`, `fieldDefinitions` | Use `title`, `revisionId`, and `fields`, plus current sections and approval metadata. |
| `LiveShare` | flat `url`, `status`, `expiresAt`, `createdAtTime`, `assets`, `recipients` | Use `liveSharingUrl`, `expiresAtTime`, and the current nested configuration objects. |
| `Route` | type/hub/plan/dispatch IDs, cost, route distance/duration, edited/pinned flags, legacy timestamps/quantities | Use current route identity, notes, timezone, scheduled/actual route timestamps, driver/vehicle, external IDs, tags, stops, links, and settings. |
| `Trip` | `startTime`, `endTime`, `distanceMeters`, `drivingTimeMs`, `driver`, `vehicle` | Use `tripStartTime`, `tripEndTime`, `completionStatus`, timestamps, `asset`, and start/end locations. |
| `User` | flat role IDs, `tagIds`, user timestamps, role meaning inferred from `authType` | Use nested `roles[*].role`, optional role tag/expiry, and `authType` only for `default` / `saml` authentication. |
| `Webhook` | `secret`, `status`, creation/update timestamps | Use `secretKey`, `version`, event types, and current header/name fields. |
| `Asset` | `serial`, `assetType` | Use `serialNumber` and `type`. |
| `Defect` | `defectType` | Use `defectTypeId` and `defectSafetyStatus`. |
| `HosViolation` | `day.date`, `day.timezone` | Use nullable `day.startTime` and `day.endTime`. |
| `DataPoint` | numeric-only `float|null` value | Treat `value` as `mixed` and validate/cast it for the selected input before arithmetic or serialization. |
| `GpsLocation` | integer-only `headingDegrees` | Accept nullable `float` and test fractional headings. |
| `RouteStop` | `hubLocationId` and the narrow legacy shape | Use current stop state, addresses, windows, documents/forms/issues, times/distances/orders, and `singleUseLocation`; there is no hub-location field. |
| `Trailer` | `assetSerialNumber` | Use `trailerSerialNumber`. |
| `Vehicle` | scalar `grossVehicleWeight` | Treat `grossVehicleWeight` as the current structured response value; list responses expose their own vehicle-weight fields. |

### Required core migrations

#### Catch SDK exceptions for every wrapped request

- Builder terminals, supported generic methods, and custom resource helpers now use the same typed error boundary, including after retries are exhausted.
- Replace Builder-specific catches for `Illuminate\Http\Client\RequestException` with the SDK exception hierarchy.
- Catch `ValidationException` for HTTP 400 and 422; use `getCode()` only when the distinction matters.
- `find()` still returns `null` for 404. Unsupported generic operations now throw `UnsupportedOperationException` before any request.

#### Account for the configured page size

- `getWithPagination()`, `paginate()`, and `lazy()` now apply configured `per_page` when the query has no explicit limit.
- Explicit terminal sizes, `limit()`, and `where('limit', ...)` retain precedence.
- Update request fakes and signatures that previously expected no `limit`.
- Endpoint helpers whose official cursor contract has no `limit` suppress only the implicit default; do not add a limit unless the endpoint and your use case allow it.

#### Audit pass-through request bodies and required filters

These are current-contract audits for pass-through arrays, not new PHP signature changes. The calls continue to compile with stale keys, but the API does not accept the old shapes:

| Operation | Stale assumption | Current contract |
|---|---|---|
| Live Share create | flat `assets`, `recipients`, `expiresAt` | required `name`, `type`, the matching nested link-config object, and optional `expiresAtTime` |
| DVIR create | nested `vehicle`, `preTrip`, client timestamps/defect rows | required `authorId`, `safetyStatus`, `type: mechanic`, and either `vehicleId` or `trailerId` |
| Work Order create | `vehicleId`, `serviceTasks[*].id` | `assetId`, `serviceTaskInstances[*].serviceTaskId`, and a current title-cased task status |

Revalidate read filters too: Trips requires `startTime` and comma-separated asset `ids`; Idling requires both timestamps; Issues and Work Order streams require `startTime`; Speeding requires `assetIds` and `startTime`; assignment feeds require `filterBy` or `driverIds` as documented; HOS daily logs use date keys. The shared Builder does not validate endpoint parameters. On no-limit operations, `first()` and `lazy($chunkSize)` add an undocumented `limit`; use `get()->first()` or `lazy()`.

#### Update exact query encoding

- `types`, `expand`, `withDecorations`, `whereAttribute`, `whereDriver`, `whereParentTag`, `whereTag`, and `whereVehicle` encode arrays as comma-separated values.
- Resource-declared form/`explode=true` arrays encode as repeated keys.
- Arbitrary arrays passed to `where()` retain Laravel's standard encoding.
- A scalar passed to `whereAttribute`, `whereDriver`, `whereParentTag`, `whereTag`, or `whereVehicle` now produces a scalar value such as `driverIds=d1`, not v1's one-element bracket array.
- Update URL assertions, caches, signatures, and hand-built workarounds. Keep passing logical arrays to dedicated helpers.

Repeated-key parameters now include:

- Vehicle list `attributes`.
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

Canadian organizations may set `SAMSARA_REGION=ca` or call `useCanadaEndpoint()`. Existing US and EU consumers require no change.

### Fleet and platform migrations

#### Drivers

| Old call/assumption | Required replacement |
|---|---|
| `createAuthToken(string): string` | `createAuthToken(array): DriverAuthToken`; send required 12+ character `code` plus one of integer `driverId`, `externalId`, or `username`, then read `$result->token` and `$result->expirationTime`. Do not confuse this production body with Preview's `id` key. |
| Untyped QR create | `createQrCode(array): DriverQrCode`. |
| `getQrCodes()` with no IDs | `getQrCodes(array|string $driverIds): EntityCollection<DriverQrCode>`. |
| `deleteQrCode(string $qrCodeId)` | `deleteQrCode(int $driverId): bool`; the ID is sent in the DELETE body. |
| Untyped/item-path remote sign-out | `remoteSignOut(string $driverId): DriverRemoteSignOutResult`. |
| `drivers()->delete($id)` | No supported driver-delete operation; remove the call. |

Driver auth uses `POST /fleet/drivers/auth-token`; QR operations use `/drivers/qr-codes`; remote sign-out uses `POST /fleet/drivers/remote-sign-out`.

#### Fleet lookup and collection reads

- Driver, trailer, and vehicle external lookup now encodes the item identifier as `{key}:{value}`. Update exact URL fakes.
- Equipment external lookup is unsupported; retain the Samsara equipment ID and call `find()`.
- Replace `routes()->all()` with `routes()->query()->between($start, $end)->get()`.
- Replace Trip `all()` or an unbounded bare query with `trips()->query()->startTime($start)->where('ids', 'vehicle-1,vehicle-2')->get()`; `ids` contains asset IDs and is required.
- Replace `vehicleStats()->all()` with `current()->types(...)`, `feed()`, or `history()`.
- Live-share `find()` is unsupported. Query the collection; update/delete now send `/live-shares?id={id}`.

#### Fleet telemetry DTOs

| Operation | Result class |
|---|---|
| Equipment location snapshot | `EquipmentLocationResult` |
| Equipment location feed/history | `EquipmentLocationSeries` |
| Equipment stats snapshot | `EquipmentStats` |
| Equipment stats feed/history | `EquipmentStatsSeries` |
| Vehicle location snapshot | `VehicleLocationResult` |
| Vehicle location feed/history | `VehicleLocationSeries` |
| Vehicle stats snapshot | `VehicleStats` |
| Vehicle stats feed/history | `VehicleStatsSeries` |
| Route audit-log feed | `RouteAuditLog` |

Trailer DTOs expose `trailerSerialNumber`. Vehicle DTOs expose list fields `vehicleWeight`, `vehicleWeightInKilograms`, and `vehicleWeightInPounds` plus item field `grossVehicleWeight`. Rename consumer projections accordingly.

### Safety and operations migrations

| Old call/assumption | Required replacement |
|---|---|
| `deleteConfigurations(array $ids)` | Call `deleteConfigurations(string $id)` once per ID. |
| Camera `get()` returned mapped rows | Call `get(array $parameters): array` and read the preserved `data` and `pagination` envelope. |
| Camera retrieval used `cameraSerial` / `eventId`, response `id`, or top-level `completed` / `url` | Send required `vehicleId`, `startTime`, `endTime`, and `mediaType`; poll with `retrievalId`; inspect each `media[*].status` and read `media[*].urlInfo.url` only when `available`. |
| `forms()->exportPdf(array)` | `exportPdf(string $submissionId): array`. |
| `forms()->templates()` returned a collection/parent DTO | Add a terminal call; it is now `Builder<FormTemplate>`. |
| `workOrders()->serviceTasks()` returned parent DTOs | It is now `Builder<ServiceTask>`. |
| `trailerAssignments()->forTrailer(string)` | `forTrailer(int $trailerId, array $params = []): Trailer`. |
| HOS rows were treated as `HosLog` everywhere | Use `HosLogsForDriver`, `HosClocksForDriver`, `HosDailyLog`, and `HosViolationGroup`; unwrap with their typed helpers. |
| Safety audit-log rows were `SafetyEvent` | Use `SafetyEventActivity::getSafetyEvent()` for the nested event. |
| Alert fields `enabled` or string incident status | Use boolean `isEnabled` and `isResolved`. |
| `AlertIncident::isTriggered()` / `isDismissed()` | Current incidents have no legacy string status. Use `isResolved()` only for actual resolution checks; otherwise inspect conditions or application-owned state. |
| Alert create/update used flat `type`, `threshold`, or `tagIds` | Create with current required `actions`, `isEnabled`, `name`, `scope`, and `triggers`; update with current configuration fields and collection-body `id`. |
| Alert incidents were read without bounded configuration filters | Every `/alerts/incidents/stream` read requires `startTime` plus one to 50 repeated `configurationIds`; `endTime` and `after` are optional. |
| Form submission create used flat `formTemplateId`, `driverId`, or generic `{id,value}` fields | Send required `formTemplate: {id,...}` and `status: notStarted`; use `assignedTo: {id,type}` and `{id,type,<type>Value}` field objects. |
| Form submissions used a bare GET or generic driver/tag filters | Supply required comma-separated `ids` (and optional comma-separated `include`) or use the time-bounded submissions stream. |
| Form PDF polling used `$export['id']`, `status`, or `url` | Poll `getPdfExport($export['pdfId'])`; read `jobStatus` and `pdfUrl` when `jobStatus === 'done'`. |
| Fuel report windows used `startTime`/`endTime` | Reports use `startDate`/`endDate`; efficiency streams continue to use `startTime`/`endTime`. |
| Fuel purchase used gallon, scalar-price, total, purchase-time, flat location, or `fuelType` fields | Convert gallons to the required `fuelQuantityLiters` string; use `transactionLocation`, total `{amount,currency}` `transactionPrice`, `transactionReference`, and `transactionTime`; map fuel classification to current `iftaFuelType` values such as `Diesel`. |
| IFTA detail export used quarter examples | Use `startHour`/`endHour`, split every range into jobs of at most one month, and combine the output files; jurisdiction/vehicle reports retain their own report parameters. |
| IFTA detail polling used `id`, lowercase `status`, or a flat `downloadUrl` | Poll with `jobId`, compare case-sensitive `jobStatus`, and read each completed output from `files[*].downloadUrl`. |

Maintenance generic CRUD is no longer callable. `createDvir()` posts to `/fleet/dvirs`; `dvirs()` and `defects()` return builders, with `Defect` selected for defect rows. The official `PATCH /fleet/dvirs/{id}` operation is not yet implemented.

Work Orders supports generic `all` and `create` only. Query by repeated `ids`; generic `find`, `update`, and `delete` are rejected because the official mutations use collection-style identifiers not exposed by those signatures.

The package still implements legacy `/fleet/safety-events` and its audit feed. The modern `/safety-events` family is deferred; do not change production paths until the SDK exposes and documents that additive replacement.

### Industrial and unstable migrations

#### Assets and Industrial

- Rename `Asset::$assetType` to `type` and `Asset::$serial` to `serialNumber` in projections and serialized output.
- Remove `assets()->find($id)`; it is unsupported.
- Update asset mutation fakes to `PATCH|DELETE /assets?id={id}`.
- Industrial data points now use `/industrial/data-inputs/data-points[/feed|/history]` and return `DataInput` with nested `DataPoint` objects.
- `sensors()->list($data)` is now `sensors()->list()`.

#### Beta helpers

| Old behavior | Required replacement |
|---|---|
| AEMP returned `Builder` | `aempFleet(string $pageNumber): array`; handle the raw Fleet wrapper. |
| Flat industrial job body/item path | Nest under `job`; update/delete use `/beta/industrial/jobs?id={id}`. |
| Industrial job delete returned `bool` | Handle the returned response `array`. |
| Reading create accepted a flat payload/returned response data | Pass a top-level `data` envelope; handle `bool`. |
| Immediate device/type/definition/config/course collections | `devices`, `qualificationsTypes`, `readingsDefinitions`, `reportsConfigs`, and `trainingCourses` return `Builder`; add `get`, `paginate`, or `lazy`. |

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

Unsupported inherited operations now fail locally. Audit every consumer call to `all`, `find`, `create`, `update`, and `delete` against the [capability matrix](docs/api-sync/documentation-coverage.md#exact-generic-capability-matrix). Do not substitute a raw request unless your application intentionally owns that unsupported or official-only contract.

### Deferred official-only operations

Operations present only in the 2026-08-10 first-party contract remain additive backlog, not implemented behavior. This includes the modern Safety Events family, `PATCH /fleet/dvirs/{id}`, unwrapped collection mutations not represented by explicit SDK methods, and additional Beta/Preview operations. No consumer migration is required until a later release explicitly adds one.

## 1.1.0 — 2026-05-07

### PHP and Laravel support floor

- **Affected consumers:** Applications on PHP 8.1, Laravel 10, or Laravel 11.
- **Old support:** Version 1.0.x supported PHP 8.1 and Laravel 10–12.
- **New support:** Version 1.1.0 requires PHP 8.2 or later and Laravel 12 or 13.
- **Migration:** Upgrade the application runtime to PHP 8.2 or later and Laravel to version 12 or 13 before upgrading the SDK. Consumers that cannot upgrade must remain on a compatible 1.0.x release and apply their own security and compatibility review.
- **Verification:** Run `composer why-not erikgall/samsara ^1.1` in the consuming application, resolve reported platform or framework constraints, then run that application's full test suite.
