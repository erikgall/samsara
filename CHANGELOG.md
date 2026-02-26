# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
