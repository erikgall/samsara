# Development Changelog

This ledger records detailed engineering work, evidence, assumptions, and deferred decisions for the Samsara SDK. Keep consumer-facing release notes in `CHANGELOG.md`. Record only confirmed consumer migrations in `BREAKING_CHANGES.md`.

## Unreleased

### 2026-08-12 — Integrated repository-wide quality and developer-experience pass

#### Scope and accounting

- Audited all 399 tracked package files exactly once across isolated core, DTO, resource, tooling/testing, and documentation lanes. The final coverage ledger records 238 changed and 161 keep decisions, including the four focused tests added during the pass, with zero missing, duplicate, or out-of-scope paths.
- Integrated two bounded cross-lane follow-ups for current fake-response fixtures and nonstandard cursor envelopes. Preserved the pre-existing untracked `docs/.bundle/` directory and made no database, runtime-dependency, or installed SDK configuration behavior change.

#### Developer experience and quality

- Added contract-backed DTO helpers and annotations, complete pinned vehicle-stat/webhook enums, non-mutating UTC conversion, stable `"0"` collection IDs, safer paginator navigation, consistent webhook timestamp failures, custom base URLs, and one-segment generic ID encoding.
- Centralized resource builder creation and request/error handling, corrected query serialization and unsupported implicit limits, and added nested collection/custom-cursor configuration for camera media, fuel/energy reports, IFTA vehicle reports, and legacy asset feeds while preserving their raw-envelope methods.
- Reconciled all 13 shipped fake fixtures with current endpoint/DTO shapes and added recursive required-key, enum, scalar, object, array, and nullability assertions so future drift fails locally.
- Made the maintainer `composer check` alias self-contained with PHPStan's verified 512 MB requirement. CI, local maintenance aliases, formatting ownership, snapshot provenance, and linked-worktree reconciliation now exercise the same contracts.

#### Documentation, compatibility, and release proof

- Updated the changelog, compact breaking record, entry-point upgrade guide, canonical v2 guide, configuration/query references, enum catalog, topical resource pages, contract inventory, and documentation coverage for the integrated result.
- Expanded the machine-checked tag-to-worktree disposition from 257 to 338 paths: 91 upgrade actions, 143 supporting-evidence paths, 35 additive/no-action paths, 68 documentation-only paths, and one internal-only path. Present files carry a content SHA-256, deletions carry an explicit deleted state, and the check runs in the normal maintenance/CI gate, so edits to existing paths cannot bypass review. The frozen 239-path audited implementation baseline remains explicit.
- Added no intentional break beyond the staged-v2 migrations already recorded. `MIG-19` now calls out observable compatibility corrections: non-mutating dates, nullable inconsistent-cursor navigation, encoded reserved-character IDs, and normalized invalid webhook timestamps.

#### Verification

- `composer check` — passed: 1,632 tests / 4,801 assertions, PHPStan with no errors, Pint, 15 maintenance-validator tests, pinned-snapshot verification, and 109 contract-reconciliation assertions.
- `vendor/bin/phpunit --fail-on-warning --fail-on-risky` — passed: 1,632 tests / 4,801 assertions.
- `composer check:v2-upgrade-guide` — passed: all 338 release paths classified once with 91 linked upgrade actions.
- Strict Composer validation, Prettier ownership check, all tracked JSON parsing, 810 local Markdown link/fragment checks, and `git diff --check` — passed.

### 2026-08-12 — Documentation quality and developer-experience lane

#### Scope and decisions

- Verified detached `HEAD`, `dev`, and their merge base at `3b475a16340eb23e341f51dba7135fa38b62364f` before editing; audited the exact 64-file documentation assignment once with zero omissions or duplicates.
- Changed 41 owned files, including this ledger, and kept 23 after review. No source, tests, config, tooling, contract snapshot, dependency file, or lockfile was modified. `docs/.bundle/` was not created or touched.
- Corrected current request bodies for Live Shares, DVIR creation, Work Orders, Settings, and gateway creation; replaced stale response tables for Assets, HOS violations, Idling, Issues, Route Events, Speeding, carrier-proposed assignments, hubs, vehicles, and gateways.
- Reconciled required endpoint filters and no-limit behavior across Trips, Forms, Assignments, Maintenance, Safety Events, Vehicle Stats, Equipment, Assets, Beta helpers, and other resource examples. The shared Builder documentation now states explicitly that fluent methods do not validate endpoint contracts.
- Added maintainer integration gates to the upgrade entry point, canonical guide, and compact breaking record. Later parallel-lane commits must receive a consumer-action or explicit no-action disposition before the existing 257-path proof claim can be carried forward.

#### Cross-lane follow-ups

