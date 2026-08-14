---
title: Documentation Coverage and API Sync
layout: default
nav_order: 98
description: Audited package surface, migration map, and upstream deferrals as of 2026-08-10.
permalink: /api-sync/documentation-coverage
---

# Documentation Coverage and API Sync

This report separates two truths:

- **Package truth** is the integrated source and tests on branch `dev` at the 2026-08-10 documentation reconciliation. It determines what this SDK sends and returns.
- **Upstream truth** is the first-party Samsara OpenAPI and developer guidance retrieved on 2026-08-10. It determines whether an operation is current, legacy, beta, preview, or official-only.

The retrieved OpenAPI is 3.0.1 with `info.version` `2025-10-23`, 260 paths, 372 operations, and US, EU, and Canada servers. The captured bytes have SHA-256 `a7255e2d5b7a94eea97e11f47db1c7b969931f639366bcae49e4922b02ba3fc6`, `Last-Modified: 2026-08-10T14:51:20.878Z`, and `ETag: W/"40311a-FNzOZFT6SXU1AULxdChC0l9RNhU"`. An upstream operation absent from the matrices below is not silently implemented.

## Shared public behavior

- `SAMSARA_REGION` accepts `us`, `eu`, and `ca`; runtime switches are `useUsEndpoint()`, `useEuEndpoint()`, and `useCanadaEndpoint()`.
- `paginate()` and `lazy()` apply configured `per_page` when no explicit limit exists. Explicit terminal arguments, `limit()`, and `where('limit', ...)` win.
- Resource helpers call `withoutDefaultPageSize()` for cursor operations that have `after` but no official `limit`. The builder preserves caller-supplied limits, but callers must omit them on operations whose contract does not declare `limit`; the opt-out persists across cursor traversal.
- Dedicated list helpers use CSV values. OpenAPI form/`explode=true` arrays explicitly declared by a resource use repeated query keys. Arbitrary arrays passed to `where()` retain Laravel's standard encoding.
- `get()` returns one HTTP response page; `paginate()` returns one page plus cursor metadata; `lazy()` walks the cursor sequence. A short or empty page does not end traversal when `hasNextPage` is true.
- `usingEntity()` and `mapUsing()` retain endpoint-specific mapping across `get`, `paginate`, `nextPage`, and `lazy`.
- Supported generic operations, custom helpers, and all Builder terminals use the shared typed request executor. HTTP 400/422 map to `ValidationException`; 401/403/404/429/5xx use status-specific SDK exceptions. `find()` returns `null` for 404. Unsupported generic calls fail before HTTP with operation context.
- `Samsara::fake()` swaps the facade root, the `Samsara\Samsara` binding, and the `samsara` alias to the same fake.

## Exact generic capability matrix

“None” means every inherited `all`, `find`, `create`, `update`, and `delete` call is rejected unless the notes name an explicit override. Introspection (`client`, `getEndpoint`, `getEntityClass`) remains public but is not an upstream operation.

