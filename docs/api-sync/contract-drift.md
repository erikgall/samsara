# Samsara API final integrated contract reconciliation

Audit date: 2026-08-10
Authoritative package worktree: `/Users/erikgall/Code/samsara` (`dev`, dirty working tree preserved)
Authoritative official contract: `contracts/samsara/samsara-api-2026-08-10.json`

## Verdict

The current package surface contains **195** unique fixed wire operations. **187** match the pinned contract by normalized HTTP method and path. All 187 are wire-accurate across method/path, fixed parameter placement and serialization, request/response wrapper behavior, and centralized errors. Six matched direct helpers preserve the official pagination envelope but do not expose automatic `Builder`/`CursorPaginator` traversal, so they remain classified additive changes; 181 matched operations have no classified difference. This is transport-contract proof, not strict generated-model equality: request arrays and `Builder::where()` remain open pass-through inputs, and many response types are open `Fluent` entities or raw arrays.

Eight implemented legacy helpers remain outside the pinned contract. They are the only unresolved package-only operations and are listed below. Capability gates leave **69** supported inherited bindings, all official; **0** inherited phantom operation remains.

## Exact counts

| Measure | Count | Definition |
|---|---:|---|
| Official operation declarations | 372 | Every HTTP operation under the pinned OpenAPI `paths` object. |
| Official unique normalized wire operations | 370 | Placeholder spelling normalized to `{}`; two legacy alias pairs collapse. |
| Package operations | 195 | Unique fixed method/path operations observed from public resource methods after capability gates. |
| Matched operations | 187 | Same normalized method/path; no duplicate official alias is matched. |
| Official-only declarations | 185 | 183 unique normalized wire operations. |
| Package-only operations | 8 | All explicit `LegacyResource` compatibility methods. |
| Changed matched operations | 6 | Confirmed contract-surface difference: six additive paginator omissions, with no wire mismatch. |
| Matched without confirmed change | 181 | Transport-contract accurate within the proof boundary above. |
| Unclassified differences | **0** | All 185 official-only gaps and six pagination ergonomics gaps are additive; all 8 package-only gaps are breaking; root-baseline disposition is docs-only. |

Arithmetic passed: `187 + 185 = 372` official declarations, `187 + 183 = 370` official unique wire operations, and `187 + 8 = 195` package operations.

## Implemented-surface disposition

The audit observed 240 public SDK method request variants and collapsed aliases to 195 wire operations. The runtime probe had zero failures. All 41 concrete resources override `supportedGenericOperations()`; `Resource` checks the gate before HTTP at `src/Resources/Resource.php:73-75`, `:108-110`, `:133-136`, `:156-159`, and `:224-227`. The gates permit 69 inherited bindings, and the reconciliation matched all 69 to official operations. Another 132 inherited method/resource combinations throw `UnsupportedOperationException` before HTTP at `src/Resources/Resource.php:382-399`; `EquipmentResource::findByExternalId()` is one additional explicit unsupported alias.

Matched request schemas are supported as transport contracts rather than locally generated validators: generic writes pass caller arrays through, builders allow contract field pass-through, and fixed helpers have exact outbound method/path/query/body tests. Response decoding is either raw JSON, an open entity that retains fields, or an endpoint-specific mapper. `Builder` and direct resources share `RequestExecutor` (`src/Query/Builder.php:521-536`, `src/Resources/Resource.php:278-288`); 400/422, 401, 403, 404, 429, and 5xx map through `src/Resources/Resource.php:302-337`. Cursor pagination and configured page size run through `src/Query/Builder.php:225-275` and `:505-516`.

These facts do **not** prove server runtime behavior, reject invalid request fields locally, or establish generated-model equality for all 5,157 official schemas. The exhaustive operation parameters, bodies, responses, enums, pagination facts, SDK accessors, response mappers, and proof limits are in `docs/api-sync/contract-inventory.json`.

### Unresolved implemented operations