- Tooling owner: add the already-recorded `tests/Unit/BoostDocumentationTest.php` publication artifact to `$allowedPostAuditPaths` in `scripts/check-v2-upgrade-guide.php`; the clean starting commit otherwise fails the checker before ledger validation.
- Query-builder owner: decide whether `first()` and chunked `lazy($size)` should reject or avoid `limit` on `withoutDefaultPageSize()` operations. Documentation now uses `get()->first()` and `lazy()` for those endpoints.
- Beta-resource owner: declare repeated-key encoding for required qualification-record `ids` in `BetaResource::qualificationsRecords()`. The guide currently shows the explicit `repeatQueryParameters('ids')` workaround.
- Release integrator: classify every incoming quality-lane path for signatures, capabilities, wire contracts, DTO/null semantics, errors, pagination, serialized enum values, tests, and migration documentation; then update the v2 guide/ledger counts and rerun all release gates.

#### Verification

- Local links/fragments — passed: 807 checks across all 58 assigned Markdown files.
- Markdown fences, both documentation JSON artifacts, exact snapshot digest/provenance, strict Composer validation, and `git diff --check` — passed.
- `BoostDocumentationTest` — passed: 3 tests / 65 assertions using the canonical checkout's installed PHPUnit dependencies against this worktree's test and documentation files.
- `composer check:v2-upgrade-guide` — expected baseline failure: `tests/Unit/BoostDocumentationTest.php` is missing only from the checker's post-audit allowlist; the path is already present in the publication-artifact list and ledger.
- Full PHPUnit and Jekyll build — not run in this worktree because `vendor/bin/phpunit` and the `jekyll` executable are absent. Dependencies were not installed or copied; the focused documentation test ran without modifying dependency state, and the release integrator must rerun the full suite and site build in the dependency-equipped integration checkout.

### 2026-08-12 — Laravel Boost agent-documentation overhaul

#### Scope and decisions

- Replaced the stale, always-loaded v1-era Boost guideline set with one 110-word v2 guideline that routes normal SDK work and v1.1-to-v2 migrations to separate Agent Skills.
- Added a current-v2 consumer skill with progressively disclosed integration, query/response, testing, and webhook references, plus a dedicated upgrade skill that requires the canonical migration guide instead of duplicating it.
- Renamed the repository-local development skill to `samsara-sdk-maintenance` so package-maintainer instructions no longer collide with the published consumer skill.
- Confirmed from Laravel 13 documentation and Laravel Boost 2.5.3 source that third-party packages publish guidelines from `resources/boost/guidelines/` and skills from `resources/boost/skills/`; no Composer metadata or runtime Boost dependency is required.
- Expanded the final v2 release ledger from 244 to 257 paths while preserving the frozen 239-path implementation audit. The 18 post-audit publication artifacts now include all changed Boost sources and their contract test.

#### Verification

- Laravel Boost 2.5.3 in a temporary Laravel 13.25 consumer discovered `erikgall/samsara/core`, generated `AGENTS.md`, installed both skills and all three references, and completed `boost:update` successfully.
- The official Agent Skills validator passed the two published skills, the local maintenance skill, and both generated consumer copies.
- `BoostDocumentationTest` passed 3 tests / 65 assertions; it enforces the one-guideline boundary, exact published skill set, frontmatter limits, reference integrity, and local/published skill separation.
- Full PHPUnit, PHPStan, Pint, strict Composer validation, and the updated v2 disposition checker passed.

### 2026-08-12 — Canonical v2 upgrade guide and exhaustive disposition ledger

#### Scope and decisions

- Established `docs/upgrade-guide-v2.md` as the canonical developer and coding-agent migration authority from `v1.1.0` to v2.0.0, with `UPGRADE.md` as the repository entry point.
- Re-audited the exact `v1.1.0..1750395c98c1724c40c82ba82b96b4866dd2106b` release baseline: all 239 changed paths received one disposition—86 upgrade action, 88 supporting evidence, 11 additive/no-action, 1 internal-only, and 53 documentation-only.
- Extended that frozen implementation baseline with five final publication artifacts (`UPGRADE.md`, the canonical guide, its ledger, its checker, and the Composer command registration), producing a 244-path final release scope: 86 upgrade action, 90 supporting evidence, 12 additive/no-action, 1 internal-only, and 55 documentation-only.
- Expanded beyond the compact breaking summary to cover custom `QueryBuilderInterface` and `Resource` extensions, every changed wire family, endpoint-specific DTO unwraps, dynamic payload fields, removed helpers, persisted enum data, exact query/pagination behavior, fakes, and consumer verification.
- Added `scripts/check-v2-upgrade-guide.php` and `composer check:v2-upgrade-guide` to prove baseline equality, exact disposition counts, guide-section linkage, and required migration evidence.
- Refreshed the contract inventory's package-source head, byte count, and manifest digest after the prior DTO-helper wave; the reconciliation checker had correctly exposed that stale evidence even though the source tree itself was clean.
- Preserved the pre-existing untracked `docs/.bundle/` directory without modification.

#### Documentation surface

- Added prominent upgrade links to the repository README, docs home, Getting Started page, compact breaking record, and release changelog.
- Added a copyable agent execution contract, ordered checklist, focused before/after examples, exact old/new endpoint table, completion record, and definition of done.
- Recorded each baseline path in `docs/api-sync/v2-upgrade-disposition.json` with a guide link or explicit no-action rationale.