| Accessor | Resource page | Supported generic operations | Explicit boundary |
|---|---|---|---|
| `drivers()` | [Drivers](../resources/drivers.html) | all, find, create, update | delete rejected; custom token/QR/sign-out operations |
| `vehicles()` | [Vehicles](../resources/vehicles.html) | all, find, update | create/delete rejected |
| `trailers()` | [Trailers](../resources/trailers.html) | all, find, create, update, delete | external item lookup uses `key:value` |
| `equipment()` | [Equipment](../resources/equipment.html) | all, find | create/update/delete and external lookup rejected |
| `vehicleStats()` | [Vehicle Stats](../resources/vehicle-stats.html) | none | current/feed/history builders only |
| `vehicleLocations()` | [Vehicle Locations](../resources/vehicle-locations.html) | all | query/current/feed/history builders |
| `trips()` | [Trips](../resources/trips.html) | none | stream builders only |
| `hoursOfService()` | [Hours of Service](../resources/hours-of-service.html) | all | logs/clocks/daily logs/violations builders |
| `maintenance()` | [Maintenance](../resources/maintenance.html) | none | create/find/resolve DVIR helpers, defect find/update, defect types, and DVIR/defect builders |
| `safetyEvents()` | [Safety Events](../resources/safety-events.html) | none | event and activity builders; implemented paths are upstream legacy |
| `routes()` | [Routes](../resources/routes.html) | find, create, update, delete | collection reads use `query()->between(...)` |
| `addresses()` | [Addresses](../resources/addresses.html) | all, find, create, update, delete | query builder also available |
| `users()` | [Users](../resources/users.html) | all, find, create, update, delete | query builder also available |
| `tags()` | [Tags](../resources/tags.html) | all, find, create, update, delete | query builder also available |
| `contacts()` | [Contacts](../resources/contacts.html) | all, find, create, update, delete | query builder also available |
| `assets()` | [Assets](../resources/assets.html) | all, create | explicit `update`/`delete` overrides use `/assets?id=`; find rejected |
| `industrial()` | [Industrial](../resources/industrial.html) | all, create, update, delete | endpoint-specific asset/data-input builders |
| `sensors()` | [Sensors](../resources/sensors.html) | none | legacy explicit methods only |
| `gateways()` | [Gateways](../resources/gateways.html) | all, create, delete | find/update rejected |
| `liveShares()` | [Live Shares](../resources/live-shares.html) | all, create, update, delete | update/delete use collection query ID; find rejected |
| `webhooks()` | [Webhooks](../resources/webhooks.html) | all, find, create, update, delete | query builder also available |
| `alerts()` | [Alerts](../resources/alerts.html) | all, create | explicit collection update/delete plus two builders |
| `cameraMedia()` | [Camera Media](../resources/camera-media.html) | none | explicit raw-envelope media/retrieval helpers |
| `carrierProposedAssignments()` | [Carrier Proposed Assignments](../resources/carrier-proposed-assignments.html) | all, create, delete | query builder; find/update rejected |
| `driverTrailerAssignments()` | [Assignments](../resources/assignments.html) | create | query builder plus dedicated collection update; other generic verbs rejected |
| `driverVehicleAssignments()` | [Assignments](../resources/assignments.html) | create | query builder plus dedicated collection update/delete; other generic verbs rejected |
| `forms()` | [Forms](../resources/forms.html) | create | explicit update, PDF, submission/template builders |
| `fuelAndEnergy()` | [Fuel and Energy](../resources/fuel-and-energy.html) | none | explicit report/efficiency/purchase helpers |
| `hubs()` | [Hubs](../resources/hubs.html) | all | query builder; other generic verbs rejected |
| `idling()` | [Idling](../resources/idling.html) | none | events builder only |
| `ifta()` | [IFTA](../resources/ifta.html) | none | four raw-array report/export helpers |
| `issues()` | [Issues](../resources/issues.html) | create | stream builder plus dedicated collection update; other generic verbs rejected |
| `routeEvents()` | [Route Events](../resources/route-events.html) | all | stream query builder |
| `settings()` | [Settings](../resources/settings.html) | none | five explicit raw-array helpers |
| `speeding()` | [Speeding](../resources/speeding.html) | none | intervals stream builder |
| `tachograph()` | [Tachograph](../resources/tachograph.html) | none | three history builders |
| `trailerAssignments()` | [Assignments](../resources/assignments.html) | none | typed collection query and `forTrailer(int, array): Trailer` |
| `workOrders()` | [Work Orders](../resources/work-orders.html) | all, create | query/stream/service-task/invoice helpers plus dedicated collection update/delete |
| `beta()` | [Beta](../resources/beta.html) | none | explicit mixed-stage operations only |
| `preview()` | [Preview](../resources/preview.html) | none | three contract-stage methods only |
| `legacy()` | [Legacy](../resources/legacy.html) | none | explicit legacy methods only |

## Consumer migration map

### Core