| Package operation | Evidence | Disposition |
|---|---|---|
| `GET /v1/fleet/dispatch/routes` | `src/Resources/Legacy/LegacyResource.php:58` | Breaking: compatibility helper has no declaration in the pinned contract. |
| `POST /v1/fleet/assets` | `src/Resources/Legacy/LegacyResource.php:82` | Breaking: compatibility helper has no declaration in the pinned contract. |
| `POST /v1/fleet/assets/locations` | `src/Resources/Legacy/LegacyResource.php:36` | Breaking: compatibility helper has no declaration in the pinned contract. |
| `POST /v1/fleet/assets/reefers` | `src/Resources/Legacy/LegacyResource.php:47` | Breaking: compatibility helper has no declaration in the pinned contract. |
| `POST /v1/fleet/hos_authentication_logs` | `src/Resources/Legacy/LegacyResource.php:93` | Breaking: compatibility helper has no declaration in the pinned contract. |
| `POST /v1/fleet/maintenance/list` | `src/Resources/Legacy/LegacyResource.php:115` | Breaking: compatibility helper has no declaration in the pinned contract. |
| `POST /v1/fleet/trips` | `src/Resources/Legacy/LegacyResource.php:137` | Breaking: compatibility helper has no declaration in the pinned contract. |
| `POST /v1/industrial/vision/cameras` | `src/Resources/Legacy/LegacyResource.php:174` | Breaking: compatibility helper has no declaration in the pinned contract. |

These eight methods preserve old compatibility shapes, but the dated contract declares different current v1 verbs and item paths for most of them. Implementation direction: deprecate/remove each helper or deliberately migrate it to a declared operation with exact request/response/error tests. Until then, do not count them as current-contract coverage.

### Matched operations with additive pagination ergonomics (6)

The following operations preserve the full official response envelope and accept pass-through cursor parameters, so no response data is lost. They lack an automatic `Builder`/`CursorPaginator` traversal surface and therefore remain `changed` under the inventory's SDK-pagination definition:

- `GET /cameras/media` — `src/Resources/Additional/CameraMediaResource.php:27`; fixture/test evidence at `tests/Unit/Resources/Additional/CurrentContractAResourceTest.php:137`.
- `GET /fleet/reports/drivers/fuel-energy` — `src/Resources/Additional/FuelAndEnergyResource.php:54`; `tests/Unit/Resources/Additional/CurrentContractBResourceTest.php:231`.
- `GET /fleet/reports/ifta/vehicle` — `src/Resources/Additional/IftaResource.php:77`; `tests/Unit/Resources/Additional/CurrentContractBResourceTest.php:335`.
- `GET /fleet/reports/vehicles/fuel-energy` — `src/Resources/Additional/FuelAndEnergyResource.php:80`; `tests/Unit/Resources/Additional/CurrentContractBResourceTest.php:246`.
- `GET /v1/fleet/assets/locations` — `src/Resources/Industrial/AssetsResource.php:81`; `tests/Unit/Resources/Industrial/AssetsResourceTest.php:108`.
- `GET /v1/fleet/assets/reefers` — `src/Resources/Industrial/AssetsResource.php:107`; `tests/Unit/Resources/Industrial/AssetsResourceTest.php:134`.

Implementation direction: add an operation-specific builder/paginator without removing the raw-envelope helper, then test `after`, `pagination.endCursor`, and `pagination.hasNextPage` across multiple pages.

## Official-only backlog by lifecycle

| Lifecycle bucket | Official total | Implemented matches | Official-only declarations | Official-only unique wire operations |
|---|---:|---:|---:|---:|
| Stable residual | 241 | 167 | 74 | 72 |
| Beta | 123 | 17 | 106 | 106 |
| Preview | 8 | 3 | 5 | 5 |

Stage classification uses only contract metadata: `Preview APIs` => Preview, otherwise `Beta APIs` => Beta, otherwise stable residual. Every official operation has exactly one tag, no operation has both lifecycle tags, and summary/description labels have zero conflicts. The contract does not affirmatively label GA; “stable GA additions” below means the required non-Beta/non-Preview residual. It includes legacy operations.