#### Verification

- `composer check:v2-upgrade-guide` — passed: all 244 final release paths have one disposition, all 86 upgrade-action paths link to validated guide sections, and all 158 evidence/no-action records have an explicit rationale.
- Independent audit agent — completed the 239-file implementation-baseline classification and the 244-path final reconciliation; after iterative fixes to the guide, sibling package pages, checker, and executable fixtures, it found no remaining material contract or documentation defect.
- Cursor Grok 4.5 high read-only Plan review — APPROVED after iterative current-tree reviews. Its final pass verified the production `driverId` versus Preview `id` auth-token distinction and reconfirmed Camera, Forms, Alerts, IFTA, and the final ledger with no remaining Critical or Important finding.
- Final coordinator gates — passed: 1,535 PHPUnit tests / 3,041 assertions; PHPStan 156/156; Pint; strict Composer validation; snapshot digest/provenance; 109 reconciliation assertions with zero unclassified differences; 244/244 upgrade-guide coverage; Jekyll build; and 54 rendered HTML files with 2,153 internal links/fragments resolving.

### 2026-08-11 — Contract-backed DTO helper first wave

#### Scope and decisions

- Promoted the exact-once DTO audit into `docs/api-sync/dto-helper-catalog.md`: 73/73 concrete DTOs, 96 valid proposed signatures across 46 DTOs, and 27 explicit no-helper decisions.
- Used Cursor Grok 4.5 high for prioritization, then independently revalidated every selected field, enum, unit, null behavior, and endpoint hydration path against `contracts/samsara/samsara-api-2026-08-10.json` and `docs/api-sync/contract-inventory.json`.
- Implemented the focused first wave only: 28 additive helpers across 9 DTOs. The other 68 valid proposals remain deferred; rejected Route, Trip, User, Webhook, and equipment-location helpers were not restored.
- Preserved the callable deprecated `FormSubmission::isDraft()` and `isSubmitted()` methods instead of creating an additional compatibility break.

#### Implementation

- Added seven exact current-status predicates to `FormSubmission`; six state predicates and four distance conversions to `RouteStop`; and two completion predicates to `Trip`.
- Added nested-role extraction to `User`, defect presence/classification helpers to `Dvir` and `Defect`, and `SafetyEvent::needsCoaching()`.
- Added address-name and reverse-geocode accessors to `EquipmentGpsLocation`, plus exact external-ID and typed-parent helpers to `Tag`.
- Corrected `Tag::hasParent()` to recognize either `parentTagId` or a non-empty nested `parentTag`.
- Changed `DutyStatus::SLEEPER_BERTH` from the stale `sleeperBerth` backing value to the current `sleeperBed` wire value and proved `HosLog::getDutyStatus()` hydration.

#### Parallel RED/GREEN evidence

- Operational task `019ff43f-d9df-70d2-82fd-a9ac40949fd7`: RED produced 8 focused failures from absent APIs; GREEN passed 34 tests / 232 assertions. Reviewed and integrated as `a4fab9e52244`.
- Safety/maintenance task `019ff43f-d9e1-72e0-bafd-63d1a0d531c9`: three RED stages produced 2, 3, and 2 failures; GREEN passed 49 tests / 95 assertions. Reviewed and integrated as `84a25ccf79e0`.
- Identity/GPS/HOS task `019ff43f-d9e3-7250-8a07-f619fd58eb8b`: RED produced 14 failures covering missing helpers, parent detection, enum value, and HOS mapping; GREEN passed 47 tests / 102 assertions. Reviewed and integrated as `e9b5b0fdba8a`.
- Each task used an isolated worktree based on `05d3f47a948a`, changed only its owned source/test files, passed Pint and `git diff --check`, committed independently, and did not push.

#### Documentation and migration records

- Updated selected resource pages and corrected stale Forms, Routes, Trips, Users, Webhooks, and Live Shares claims exposed by the helper audit.
- Expanded `BREAKING_CHANGES.md` with the exact 17 removed helpers, the `EquipmentStats::getGps()` DTO split, dynamic payload/schema migrations, and the `DutyStatus` serialized-value migration.
- Updated the standalone visual migration guide with the 28-helper map and a compact `sleeperBerth` → `sleeperBed` migration card.

#### Final verification

- Combined focused helper suite — passed, 130 tests and 429 assertions.
- `composer test` — passed, 1,535 tests and 3,028 assertions on PHP 8.4.23.
- `composer analyse` — passed, PHPStan 156/156 with no errors. The first sandboxed attempt could not open PHPStan's loopback worker socket; the permission-correct rerun passed unchanged.
- `vendor/bin/pint --test`, strict Composer validation, exact snapshot validation, and `git diff --check` — passed.
- Jekyll 4.4.1 build — passed; 52 rendered HTML files contained 2,050 checked internal links/fragments with zero failures. The configured remote theme emitted only non-fatal Sass deprecation warnings.
- Visual migration guide browser check — passed with the 28-helper grid, sleeper-berth value shift, responsive cards, and zero console errors.
- Cursor Grok 4.5 high independently reviewed the complete integrated code, tests, contract evidence, package docs, changelogs, catalog, and visual guide in read-only Plan mode. It approved with no Critical or Important finding, identified two optional documentation-clarity issues, and approved both fixes plus the final wording correction on re-review with no remaining material finding.

