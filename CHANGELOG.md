# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2025-01-17

### Breaking Changes

This is a complete rewrite of the SDK, replacing the Saloon-based implementation with Laravel's HTTP client.

**Removed Dependencies:**
- `saloonphp/saloon` and all related packages have been removed
- `saloonphp/laravel-plugin` has been removed

**New Dependencies:**
- `illuminate/http` - Laravel HTTP Client
- `illuminate/collections` - Laravel Collections
- `illuminate/support` - Laravel Support

**API Changes:**
- All Request classes have been removed (e.g., `src/Requests/*`)
- All old Resource classes have been replaced (e.g., `src/Resource/*`)
- All old Entity classes have been replaced (e.g., `src/Entities/*`)
- The main `Samsara` class has been completely rewritten

### Added

**Core Infrastructure:**
- New `Samsara` client class with fluent API
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

### Changed

- PHP minimum version is now 8.1 (for enum support)
- Laravel minimum version is now 10.x
- All entities now extend `Illuminate\Support\Fluent`
- All methods return `static` instead of `self` for proper inheritance

### Removed

- All Saloon-based Request classes
- All Saloon-based Resource classes
- All old Entity classes
- `Samsara.php.bak` backup file
- Empty `src/Http/` directory

## [1.x] - Previous Versions

See the [previous releases](https://github.com/erikgall/samsara/releases) for the Saloon-based implementation.
