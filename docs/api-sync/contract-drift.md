# Samsara API final integrated contract reconciliation

Reconciled: 2026-10-21 (pinned upstream snapshot: 2026-08-10)
Authoritative package worktree: `/Users/erikgall/Code/samsara` (`dev`, final v2 integration)
Authoritative official contract: `contracts/samsara/samsara-api-2026-08-10.json`

## Verdict

The current package surface contains **206** unique fixed wire operations. All **206** match the pinned contract by normalized HTTP method and path with no classified operation difference. This is transport-contract proof, not strict generated-model equality: request arrays and `Builder::where()` remain open pass-through inputs, and many response types are open `Fluent` entities or raw arrays.

No package-only operation remains. The legacy compatibility helpers now use the pinned verbs, and the undeclared dispatch-list helper was removed. Per-operation traits and explicit overrides expose **53** generic method/resource bindings, all official; **0** phantom generic operations remain. Unsupported CRUD methods are absent.

## Exact counts

| Measure | Count | Definition |
|---|---:|---|
| Official operation declarations | 372 | Every HTTP operation under the pinned OpenAPI `paths` object. |
| Official unique normalized wire operations | 370 | Placeholder spelling normalized to `{}`; two legacy alias pairs collapse. |
| Package operations | 206 | Unique fixed method/path operations observed from public resource methods after unsupported CRUD methods were removed. |
| Matched operations | 206 | Same normalized method/path; no duplicate official alias is matched. |
| Official-only declarations | 166 | 164 unique normalized wire operations. |
| Package-only operations | 0 | No public helper points at an undeclared normalized wire operation. |
| Changed matched operations | 0 | Every matched operation has its declared wire and SDK pagination surface represented. |
| Matched without confirmed change | 206 | Transport-contract accurate within the proof boundary above. |
| Unclassified differences | **0** | All 166 official-only gaps are additive; root-baseline disposition is docs-only. |

Arithmetic passed: `206 + 166 = 372` official declarations, `206 + 164 = 370` official unique wire operations, and `206 + 0 = 206` package operations.

## Implemented-surface disposition

The current inventory records 241 public SDK method request variants, collapsed to 206 wire operations. Across 42 concrete resources, 49 trait bindings and four explicit collection-query overrides produce 53 effective generic bindings. The traits are `CreatesEntities`, `FindsEntities`, `UpdatesEntities`, and `DeletesEntities` (`src/Resources/Concerns/CreatesEntities.php:36`, `FindsEntities.php:33`, `UpdatesEntities.php:38`, `DeletesEntities.php:33`). Assets and Live Shares supply the four explicit update/delete overrides (`src/Resources/Industrial/AssetsResource.php:39,163`, `src/Resources/Integrations/LiveSharingLinksResource.php:37,63`). `Resource::all()` and `supportedGenericOperations()` are removed; unsupported CRUD methods no longer exist. `EquipmentResource::findByExternalId()` remains an explicit local rejection (`src/Resources/Fleet/EquipmentResource.php:47-56`).

Matched request schemas are supported as transport contracts rather than locally generated validators: generic writes pass caller arrays through, builders allow contract field pass-through, and fixed helpers have outbound method/path/query/body tests. Response decoding is either raw JSON, an open entity that retains fields, or an endpoint-specific mapper. `Builder` and direct resources share `RequestExecutor` (`src/Query/Builder.php:603-618`, `src/Resources/Resource.php:185-195`); 400/422, 401, 403, 404, 429, and 5xx map through `src/Resources/Resource.php:233-267`. Supported `find()` methods return `null` on 404 (`src/Resources/Resource.php:203-215`). Cursor traversal and configured page size run through `src/Query/Builder.php:258-301,341-369,589-597`; `first()` applies `limit=1` only when automatic page sizing is enabled (`:218-224`).

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

## Validation and proof limits

The read-only reconciliation checker validates the pinned contract digest, current source digest, operation sets, lifecycle buckets, source evidence, arithmetic, zero generic phantoms, zero unclassified differences, and source-derived enum/DTO catalogs. The inventory records normalized schemas for all 5,157 official components and 552 official enum occurrences.

Read-only verification on 2026-10-21:

```text
$ php scripts/check-samsara-contract-reconciliation.php
Derived official declarations: 372
Derived official unique wire operations: 370
Derived package operations from ledger: 206
Derived matched/package-only/official-only(unique): 206/0/164
Derived changed operations: 0
Derived official stages: stable=241, beta=123, preview=8
Derived official-only stages: stable=55, beta=106, preview=5
Derived generic operations/phantoms: 53/0
Derived concrete resources: 42
Derived src tree: 174 files, SHA-256 ecc80414e40618f2f4ce1c0535948e2e88d64db8b5f251502c5be955299a6d80
Derived package enums/cases: 12/186
Derived package data classes/properties: 75/741
PASS: 402 reconciliation assertions; unclassified differences = 0.
```

Rerun the checker after source changes and refresh the source evidence and upgrade disposition ledger in the owning maintenance step. This documentation pass does not regenerate JSON ledgers, run the full test suite, or establish live API behavior.
