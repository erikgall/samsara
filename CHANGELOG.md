# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- **Upgrade documentation**: Added a canonical v1.1.0-to-v2.0.0 guide with executable migrations, exact before/after wire contracts, an agent completion record, and a machine-checked disposition for all 337 final release paths.
- **Laravel Boost guidance**: Added package-discovered Agent Skills for current v2 application development and v1.1-to-v2 migration work, with focused references for integration, querying, responses, testing, and webhooks.
- **Form submissions**: Added seven exact current-status predicates: `isNotStarted()`, `isCompleted()`, `isArchived()`, `isInProgress()`, `needsReview()`, `hasChangesRequested()`, and `isApproved()`.
- **Route stops**: Added six exact state predicates plus actual/planned distance conversion helpers for kilometers and miles.
- **Trips**: Added `isCompleted()` and `isInProgress()` for the current completion-status contract.
- **Users**: Added `getRoleIds()` for nested role assignments.
- **Maintenance and safety**: Added `Dvir::hasDefects()`, `Defect::isSafe()`, `Defect::isUnsafe()`, and `SafetyEvent::needsCoaching()`.
- **Equipment stats GPS**: Added `EquipmentGpsLocation::getAddressName()` and `getReverseGeoLocation()`.
- **Tags**: Added `getExternalId()` and typed `getParent()` helpers.
- **Documentation**: Added a contract-evidenced catalog covering all 73 concrete DTOs, 96 valid proposals, and 27 explicit no-helper decisions.
- **Pagination flexibility**: Added nested response collection selectors and custom cursor parameter names for extension authors, plus cursor builders for Camera Media, fuel/energy reports, IFTA vehicle reports, and legacy asset locations/reefers without changing the raw-envelope methods.
- **Enums**: Added every pinned `VehicleStatType` and `WebhookEvent` value while retaining legacy EV spellings for compatibility.
- **Client configuration**: Added `useBaseUrl()` for compatible proxies, gateways, and test servers.

### Changed

- **API contract alignment**: Reconciled capability gates, wrapped paths and methods, query encoding, pagination defaults, typed errors, and endpoint-specific DTO mapping against the pinned 2026-08-10 Samsara contract. See `BREAKING_CHANGES.md` and `UPGRADE.md` before adopting v2.
- **Documentation accuracy**: Corrected endpoint-specific required filters, no-limit pagination examples, current create payloads, and response-field tables across stable and beta resource guides.
- **Laravel Boost guidelines**: Replaced the large, v1-era always-loaded guideline set with one concise v2 guideline that routes detailed work into on-demand skills; added a current package-maintenance skill and publication-contract tests.
- **Tags**: `hasParent()` now recognizes either `parentTagId` or a documented nested `parentTag` payload.
- **Hours of Service**: `DutyStatus::SLEEPER_BERTH` now uses the upstream `sleeperBed` backing value, allowing `HosLog::getDutyStatus()` to hydrate current sleeper-berth rows. This changes the enum's serialized value; see `BREAKING_CHANGES.md`.
- **Core quality**: UTC formatting no longer mutates caller-owned dates; collection IDs retain `"0"`; paginator navigation rejects inconsistent cursor metadata; generic item IDs remain one encoded path segment; malformed webhook timestamps use `InvalidSignatureException`.
- **Testing fixtures**: Reconciled all 12 shipped fake-response fixtures with their current DTO and endpoint contracts and added executable shape assertions.

### Deprecated

- **Form submissions**: `isDraft()` and `isSubmitted()` remain callable but are deprecated because the current response status enum contains neither `draft` nor `submitted`.

### Removed

- **Unsupported generic operations**: Capability-gated inherited CRUD methods that have no represented upstream operation now fail locally with `UnsupportedOperationException`; use the exact matrix and migrations in the v2 guide.
- **DTO helpers**: Removed 17 helpers whose source fields are absent, renamed, semantically different, or owned by another endpoint. The upgrade guide lists every replacement or explicit no-equivalent decision.

## [1.1.0] - 2026-05-07

### Added

- Support for Laravel 13.x.
- GitHub Actions test matrix covering PHP 8.2/8.3/8.4 against Laravel 12 and 13, including a `--prefer-lowest` cell that locks lower-bound compatibility.

### Changed

- **Entity**: Defined a local `Entity::make()` factory so the SDK no longer depends on `Illuminate\Support\Fluent::make()`, which was only added in Laravel 12.9.

### Removed

- Support for Laravel 10 and 11. The package now supports Laravel 12 and 13.
- Support for PHP 8.1. The minimum required PHP version is now 8.2.

## [1.0.4] - 2026-02-26

### Fixed