### 2026-08-10 — Final contract reconciliation and release gate

#### Status

- Completed the staged-v2 reconciliation against the exact first-party snapshot retrieved on 2026-08-10. The owner directed the snapshot and implementation to be committed and pushed on a new `dev` branch from `main`, resolving the earlier publication hold recorded in the historical entries below.
- Recomputed the complete operation ledger from the integrated source instead of carrying the pre-implementation audit counts forward.

#### Final inventory

- Official contract: 372 declarations, 370 unique wire operations, 241 stable, 123 beta, and 8 preview.
- Package ledger: 195 operations; 187 match official wires and 8 are explicit legacy helpers absent from the pinned contract.
- Official-only backlog: 185 declarations / 183 unique wires — 74 stable, 106 beta, and 5 preview.
- Changed matched operations: 6, all classified as additive pagination-ergonomics gaps. No matched operation retains a classified wire, request-body, response-loss, or error-mapping break.
- Inherited public surface: 69 effective operations, 133 unsupported calls blocked locally, zero inherited phantom operations, and exact capability declarations on all 41 concrete resources.
- Unclassified differences: zero.

#### Final verification

- `composer test` — passed, 1,510 tests and 2,780 assertions on PHP 8.4.23.
- `composer analyse` — passed, PHPStan 156/156 with no errors.
- `vendor/bin/pint --test`, strict Composer validation, and `git diff --check` — passed.
- Exact snapshot and metadata validation plus all 14 standalone maintenance cases — passed.
- Reconciliation checker — passed 109 assertions with zero unclassified differences.
- Jekyll 4.4.1 build — passed; 50 rendered HTML files contained 1,968 verified internal links/fragments and zero broken targets. The configured remote theme emitted only non-fatal Dart Sass deprecation warnings.

### 2026-08-10 — Final documentation and migration reconciliation

#### Status

- Bootstrapped the integrated `dev` documentation into the documentation worktree and reconciled it against the complete staged-v2 source, tests, and domain handoffs.
- Limited authored changes to `README.md`, `BREAKING_CHANGES.md`, `DEVELOPMENT_CHANGELOG.md`, and Markdown under `docs/`; the audit-owned contract drift report/inventory, source, tests, contracts, dependencies, generated site, and database were not changed.

#### Completed

- Documented exact generic capabilities for all 41 resource accessors and current helper paths, signatures, return types, query encoding, cursor-limit behavior, endpoint-specific DTOs, typed errors, Canada routing, and facade faking.
- Added a complete documentation/resource coverage matrix and kept official-only operations, current enum differences, and modern Safety Events/DVIR additions explicitly deferred.
- Reworked `BREAKING_CHANGES.md` as a consumer migration guide and kept this ledger focused on engineering sequence and evidence.
- Replaced source-relative `.md` links with rendered `.html` targets throughout the Jekyll pages.
- Addressed the independent review's four concrete stale statements: HOS wrappers/capabilities, service-task runtime type, the dangling Work Order status claim, and the legacy trailer-assignment return type.

#### Validation

- Authoritative `dev` suite: `composer test` — passed, 1,510 tests and 2,780 assertions on PHP 8.4.23.
- Jekyll 4.4.1: `bundle exec jekyll build --destination /private/tmp/samsara-docs-c094-site` — passed. Output remained outside the repository; only upstream/theme Dart Sass deprecation warnings were emitted.
- Rendered link and fragment scan — 1,968 links checked, zero broken.
- Markdown example scan — 52 files, 374 fenced blocks, 341 PHP blocks, zero unclosed fences, and zero stale live-call matches.
- `git diff --check` and exclusive tracked/untracked scope checks — passed.
- Independent source-accuracy review — four findings, all resolved once; no second review loop opened.

### 2026-08-10 — Safety and operations contract slice

#### Status

- Integrated into the `dev` working tree on the staged-v2 core.
- Reconciled package-owned Alerts, Camera Media, Forms, Fuel/Energy, HOS, IFTA, Maintenance, Safety Events, assignments, Settings, Tachograph, and Work Orders behavior. Official-only additions remain deferred.

#### Completed

- Routed owned direct HTTP helpers through the shared typed request executor and declared exact generic capability ownership for every resource in the slice.
- Corrected alert incident/configuration collection semantics, Camera Media paths and envelope preservation, form PDF query IDs, fuel/IFTA parameter names, DVIR creation, settings/tachograph paths, trailer-assignment signature, and work-order/service-task query behavior.
- Added endpoint-specific mapping for `AlertIncident`, `FormTemplate`, `ServiceTask`, `Defect`, `SafetyEventActivity`, and the four HOS response wrappers.
- Declared repeated-key encoding for alert IDs, Camera Media arrays, DVIR safety status, service-task/work-order IDs, and work-order-stream status/asset/assignee arrays.
- Preserved the implemented legacy Safety Events operations while classifying the modern `/safety-events` family as deferred official-only replacement work.

