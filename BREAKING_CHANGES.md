# Breaking Changes

This file lists verified consumer actions for the integrated staged-v2 behavior. Engineering evidence belongs in `DEVELOPMENT_CHANGELOG.md`.

## Unreleased — staged v2

The final audit maps 195 package operations: 187 match the pinned first-party contract and 8 are explicit legacy helpers. Six matched operations retain only additive pagination-ergonomics gaps; no matched operation retains a classified wire, request-body, response-loss, or error-mapping break. Exact capability gates now reject 133 unsupported public operation calls locally, so consumers must apply the migrations below instead of relying on inherited CRUD. The 183 unique official-only operations are additive backlog and do not require a consumer migration until implemented.

### Required core migrations

#### Catch SDK exceptions for every wrapped request

- Builder terminals, supported generic methods, and custom resource helpers now use the same typed error boundary, including after retries are exhausted.
- Replace Builder-specific catches for `Illuminate\Http\Client\RequestException` with the SDK exception hierarchy.
- Catch `ValidationException` for HTTP 400 and 422; use `getCode()` only when the distinction matters.
- `find()` still returns `null` for 404. Unsupported generic operations now throw `UnsupportedOperationException` before any request.

#### Account for the configured page size

- `paginate()` and `lazy()` now apply configured `per_page` when the query has no explicit limit.
- Explicit terminal sizes, `limit()`, and `where('limit', ...)` retain precedence.
- Update request fakes and signatures that previously expected no `limit`.
- Endpoint helpers whose official cursor contract has no `limit` suppress only the implicit default; do not add a limit unless the endpoint and your use case allow it.

#### Update exact query encoding

- `types`, `expand`, `withDecorations`, `whereAttribute`, `whereDriver`, `whereParentTag`, `whereTag`, and `whereVehicle` encode arrays as comma-separated values.
- Resource-declared form/`explode=true` arrays encode as repeated keys.
- Arbitrary arrays passed to `where()` retain Laravel's standard encoding.
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
| `createAuthToken(string): string` | `createAuthToken(array): DriverAuthToken`; read `$result->token` and `$result->expirationTime`. |
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
- Replace Trip `all()` or an unbounded bare query with `trips()->query()->startTime($start)->where('ids', 'id1,id2')->get()` as needed.
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
| `forms()->exportPdf(array)` | `exportPdf(string $submissionId): array`. |
| `forms()->templates()` returned a collection/parent DTO | Add a terminal call; it is now `Builder<FormTemplate>`. |
| `workOrders()->serviceTasks()` returned parent DTOs | It is now `Builder<ServiceTask>`. |
| `trailerAssignments()->forTrailer(string)` | `forTrailer(int $trailerId, array $params = []): Trailer`. |
| HOS rows were treated as `HosLog` everywhere | Use `HosLogsForDriver`, `HosClocksForDriver`, `HosDailyLog`, and `HosViolationGroup`; unwrap with their typed helpers. |
| Safety audit-log rows were `SafetyEvent` | Use `SafetyEventActivity::getSafetyEvent()` for the nested event. |
| Alert fields `enabled` or string incident status | Use boolean `isEnabled` and `isResolved`. |
| Fuel windows used `startTime`/`endTime` | Use `startDate`/`endDate`. |
| IFTA detail export used quarter examples | Use `startHour`/`endHour`; jurisdiction/vehicle reports retain their own report parameters. |

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