### Every stable residual addition (74 declarations; 72 wire operations)

| Method | Path | Operation ID | Summary |
|---|---|---|---|
| `GET` | `/attributes` | `getAttributesByEntityType` | List all attributes by entity type |
| `POST` | `/attributes` | `createAttribute` | Create an attribute |
| `DELETE` | `/attributes/{id}` | `deleteAttribute` | Deleting an attribute |
| `GET` | `/attributes/{id}` | `getAttribute` | Retrieve an attribute |
| `PATCH` | `/attributes/{id}` | `updateAttribute` | Update an attribute |
| `GET` | `/coaching/driver-coach-assignments` | `getDriverCoachAssignment` | Get driver coach assignments. |
| `PUT` | `/coaching/driver-coach-assignments` | `putDriverCoachAssignment` | Put driver coach assignments. |
| `GET` | `/coaching/sessions/stream` | `getCoachingSessions` | Get coaching sessions. |
| `GET` | `/defect-types` | `getDefectTypes` | Get DVIR defect types. |
| `GET` | `/defects/{id}` | `getDefect` | Get a single DVIR defect by ID. |
| `PATCH` | `/driver-trailer-assignments` | `updateDriverTrailerAssignment` | Update an existing driver-trailer assignment. |
| `GET` | `/dvirs/{id}` | `getDvir` | Get a single DVIR by ID. |
| `GET` | `/fleet/carb-ctc/vehicles` | `listCarbCtcVehicles` | List CARB CTC enrolled vehicles |
| `GET` | `/fleet/carb-ctc/vehicles/history` | `listCarbCtcVehicleHistory` | List CARB CTC vehicle collection history |
| `GET` | `/fleet/defects/history` | `getDvirDefects` | [legacy] Get all defects |
| `PATCH` | `/fleet/defects/{id}` | `updateDvirDefect` | Update a defect |
| `GET` | `/fleet/document-types` | `getDocumentTypes` | Fetch document types |
| `GET` | `/fleet/documents` | `getDocuments` | Fetch all documents |
| `POST` | `/fleet/documents` | `postDocument` | Create document |
| `POST` | `/fleet/documents/pdfs` | `generateDocumentPdf` | Create a document PDF |
| `GET` | `/fleet/documents/pdfs/{id}` | `getDocumentPdf` | Query a document PDF |
| `DELETE` | `/fleet/documents/{id}` | `deleteDocument` | Delete document |
| `GET` | `/fleet/documents/{id}` | `getDocument` | Fetch document |
| `DELETE` | `/fleet/driver-vehicle-assignments` | `deleteDriverVehicleAssignments` | Delete API generated driver-vehicle assignments |
| `PATCH` | `/fleet/driver-vehicle-assignments` | `updateDriverVehicleAssignment` | Update API generated driver-vehicle assignments |
| `GET` | `/fleet/drivers/vehicle-assignments` | `getDriversVehicleAssignments` | [legacy] Get all vehicles assigned to a set of drivers |
| `GET` | `/fleet/dvirs/history` | `getDvirHistory` | [legacy] Get all DVIRs |
| `PATCH` | `/fleet/dvirs/{id}` | `updateDvir` | Resolve a DVIR |
| `GET` | `/fleet/reports/vehicle/idling` | `getVehicleIdlingReports` | [legacy] Get vehicle idling reports. |
| `GET` | `/fleet/vehicles/driver-assignments` | `getVehiclesDriverAssignments` | [legacy] Get all drivers assigned to a set of vehicles |
| `GET` | `/hub/capacities` | `listHubCapacities` | List capacities for a specific hub |
| `GET` | `/hub/customProperties` | `listHubCustomProperties` | List custom properties for a specific hub |
| `PATCH` | `/hub/location/{id}` | `updateHubLocation` | Update a location |
| `GET` | `/hub/locations` | `listHubLocations` | List locations for a specific hub |
| `POST` | `/hub/locations` | `createHubLocations` | Create locations in bulk |
| `POST` | `/hub/plan` | `createHubPlan` | Create a new plan |
| `POST` | `/hub/plan/orders` | `createPlanOrders` | Create orders in bulk |
| `GET` | `/hub/plan/routes` | `listHubPlanRoutes` | List routes for a specific plan |
| `GET` | `/hub/plans` | `listHubPlans` | List plans for a specific hub |
| `GET` | `/hub/skills` | `listHubSkills` | List skills for a specific hub |
| `PATCH` | `/industrial/assets/{id}/data-outputs` | `patchAssetDataOutputs` | Writes to data outputs on an asset |
| `PATCH` | `/issues` | `patchIssue` | Update a single issue. |
| `DELETE` | `/maintenance/work-orders` | `deleteWorkOrders` | Deletes a work order. |
| `PATCH` | `/maintenance/work-orders` | `patchWorkOrders` | Updates a work order. |
| `GET` | `/me` | `getOrganizationInfo` | Get information about your organization |
| `GET` | `/safety-events` | `getSafetyEventsV2` | Get Safety Events |
| `GET` | `/safety-events/stream` | `getSafetyEventsV2Stream` | Get Safety Events Stream |
| `GET` | `/safety-scores/tag-group` | `getTagGroupSafetyScores` | Get tags combined score |
| `GET` | `/safety-scores/tags` | `getTagSafetyScores` | Get tag scores |
| `PUT` | `/tags/{id}` | `replaceTag` | Update a tag |
| `DELETE` | `/training-assignments` | `deleteTrainingAssignments` | Delete training assignments. |
| `PATCH` | `/training-assignments` | `patchTrainingAssignments` | Update training assignments. |
| `POST` | `/training-assignments` | `postTrainingAssignments` | Create training assignments. |
| `GET` | `/user-roles` | `listUserRoles` | List all user roles |
| `GET` | `/v1/fleet/assets` | `V1getAllAssets` | [Legacy] List all assets |
| `GET` | `/v1/fleet/assets/{assetId}/locations` | `getAssetLocation` | List historical locations for a given asset |
| `GET` | `/v1/fleet/assets/{assetId}/reefer` | `getAssetReefer` | List stats for a given reefer |
| `GET` | `/v1/fleet/assets/{asset_id}/locations` | `V1getAssetLocation` | List historical locations for a given asset |
| `GET` | `/v1/fleet/assets/{asset_id}/reefer` | `V1getAssetReefer` | List stats for a given reefer |
| `DELETE` | `/v1/fleet/dispatch/routes/{route_id_or_external_id}` | `V1deleteDispatchRouteById` | Delete a route |
| `POST` | `/v1/fleet/drivers/{driver_id}/hos/duty_status` | `setCurrentDutyStatus` | Set a duty status for a specific driver |
| `GET` | `/v1/fleet/hos_authentication_logs` | `V1getFleetHosAuthenticationLogs` | Get HOS signin and signout |
| `GET` | `/v1/fleet/locations` | `getFleetLocations` | /fleet/locations |
| `GET` | `/v1/fleet/maintenance/list` | `V1getFleetMaintenanceList` | Get vehicles with engine faults or check lights |
| `GET` | `/v1/fleet/messages` | `V1getMessages` | Get all messages. |
| `GET` | `/v1/fleet/trailers/assignments` | `V1getAllTrailerAssignments` | List trailer assignments for all trailers |
| `GET` | `/v1/fleet/trips` | `V1getFleetTrips` | Get vehicle trips |
| `GET` | `/v1/industrial/vision/cameras` | `V1getCameras` | Fetch industrial cameras |
| `GET` | `/v1/industrial/vision/cameras/{camera_id}/programs` | `V1getVisionProgramsByCamera` | Fetch industrial camera programs |
| `GET` | `/v1/industrial/vision/run/camera/{camera_id}` | `V1getVisionLatestRunCamera` | Fetch the latest run for a camera or program |
| `GET` | `/v1/industrial/vision/runs` | `V1getVisionRuns` | Fetch runs |
| `GET` | `/v1/industrial/vision/runs/{camera_id}` | `getVisionRunsByCamera` | Fetch runs by camera |
| `GET` | `/v1/industrial/vision/runs/{camera_id}/{program_id}/{started_at_ms}` | `V1getVisionRunsByCameraAndProgram` | Fetch runs by camera and program |
| `POST` | `/v1/machines/history` | `V1getMachinesHistory` | Get machine history |