#### Consumer contract notes

- `deleteConfigurations(array)` became one `deleteConfigurations(string)` request per configuration.
- `CameraMediaResource::get(array): array` now preserves the full response envelope.
- `FormsResource::exportPdf(array)` became `exportPdf(string $submissionId)`; `templates()` now returns `Builder<FormTemplate>`.
- `TrailerAssignmentsResource::forTrailer(string)` became `forTrailer(int, array = []): Trailer`.
- HOS builders now return endpoint wrapper DTOs; service tasks and safety activity use specialized DTOs.
- Alert configuration and incident booleans are `isEnabled` and `isResolved`; fuel/energy reports use `startDate`/`endDate` while efficiency streams use `startTime`/`endTime`; IFTA detail export uses `startHour`/`endHour`.

### 2026-08-10 — Fleet and platform contract slice

#### Status

- Integrated into the `dev` working tree on the staged-v2 core.
- Reconciled 70 stable package-owned operations across Fleet, Telematics, Dispatch, Organization, and Integrations resources.

#### Completed

- Declared exact generic capabilities across the owned resources and rejected unsupported inherited calls before HTTP.
- Marked 15 cursor operations without an official `limit` to suppress only the implicit configured page size.
- Declared `GET /fleet/vehicles` `attributes` as the sole owned Fleet/platform repeated-key array.
- Corrected driver authentication, QR-code, and remote-sign-out paths, payload placement, signatures, and typed returns.
- Corrected external-ID item lookup to `{key}:{value}` for drivers, trailers, and vehicles; rejected unsupported equipment external lookup.
- Replaced resource-wide location/stat mapping with distinct snapshot and series DTOs for equipment and vehicles; mapped route audit logs to `RouteAuditLog`.
- Added current trailer/vehicle fields including `trailerSerialNumber`, `grossVehicleWeight`, `vehicleWeight`, `vehicleWeightInKilograms`, and `vehicleWeightInPounds`.

#### Consumer contract notes

- Routes collection reads moved from `all()` to `query()->between(...)`; Trips reads require the stream builder with `startTime` and comma-separated `ids` when filtered.
- Vehicle stats reads moved from generic `all()` to `current()`, `feed()`, or `history()` builders.
- Live-share `find()` is unsupported; update/delete use `/live-shares?id={id}`.
- Driver helper returns are now `DriverAuthToken`, `DriverQrCode`, `EntityCollection<DriverQrCode>`, and `DriverRemoteSignOutResult` as appropriate.

### 2026-08-10 — Industrial, unstable, and legacy contract slice

#### Status

- Integrated into the `dev` working tree and regression-tested with the finalized staged-v2 core.
- Reconciled all 22 owned matched operations across Industrial, Assets, Sensors, Beta, Preview, and Legacy resources. The 112 official-only Beta/Preview operations remain an explicit additive backlog rather than being added without a product decision.

#### Completed

- Routed all 36 owned domain HTTP calls through the shared `executeSdkRequest()` error boundary and declared exact inherited CRUD capabilities for all six owned resources.
- Corrected 19 verified request paths, verbs, signatures, and body shapes, including asset collection mutation routes, industrial data-point endpoints, industrial job query IDs and nested bodies, qualification/training/HOS routes, and preview vehicle locks.
- Added endpoint-specific response mapping for asset locations, industrial assets, data inputs and nested data points, driver safety scores, and trailer stats.
- Changed former one-page helpers for devices, qualification types, reading definitions, report configurations, and training courses to return `Builder`; consumers now choose `get()`, `paginate()`, or `lazy()` explicitly.
- Added or refreshed 12 focused DTO and resource test files covering the owned contract surface.

#### Validation

- The workstream's focused suite passed: 144 tests and 264 assertions.
- The combined integration suite passed: 1,373 tests and 2,158 assertions.
- PHPStan passed across 137 files; Pint and `git diff --check` passed.
- Independent workstream review found no authored Critical, Important, or Minor issue.

#### Compatibility notes

- `Asset` now exposes `type` and `serialNumber` in place of `assetType` and `serial`; asset update/delete use `/assets?id={id}`, and inherited asset `find()` is unsupported.
- `SensorsResource::list()` no longer accepts a request-data argument.
- AEMP fleet retrieval accepts a page-number string and returns the raw Fleet response wrapper instead of a `Builder`.
- Industrial job create/update payloads use the contract's nested `job` shape; job update/delete identify records with a query `id`, and delete returns the response array rather than a boolean.
- Industrial reading creation requires a `data` envelope and returns a boolean.
- Driver token creation uses the payload-only identification contract. Preview vehicle lock/unlock operations use `PUT`/`DELETE /preview/fleet/vehicles/{id}/lock` and return booleans.