| Before | Integrated behavior / action |
|---|---|
| `per_page` had no effect | Expect it as the implicit `limit` for `paginate()`/`lazy()`, or set an explicit limit. |
| Builder errors could become empty results or Laravel request exceptions | Catch the same typed SDK exceptions for Builder and resource calls; validation may be HTTP 400 or 422. |
| List helpers used mixed PHP-array encoding | Expect CSV for dedicated list helpers. Use resource-declared repeated keys only for `explode=true` operations. |
| No Canada selection | Use `SAMSARA_REGION=ca` or `useCanadaEndpoint()`. |
| `Samsara::fake()` returned an isolated object | Subsequent facade and container resolution now uses the installed fake. |
| Resource-wide entity mapping | Expect endpoint-specific DTOs listed below. |

### Fleet and platform

| Before | Current method / wire contract / return |
|---|---|
| `createAuthToken(string): string` | `createAuthToken(array): DriverAuthToken`; `POST /fleet/drivers/auth-token`. |
| QR helpers used old paths and untyped results | `createQrCode(array): DriverQrCode`; `getQrCodes(array\|string): EntityCollection<DriverQrCode>`; `/drivers/qr-codes`. |
| `deleteQrCode(string)` deleted a path ID | `deleteQrCode(int $driverId): bool`; `driverId` is in the DELETE body. |
| Remote sign-out was untyped/item-path | `remoteSignOut(string): DriverRemoteSignOutResult`; POST collection body `driverId`. |
| External item lookup encoded `externalIds[key]` | Driver, trailer, and vehicle lookups pass item ID `key:value`; equipment external lookup is unsupported. |
| `routes()->all()` | `routes()->query()->between(...)->get()`; generic all is rejected. |
| `trips()->all()` or an unbounded bare query | `trips()->query()->startTime(...)->where('ids', 'id1,id2')->get()`. |
| `vehicleStats()->all()` | `current()->types(...)`, `feed()`, or `history()` typed builders. |
| Live-share `find()` | No single-item operation; query the collection. Update/delete use `/live-shares?id=`. |
| One DTO shape for snapshots and series | Equipment locations: `EquipmentLocationResult` vs `EquipmentLocationSeries`; equipment stats: `EquipmentStats` vs `EquipmentStatsSeries`; vehicle locations: `VehicleLocationResult` vs `VehicleLocationSeries`; vehicle stats: `VehicleStats` vs `VehicleStatsSeries`. |

Fleet/platform has 15 implemented cursor operations without official `limit`; those builders suppress the implicit page size and callers must not add an explicit one. `GET /fleet/vehicles` attributes is the sole owned Fleet/platform `explode=true` repeated-key array. Trailer DTOs expose `trailerSerialNumber`. Vehicle DTOs retain list fields `vehicleWeight`, `vehicleWeightInKilograms`, and `vehicleWeightInPounds` plus item field `grossVehicleWeight`.

### Safety and operations

| Before | Current method / wire contract / return |
|---|---|
| `deleteConfigurations(array)` | `deleteConfigurations(string): bool`; one collection query-ID call per configuration. |
| Camera `get()` returned mapped rows | `get(array): array` preserves the full `/cameras/media` envelope and pagination. |
| `forms()->exportPdf(array)` | `exportPdf(string $submissionId): array`; collection query ID. |
| Templates/service tasks shared the parent DTO | `templates(): Builder<FormTemplate>` and `serviceTasks(): Builder<ServiceTask>`. |
| `forTrailer(string)` returned generic assignments | `forTrailer(int, array = []): Trailer`. |
| HOS endpoint rows shared `HosLog` | `HosLogsForDriver`, `HosClocksForDriver`, `HosDailyLog`, and `HosViolationGroup` wrappers expose nested typed data. |
| Safety activity rows used `SafetyEvent` | `auditLogs(): Builder<SafetyEventActivity>`; call `getSafetyEvent()` for the nested event. |
| Alerts used `enabled` and string status | Configuration field is boolean `isEnabled`; incident field is boolean `isResolved`. |
| Fuel report windows used timestamp names | Report parameters use `startDate` and `endDate`; efficiency builders continue to use `startTime` and `endTime`. |
| IFTA detail used year/quarter in export examples | CSV detail accepts `startHour` and `endHour`. |