The 106 Beta and five Preview declarations are exhaustive in `official_only_backlog.beta` and `.preview` in the JSON inventory.

## Root `samsara.json` disposition

Leave the tracked root `samsara.json` unchanged as the documented historical compatibility baseline. This creates **no current-contract completion contradiction** because `docs/api-maintenance.md:22` explicitly denies it current-upstream authority, `contracts/samsara/README.md:5-21` identifies the dated snapshot as current, the snapshot validator defaults to `contracts/samsara` (`scripts/validate-samsara-snapshot.php:43`), and `src/`, `config/`, `tests/`, and `scripts/` contain no reference that uses root `samsara.json` as runtime or current-contract authority.

The files are materially different despite the same `info.version`: root has 308 operations/308 unique wires; the dated snapshot has 372 declarations/370 unique wires; 302 wires overlap, 68 are dated-only, and six are root-only. Recommendation: keep every current checker and completion claim pinned to the dated path plus SHA-256 `a7255e2d5b7a94eea97e11f47db1c7b969931f639366bcae49e4922b02ba3fc6`, fail instead of falling back to root, and never use `info.version` as freshness evidence.

## Validation and independent review

The final checker validates the pinned contract digest, current source digest, all operation sets, stage buckets, source evidence, arithmetic, zero inherited phantoms, and zero unclassified differences. The inventory also records the exact normalized schemas for all 5,157 official components and all 552 enum occurrences.