### 2026-08-10 — First staged-v2 defect and documentation slice

#### Status

- Integrated into the working tree and regression-tested.
- This slice established the shared core later consumed by the integrated Fleet/platform, Safety/operations, and Industrial/unstable slices above.

#### Completed

- Corrected `Samsara::fake()` so it replaces both the deferred container singleton and the facade's resolved instance.
- Applied the configured `per_page` value to `getWithPagination()`, `paginate()`, and `lazy()` when callers do not provide an explicit limit. Explicit `paginate($perPage)`, `lazy($chunkSize)`, `limit()`, and `where('limit', ...)` values retain precedence.
- Added a shared request executor for inherited Resource CRUD and Builder GET paths. Both returned failures and exhausted-retry Laravel `RequestException` responses now route through the SDK exception taxonomy with the effective endpoint in context.
- Preserved `find()` returning `null` for 404 responses across returned-response and exhausted-retry paths.
- Mapped documented HTTP 400 and compatibility HTTP 422 validation responses to `ValidationException` while preserving the real status, body, endpoint, and field details.
- Added Builder `usingEntity()` and `mapUsing()` hooks so domain endpoints can select an endpoint-specific DTO or callback across eager, paginated, and lazy results.
- Serialized dedicated OpenAPI form/`explode=false` list helpers as comma-separated values without changing arbitrary arrays passed through `where()`.
- Added explicit Canada routing through `SAMSARA_REGION=ca`, `useCanadaEndpoint()`, and `https://api.ca.samsara.com`.
- Added the protected final `executeSdkRequest()` bridge for domain-specific requests, keeping `RequestExecutor` as the sole HTTP error boundary.
- Added `supportedGenericOperations()` and a preflight gate for inherited `all`, `find`, `create`, `update`, and `delete` calls. Integrated resources now declare exact current capabilities.
- Added Builder `withoutDefaultPageSize()` for cursor endpoints that accept `after` but do not declare `limit`. It suppresses only the implicit configured page size; caller-supplied limits retain precedence across paginator and lazy traversal.
- Added Builder `repeatQueryParameters()` for OpenAPI form/`explode=true` arrays. Declared lists now use repeated RFC 3986 query keys on the wire while non-declared query values retain their existing standard encoding.
- Extracted that encoding into the shared `Http\QueryString` utility and added Resource `executeSdkGet()`, so direct GET methods with specialized response envelopes use the same query contract without being forced through Builder result mapping.
- Added focused facade, query-builder, resource, exception, client, and service-provider regressions for those behaviors.
- Refreshed `README.md` and 38 existing documentation pages, and added `docs/api-sync/documentation-coverage.md` to distinguish verified current behavior from staged-v2 follow-up work.
- Integrated the complete operation ledger and drift report under `docs/api-sync/`.

#### Validation

- Core capability and pagination follow-up passed its combined focused suite: 22 tests and 57 assertions.
- The combined integration suite passed: 1,349 tests and 2,070 assertions.
- PHPStan passed across 137 files; Pint, Composer metadata validation, PHP syntax checks, and `git diff --check` passed in their recorded scopes.
- Refreshed independent core-foundation review returned `APPROVE` with no findings across the capability gate, protected executor, and pagination opt-out.
- The repeated-key serializer followed a RED/GREEN cycle and passed the complete Builder suite: 59 tests and 130 assertions. The first RED proved the API was absent; the second reproduced Laravel replacing a URL query when an empty query array was supplied.
- The shared serializer and direct-GET bridge followed a second RED/GREEN cycle. The combined Builder, Resource, and query-string suites pass 87 tests/208 assertions; PHPStan passes across 138 files, with Pint and diff checks clean.
- The documentation site baseline built successfully with Homebrew Ruby/Bundler; a final build remains required after all staged-v2 documentation changes land.

#### Compatibility notes

- Builder-backed failures that previously leaked Laravel `RequestException` now surface the package's typed exceptions. Consumers that caught the Laravel exception on Builder calls must migrate to the SDK exception taxonomy.
- Inherited Resource CRUD and integrated domain-specific helpers now use the same typed post-retry boundary.
- HTTP 400 validation failures now surface `ValidationException` with code 400; the existing public constructor still defaults to 422 when no status is supplied.
- Dedicated list helpers now emit comma-separated values, changing outbound query strings that previously used indexed PHP-array encoding.
- Domain builders now explicitly declare explode-true array parameters. Their raw URLs change from bracketed keys such as `status[0]=a&status[1]=b` to repeated keys such as `status=a&status=b`.
- Pagination requests may now include the configured default `limit` where the setting was previously ignored. Explicit limit choices remain authoritative.
- Domain methods for cursor endpoints without a `limit` parameter opt out through `withoutDefaultPageSize()` from operation evidence.
- Integrated resources override the compatibility default with exact capability ownership.
- The three integrated domain slices cover owned path corrections, endpoint-specific DTO wiring, pagination semantics, and domain regressions.

### 2026-08-10 — OpenAPI maintenance guardrails

#### Scope

