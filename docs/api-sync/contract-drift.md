# Samsara API final integrated contract reconciliation

Audit date: 2026-08-10
Authoritative package worktree: `/Users/erikgall/Code/samsara` (`dev`, dirty working tree preserved)
Authoritative official contract: `contracts/samsara/samsara-api-2026-08-10.json`

## Verdict

The current package surface contains **206** unique fixed wire operations. All **206** match the pinned contract by normalized HTTP method and path with no classified operation difference. This is transport-contract proof, not strict generated-model equality: request arrays and `Builder::where()` remain open pass-through inputs, and many response types are open `Fluent` entities or raw arrays.

No package-only operation remains. The legacy compatibility helpers now use the pinned verbs, and the undeclared dispatch-list helper was removed. Capability gates leave **69** supported inherited bindings, all official; **0** inherited phantom operation remains.

## Exact counts

| Measure | Count | Definition |
|---|---:|---|
| Official operation declarations | 372 | Every HTTP operation under the pinned OpenAPI `paths` object. |
| Official unique normalized wire operations | 370 | Placeholder spelling normalized to `{}`; two legacy alias pairs collapse. |
| Package operations | 206 | Unique fixed method/path operations observed from public resource methods after capability gates. |
| Matched operations | 206 | Same normalized method/path; no duplicate official alias is matched. |
| Official-only declarations | 166 | 164 unique normalized wire operations. |
| Package-only operations | 0 | No public helper points at an undeclared normalized wire operation. |
| Changed matched operations | 0 | Every matched operation has its declared wire and SDK pagination surface represented. |
| Matched without confirmed change | 206 | Transport-contract accurate within the proof boundary above. |
| Unclassified differences | **0** | All 166 official-only gaps are additive; root-baseline disposition is docs-only. |

Arithmetic passed: `206 + 166 = 372` official declarations, `206 + 164 = 370` official unique wire operations, and `206 + 0 = 206` package operations.

## Implemented-surface disposition

The audit observed 261 public SDK method request variants and collapsed aliases to 206 wire operations. The runtime probe had zero failures. All 42 concrete resources override `supportedGenericOperations()`; `Resource` checks the gate before HTTP from the generic entry points at `src/Resources/Resource.php:79-225` and the shared item lookup at `:324-339`. The gates permit 69 inherited bindings, and the reconciliation matched all 69 to official operations. Another 137 inherited method/resource combinations throw `UnsupportedOperationException` before HTTP at `src/Resources/Resource.php:443-460`; `EquipmentResource::findByExternalId()` is one additional explicit unsupported alias.

Matched request schemas are supported as transport contracts rather than locally generated validators: generic writes pass caller arrays through, builders allow contract field pass-through, and fixed helpers have exact outbound method/path/query/body tests. Response decoding is either raw JSON, an open entity that retains fields, or an endpoint-specific mapper. `Builder` and direct resources share `RequestExecutor` (`src/Query/Builder.php:572-588`, `src/Resources/Resource.php:308-318`); 400/422, 401, 403, 404, 429, and 5xx map through `src/Resources/Resource.php:355-390`. Cursor pagination and configured page size run through `src/Query/Builder.php:245-311` and `:558-569`.

These facts do **not** prove server runtime behavior, reject invalid request fields locally, or establish generated-model equality for all 5,157 official schemas. The exhaustive operation parameters, bodies, responses, enums, pagination facts, SDK accessors, response mappers, and proof limits are in `docs/api-sync/contract-inventory.json`.

### Resolved legacy operation reconciliation

`assetLocations()`, `assetReefers()`, `fleetAssets()`, `hosAuthenticationLogs()`, `maintenanceList()`, `trips()`, and `visionCameras()` now issue the pinned GET operations. `messages()` now reads with GET instead of calling the separate send-message POST operation. `machines()` remains POST because the pinned `/v1/machines/list` operation is POST. The undeclared `dispatchRoutes()` helper was removed. Exact outbound method/path/query tests cover every retained public legacy method, and the ledger now derives zero package-only operations.

### Paginated direct helpers

The direct raw-envelope APIs for camera media, both fuel/energy reports, the IFTA vehicle report, and the two legacy asset feeds remain intact. Six operation-specific query helpers provide automatic `Builder`/`CursorPaginator` traversal for those same wires. The inventory records both variants without double-counting the underlying operation.

## Official-only backlog by lifecycle

| Lifecycle bucket | Official total | Implemented matches | Official-only declarations | Official-only unique wire operations |
|---|---:|---:|---:|---:|
| Stable residual | 241 | 186 | 55 | 53 |
| Beta | 123 | 17 | 106 | 106 |
| Preview | 8 | 3 | 5 | 5 |

Stage classification uses only contract metadata: `Preview APIs` => Preview, otherwise `Beta APIs` => Beta, otherwise stable residual. Every official operation has exactly one tag, no operation has both lifecycle tags, and summary/description labels have zero conflicts. The contract does not affirmatively label GA; “stable GA additions” below means the required non-Beta/non-Preview residual. It includes legacy operations.