- **Builder**: Fixed endpoint reset bug in `createBuilderWithEndpoint()` where the endpoint was temporarily set on the resource then immediately reset, causing the Builder to use the wrong (base) URL for requests. The fix stores an optional endpoint override on the Builder itself and centralizes `createBuilderWithEndpoint()` in the base Resource class, removing 16 duplicated copies from subclasses.

## [1.0.3] - 2026-02-03

### Added

- **Webhooks**: Added webhook signature verification support with `WebhookSignature` class for validating incoming Samsara webhook payloads

### Fixed

- **VehicleStatsResource**: Fixed query `types` parameter to be formatted as a comma-separated string instead of an array, matching the Samsara API's expected format

## [1.0.2] - 2026-01-20

### Changed

- **WebhookEvent**: Reduced to 27 valid event types per Samsara API documentation (removed unsupported events like `AlertIncident`, `HosLogsUpdated`, `RouteCreated`, etc.)

## [1.0.1] - 2026-01-20

### Added

- `UnsupportedOperationException` for operations not supported by the Samsara API
- **InteractsWithTime**: Added `toUnixMilliseconds()` method for legacy v1 API requests that require Unix timestamps in milliseconds
- **InteractsWithTime**: Added `fromUnixMilliseconds()` method for parsing timestamps from legacy v1 API responses
- **InteractsWithTime**: Added `toUtcString()` method for explicit UTC conversion with Z suffix

### Changed

- **VehiclesResource**: `create()` now throws `UnsupportedOperationException` with guidance to use Assets API (`POST /assets` with `type: "vehicle"`) since vehicles cannot be created via `/fleet/vehicles`
- **VehiclesResource**: `delete()` now throws `UnsupportedOperationException` explaining vehicles cannot be deleted (use `update()` to mark as retired instead)
- **InteractsWithTime**: `formatTime()` now converts timestamps to UTC with Z suffix (e.g., `2024-01-15T14:30:00Z`) to match Samsara API documentation format

### Fixed

- **DriversResourceTest**: Added required `password` field to driver creation test payload per API requirements
- **RoutesResourceTest**: Added required `stops` array with minimum 2 stops including `scheduledDepartureTime` and `scheduledArrivalTime` per API requirements
- **AddressesResourceTest**: Added `geofence` field with circle configuration per API requirements
- **Timestamp handling**: Timestamps from non-UTC timezones are now properly converted to UTC before formatting for API requests

## [1.0.0] - 2025-01-17

### Added

**Core Infrastructure:**
- `Samsara` client class with fluent API
- `SamsaraServiceProvider` with Laravel integration
- `Samsara` Facade for easy access
- EU region support with `useEuEndpoint()`

**40+ Resource Endpoints:**
- Fleet: Drivers, Vehicles, Trailers, Equipment
- Telematics: VehicleStats, VehicleLocations, Trips
- Safety: HoursOfService, Maintenance, SafetyEvents
- Dispatch: Routes, Addresses
- Organization: Users, Tags, Contacts
- Industrial: IndustrialAssets, Sensors, Assets
- Integrations: Webhooks, Gateways, LiveSharingLinks
- Additional: Alerts, Forms, IFTA, Idling, Issues, FuelAndEnergy, Tachograph, Speeding, and more
- Beta/Preview/Legacy endpoints

**Query Builder:**
- Fluent query interface with method chaining
- Filtering: `whereTag()`, `whereDriver()`, `whereVehicle()`, etc.
- Time ranges: `between()`, `startTime()`, `endTime()`, `updatedAfter()`
- Types: `types()`, `withDecorations()`, `expand()`
- Pagination: `limit()`, `after()`, `paginate()`
- Lazy loading: `lazy()` for memory-efficient streaming

**Entity Classes:**
- 50+ strongly-typed entity classes extending `Illuminate\Support\Fluent`
- `EntityCollection` with `findById()` and `ids()` methods
- Automatic response mapping

**Error Handling:**
- `AuthenticationException` (401)
- `AuthorizationException` (403)
- `NotFoundException` (404)
- `ValidationException` (422) with `getErrors()`
- `RateLimitException` (429) with `getRetryAfter()`
- `ServerException` (5xx)
- `ConnectionException` for network errors

**Enums:**
- `DriverActivationStatus`
- `DutyStatus`
- `EngineState`
- `VehicleStatType` (50+ stat types)
- `WebhookEvent` (70+ event types)
- And more...

**Testing Infrastructure:**
- `SamsaraFake` class for mocking API responses
- `Fixtures` class with 13 JSON fixture files
- Comprehensive test coverage (1185 tests)

### Requirements

- PHP 8.1 or higher (for enum support)
- Laravel 10.x, 11.x, or 12.x