- Make future Samsara OpenAPI refreshes and package drift reviews repeatable.
- Add a dependency-free check for dated snapshots and machine-readable provenance.
- Define separate development and consumer-breaking-change records.
- Preserve package source, existing resource docs, dependency manifests, contract bytes, and database state.

#### Completed

- Added `scripts/validate-samsara-snapshot.php` using the PHP 8.2 standard library only.
- Added `tests/Maintenance/ApiSnapshotValidatorTest.php`, a standalone behavior test that needs no Composer installation.
- Added `docs/api-maintenance.md` with the authority model, snapshot and metadata contract, refresh sequence, digest checks, drift review, documentation duties, and release gates.
- Added `BREAKING_CHANGES.md`; its initial provisional no-new-break state was later superseded by the completed contract audit while retaining migration guidance for the verified 1.1.0 platform-support removals.
- Made snapshot discovery independent of a hard-coded date. The validator selects the uniquely newest JSON candidate with one valid `YYYY-MM-DD` filename date, then validates it; explicit snapshot and metadata paths remain available.
- Made ambiguity a stop condition. Two snapshot candidates with the newest date require `--snapshot`; the tool never guesses.
- Bound repository-relative metadata paths to the selected snapshot, rejected symlinked evidence and unsafe predecessor paths, treated metacharacters in directory names literally, and rejected duplicate or conflicting CLI options.
- Limited automated contract checks to facts available in the files: JSON shape, minimal OpenAPI structure, provenance field shape, metadata-to-snapshot consistency, byte size, and SHA-256 over exact raw bytes.

#### Validation

- `php tests/Maintenance/ApiSnapshotValidatorTest.php` — passed; 14 tests covered newest dated-file discovery, literal metacharacter paths, read-only byte stability, missing snapshots, digest mismatch, same-date ambiguity, incomplete provenance and validator metadata, invalid calendar dates and timezone offsets, incomplete OpenAPI structure, invalid OpenAPI version markers, metadata path binding, symlink rejection, and invalid CLI combinations.
- TDD RED was observed before the validator existed: the standalone test failed because `scripts/validate-samsara-snapshot.php` was absent.
- A second RED was observed after the acquisition result arrived: its real sidecar failed the provisional `schema_version` requirement. The validator and fixtures were then aligned to the producing workstream's actual metadata without altering the acquired files.
- `php scripts/validate-samsara-snapshot.php --snapshot /Users/erikgall/.codex/worktrees/e3ab/samsara/contracts/samsara/samsara-api-2026-08-10.json --metadata /Users/erikgall/.codex/worktrees/e3ab/samsara/contracts/samsara/samsara-api-2026-08-10.json.metadata.json` — passed against the acquisition worktree; SHA-256 `a7255e2d5b7a94eea97e11f47db1c7b969931f639366bcae49e4922b02ba3fc6`, OpenAPI `3.0.1`, document version `2025-10-23`, 260 paths.
- `php -l scripts/validate-samsara-snapshot.php` and `php -l tests/Maintenance/ApiSnapshotValidatorTest.php` — passed with no syntax errors.
- `composer validate --strict --no-check-publish --no-interaction` — passed; `composer.json` is valid.
- Extracted `bash` blocks from `docs/api-maintenance.md` piped to `bash -n` — passed syntax validation.
- `git diff --check` — passed; a separate trailing-whitespace scan of all five new files returned no matches.
- Independent final review — `Ready`; no Critical, Important, or Minor findings remained, and before/after hashes confirmed that real snapshot and metadata bytes were unchanged.
- Dependency-backed repository checks remain listed under Deferred Work because `vendor/`, `node_modules/`, and the documentation bundle are absent from this worktree.

#### Baseline evidence

- Worktree baseline: detached `HEAD` at `7e6f50882d8979e5921eacade489e9f9e3b0d0fc`.
- The checkout initially had no `contracts/`, provenance sidecar, checksum manifest, or maintenance script.
- The tracked root `samsara.json` parses as OpenAPI `3.0.1`, reports `info.version` `2025-10-23`, and has SHA-256 `6ba5e2befd7e72658db621b3cf91bb958ed985f880c75e94203d1bdbd1217cb9`.
- Repository history does not prove the root file's source URL or retrieval timestamp. It is a compatibility baseline, not current-upstream provenance.
- The separate acquisition workstream proved `https://developers.samsara.com/openapi/samsara-api.json` through Samsara's OpenAPI guide and API catalog, stored 4,206,874 raw bytes, and produced the digest above. Those files remain in its isolated worktree pending integration and rights review.

#### Assumptions and limits

- The acquisition workstream's `samsara-api-2026-08-10.json.metadata.json` shape is the current machine-checkable handoff contract. Null upstream identifiers are allowed only when the source did not provide them.
- A matching digest proves that bytes did not change after capture. It does not prove the source was official, the OpenAPI document is semantically complete, or the live service behaves as described.
- `info.version` is document metadata, not a retrieval date.
- The completed contract audit confirms breaking contract drift and reduces unclassified differences to zero. That audit result does not prove any staged-v2 implementation is complete or runtime-verified.
- The acquired contract has no embedded open-source license. Confirm authorization before committing or publishing its bytes; the SDK's MIT license does not cover them.