### Every stable residual addition (55 declarations; 53 wire operations)

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
| `GET` | `/fleet/carb-ctc/vehicles` | `listCarbCtcVehicles` | List CARB CTC enrolled vehicles |
| `GET` | `/fleet/carb-ctc/vehicles/history` | `listCarbCtcVehicleHistory` | List CARB CTC vehicle collection history |
| `GET` | `/fleet/defects/history` | `getDvirDefects` | [legacy] Get all defects |
| `GET` | `/fleet/document-types` | `getDocumentTypes` | Fetch document types |
| `GET` | `/fleet/documents` | `getDocuments` | Fetch all documents |
| `POST` | `/fleet/documents` | `postDocument` | Create document |
| `POST` | `/fleet/documents/pdfs` | `generateDocumentPdf` | Create a document PDF |
| `GET` | `/fleet/documents/pdfs/{id}` | `getDocumentPdf` | Query a document PDF |
| `DELETE` | `/fleet/documents/{id}` | `deleteDocument` | Delete document |
| `GET` | `/fleet/documents/{id}` | `getDocument` | Fetch document |
| `GET` | `/fleet/drivers/vehicle-assignments` | `getDriversVehicleAssignments` | [legacy] Get all vehicles assigned to a set of drivers |
| `GET` | `/fleet/dvirs/history` | `getDvirHistory` | [legacy] Get all DVIRs |
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
| `GET` | `/safety-events` | `getSafetyEventsV2` | Get Safety Events |
| `GET` | `/safety-events/stream` | `getSafetyEventsV2Stream` | Get Safety Events Stream |
| `GET` | `/safety-scores/tag-group` | `getTagGroupSafetyScores` | Get tags combined score |
| `GET` | `/safety-scores/tags` | `getTagSafetyScores` | Get tag scores |
| `PUT` | `/tags/{id}` | `replaceTag` | Update a tag |
| `DELETE` | `/training-assignments` | `deleteTrainingAssignments` | Delete training assignments. |
| `PATCH` | `/training-assignments` | `patchTrainingAssignments` | Update training assignments. |
| `POST` | `/training-assignments` | `postTrainingAssignments` | Create training assignments. |
| `GET` | `/user-roles` | `listUserRoles` | List all user roles |
| `GET` | `/v1/fleet/assets/{assetId}/locations` | `getAssetLocation` | List historical locations for a given asset |
| `GET` | `/v1/fleet/assets/{assetId}/reefer` | `getAssetReefer` | List stats for a given reefer |
| `GET` | `/v1/fleet/assets/{asset_id}/locations` | `V1getAssetLocation` | List historical locations for a given asset |
| `GET` | `/v1/fleet/assets/{asset_id}/reefer` | `V1getAssetReefer` | List stats for a given reefer |
| `DELETE` | `/v1/fleet/dispatch/routes/{route_id_or_external_id}` | `V1deleteDispatchRouteById` | Delete a route |
| `POST` | `/v1/fleet/drivers/{driver_id}/hos/duty_status` | `setCurrentDutyStatus` | Set a duty status for a specific driver |
| `GET` | `/v1/fleet/locations` | `getFleetLocations` | /fleet/locations |
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

The final checker validates the pinned contract digest, current source digest, all operation sets, stage buckets, source evidence, arithmetic, zero inherited phantoms, zero unclassified differences, and the source-derived package enum/DTO catalogs. The inventory also records the exact normalized schemas for all 5,157 official components and all 552 official enum occurrences.

Exact commands and results:

```text
$ php scripts/check-samsara-contract-reconciliation.php --root /Users/erikgall/Code/samsara
PASS: 115 reconciliation assertions; unclassified differences = 0.
Derived: official 372 declarations/370 unique; package 206; matched 206;
package-only 0; official-only 166 declarations/164 unique; changed 0;
stages 241 stable/123 Beta/8 Preview; official-only 55/106/5;
inherited 69, phantoms 0, capability overrides 42/42;
package enums 12/186 cases; data classes 75/741 annotated properties.

$ env HTTP_PROXY=http://127.0.0.1:9 HTTPS_PROXY=http://127.0.0.1:9 ALL_PROXY=http://127.0.0.1:9 vendor/bin/phpunit --do-not-cache-result
OK (1706 tests, 5038 assertions)

$ php scripts/validate-samsara-snapshot.php --snapshot contracts/samsara/samsara-api-2026-08-10.json --metadata contracts/samsara/samsara-api-2026-08-10.json.metadata.json
Snapshot and metadata verified; SHA-256 a7255e2d5b7a94eea97e11f47db1c7b969931f639366bcae49e4922b02ba3fc6; OpenAPI 3.0.1; document 2025-10-23; 260 paths.

$ php tests/Maintenance/ApiSnapshotValidatorTest.php
15 maintenance validator tests passed.

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

This tracked reconciliation is current for the uncommitted v2 release tree. The full Round-2 validation and final release gates are recorded in `docs/playgrounds/v2-release-review-round2-devlog.md`; no database state, root `samsara.json`, tag, release, push, or pull request was changed.