Repeated-key arrays are declared for alert configuration `ids`, incident `configurationIds`, Camera Media `inputs`/`mediaTypes`/`triggerReasons`, DVIR `safetyStatus`, service-task `ids`, work-order `ids`, and work-order-stream `workOrderStatuses`/`assetIds`/`assignedUserIds`.

### Industrial and unstable

| Before | Current method / wire contract / return |
|---|---|
| Asset fields `assetType`, `serial` | `type`, `serialNumber`. |
| Asset `find()` and item mutation paths | find is rejected; update/delete use `/assets?id={id}`. |
| `sensors()->list($data)` | `sensors()->list(): EntityCollection`. |
| `aempFleet()` returned a builder | `aempFleet(string $pageNumber): array`; raw `/beta/aemp/Fleet/{pageNumber}` wrapper. |
| Industrial data points omitted `data-inputs` | `/industrial/data-inputs/data-points[/feed\|/history]`, returning `DataInput` with nested `DataPoint`. |
| Job body/path used flat item semantics | Nest `job`; update/delete use `/beta/industrial/jobs?id=`; delete returns a response array. |
| Reading create returned response data | Supply a top-level `data` envelope; `createReading(array): bool`. |
| Device/type/definition/config/course helpers returned immediate collections | `devices`, `qualificationsTypes`, `readingsDefinitions`, `reportsConfigs`, and `trainingCourses` now return `Builder`. |
| Preview driver ID argument and POST locks | `createDriverAuthToken(array): array`; lock is PUT and unlock is DELETE on `/preview/fleet/vehicles/{id}/lock`, both returning `bool`. |

## Documentation page coverage

| Page group | Audited surface | Result |
|---|---|---|
| `README.md` | install, config, regions, resources, query, errors, testing | Reconciled |
| `docs/index.md`, `getting-started.md`, `configuration.md` | entry path, config keys, Canada, active page-size default | Reconciled |
| `docs/query-builder.md` | query encoding, one-page vs lazy traversal, no-limit opt-out, endpoint mappers | Reconciled |
| `docs/error-handling.md`, `testing.md` | shared typed boundary, 400/422, capability errors, facade fake | Reconciled |
| `docs/enums.md` | package enum surface and upstream caveats | Reconciled; vehicle-stat and webhook additions are complete for the pinned contract |
| `docs/api-maintenance.md` | snapshot and maintenance process | Reconciled |
| 39 topical resource pages | all 41 resource accessors plus `me()`; Assignments covers three accessors | Reconciled against integrated source/tests |
| `DEVELOPMENT_CHANGELOG.md` | engineering sequence and evidence | Reconciled |
| `BREAKING_CHANGES.md` | consumer migration actions | Reconciled |

## Deferred official-only operations

This reconciliation does not implement every operation that exists only in the first-party contract. Examples include the modern `/safety-events` replacement family, the Attributes and Documents families, coaching-session operations, `PUT /tags/{id}`, training-assignment mutations, and additional beta/preview operations outside `BetaResource`/`PreviewResource`. The 111 official-only Beta/Preview operations remain additive backlog, not promised SDK coverage.

Enums remain bounded by package source. `VehicleStatType` and `WebhookEvent` now cover the pinned contract, but do not infer that every other package enum exhausts current alert triggers, work-order statuses, document types, route states, HOS types, or safety behavior labels. See [Enums](../enums.html) for exact shipped values and known upstream differences.

## Enum gaps

`VehicleStatType` now exposes exactly the 63 values accepted by each pinned vehicle-stats `types` parameter; the feed/history `decorations` parameters accept a 49-value subset, and unsupported historical aliases are removed. `WebhookEvent` exposes all 35 pinned event values. Other enum families remain subject to the bounded-source caveat above. See [Enums](../enums.html#vehiclestattype) for the exact shipped values.