#### Deferred work

- Integrate the acquired snapshot, README, and sidecar only after the coordinator resolves the recorded publication-rights caveat.
- Implement the owner-approved staged-v2 work in reviewable slices, with operation-level regression coverage and consumer migration detail tied to code as it lands.
- Run PHPUnit, PHPStan, Pint, the full CI compatibility matrix, and the Jekyll build after dependencies are available.
- Run the networked direct-dependency drift review separately; do not update lock files as part of the review.

### 2026-08-10 — Historical contract-drift audit baseline (superseded by the integrated slices above)

#### Decision and status

- **Audit status:** Complete. The source audit reached zero unclassified differences.
- **Owner decision:** Proceed with the confirmed migration as a staged v2 program.
- **Implementation status at audit acceptance:** Not complete. The integrated entries above supersede that historical state while retaining this baseline evidence.
- **Snapshot publication status:** Pending. No decision to commit or publish Samsara's contract bytes is made here, and the SDK's MIT license is not assumed to cover them.

#### Authoritative audit artifacts

- `/Users/erikgall/Code/samsara/docs/api-sync/contract-drift.md`
- `/Users/erikgall/Code/samsara/docs/api-sync/contract-inventory.json`

The Markdown report is the human-readable finding record. The JSON inventory is the operation-level ledger for implementation sequencing and classification evidence.

#### Verified counts

- **175 package-only operations:** callable package method/path pairs absent from the current live OpenAPI; package-only presence gaps are classified as breaking.
- **236 official-only operations:** current OpenAPI declarations with no package match; these are additive package opportunities, not proof that every operation should ship in v2.
- **72 changed matched operations:** matched method/path pairs with at least one confirmed request, response, pagination, or error-contract difference.
- **65 changed operations with a breaking difference:** a subset of the 72 changed matched operations; three changed operations contain both breaking and additive differences.
- **0 unclassified differences:** every observed delta is classified as breaking, additive, or docs-only.

Context from the same ledger: 372 official operation declarations, 311 package operations, 136 matched operations, and 64 matched operations without a classified operation-level difference. “Without a classified difference” is not runtime-equivalence proof.

#### Audit-confirmed staged-v2 migration categories

1. Replace capability-by-inheritance with explicit endpoint capabilities so inherited generic CRUD cannot expose phantom operations.
2. Correct obsolete HTTP methods and paths using the operation-level official mapping.
3. Route builder and resource failures through one typed SDK error-mapping contract.
4. Select endpoint-specific DTOs and response mappers instead of one resource-wide entity type.
5. Serialize query arrays according to each OpenAPI parameter's style and `explode` contract.
6. Define pagination ownership, cursor traversal, one-page versus all-page behavior, and configured defaults explicitly.
7. Map the documented HTTP 400 validation envelope intentionally, preserving 422 only if later runtime evidence supports a compatibility fallback.
8. Add Canada region/base-URL support as an additive capability.

These were audit-confirmed directions at acceptance. The integrated entries above record implemented signatures and migration behavior; official-only additions remain separate product decisions.

#### Audit evidence boundary

- The audit inspected 372 official declarations, 311 package operations, 41 resources, all 149 files under `src/`, 47 existing Markdown docs plus `README.md`, and 124 PHP test files.
- The source report explicitly treats package arrays and open `Fluent` DTOs as opaque where source cannot prove runtime semantics.
- Package-only means absent from the current live OpenAPI/reference sources, not proven rejected by every Samsara server; legacy APIs may remain outside the live contract.
- The first-party sources disagree on some 5xx response shapes. That uncertainty remains open until endpoint runtime evidence resolves it.

#### Validation evidence

- `contract-inventory.json` parses successfully and reports `changed_operations=72` and `unclassified_differences=0`.
- Independent recomputation over the operation ledger found 72 changed operations, 65 changed operations containing `breaking`, and zero operations marked unclassified.
- Inventory arithmetic is consistent: `372 = 136 + 236`, `311 = 136 + 175`, and `136 = 72 + 64`.
- `php tests/Maintenance/ApiSnapshotValidatorTest.php` — passed; 14 maintenance-validator tests.
- `git diff --check` and the scoped tracked-file diff across `src`, `tests`, `README.md`, `docs`, dependency manifests, and `samsara.json` — passed with no tracked changes. A separate trailing-whitespace scan of the two edited ledgers returned no matches.
- No source, test, resource-documentation, dependency, contract, or PostgreSQL change is claimed by this follow-up.

#### Historical remaining-work list

- The integrated slices above completed capability ownership, owned operation reconciliation, endpoint DTO mapping, query/pagination/error regressions, and consumer migration documentation.
- Official-only additions, publication rights, and release selection remain deferred decisions rather than implied package work.
- Resolve the snapshot publication-rights choice separately before committing or publishing upstream contract bytes.