Exact commands and results:

```text
$ php scripts/check-samsara-contract-reconciliation.php --root /Users/erikgall/Code/samsara
PASS: 109 reconciliation assertions; unclassified differences = 0.
Derived: official 372 declarations/370 unique; package 195; matched 187;
package-only 8; official-only 185 declarations/183 unique; changed 6;
stages 241 stable/123 Beta/8 Preview; official-only 74/106/5;
inherited 69, phantoms 0, capability overrides 41/41.

$ vendor/bin/phpunit --do-not-cache-result
OK (1510 tests, 2780 assertions)

$ php scripts/validate-samsara-snapshot.php --snapshot contracts/samsara/samsara-api-2026-08-10.json --metadata contracts/samsara/samsara-api-2026-08-10.json.metadata.json
Snapshot and metadata verified; SHA-256 a7255e2d5b7a94eea97e11f47db1c7b969931f639366bcae49e4922b02ba3fc6; OpenAPI 3.0.1; document 2025-10-23; 260 paths.

$ php tests/Maintenance/ApiSnapshotValidatorTest.php
14 maintenance validator tests passed.

$ php -l scripts/check-samsara-contract-reconciliation.php
No syntax errors detected.

$ jq empty docs/api-sync/contract-inventory.json
exit 0

$ jq -e '<count/arithmetic/unclassified assertions>' docs/api-sync/contract-inventory.json
true

$ git diff --check -- docs/api-sync/contract-inventory.json docs/api-sync/contract-drift.md scripts/check-samsara-contract-reconciliation.php
exit 0; no output

$ shasum -a 256 samsara.json contracts/samsara/samsara-api-2026-08-10.json
6ba5e2befd7e72658db621b3cf91bb958ed985f880c75e94203d1bdbd1217cb9  samsara.json
a7255e2d5b7a94eea97e11f47db1c7b969931f639366bcae49e4922b02ba3fc6  contracts/samsara/samsara-api-2026-08-10.json
```

No package source, package documentation outside `docs/api-sync`, changelog, dependency, generated file, database state, root `samsara.json`, commit, push, or pull request was changed. Stop reason: none.
