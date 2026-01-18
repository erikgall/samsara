# Samsara SDK - Complete Implementation TODO

## Phase 1: Core Infrastructure

### 1.1 Base Setup
- [x] Remove Saloon dependency from `composer.json`
- [x] Add `illuminate/http` as dependency
- [x] Add `illuminate/collections` as dependency
- [x] Add `illuminate/support` as dependency
- [x] Update minimum PHP version to 8.1 (for enums)
- [x] Update autoload namespace if needed

### 1.2 Configuration
- [x] Create `config/samsara.php`
  - [x] `api_key` - API token from env
  - [x] `region` - US or EU
  - [x] `timeout` - Request timeout
  - [x] `retry` - Retry count
  - [x] `per_page` - Default pagination limit

### 1.3 Main Client (`src/Samsara.php`)
- [x] Create new `Samsara` class (replace Saloon-based version)
  - [x] `__construct(?string $token, array $config = [])`
  - [x] `static make(?string $token, array $config = []): static`
  - [x] `withToken(string $token): static`
  - [x] `useEuEndpoint(): static`
  - [x] `client(): PendingRequest` - Laravel HTTP client
  - [ ] Resource accessor methods (see Phase 7)

### 1.4 Service Provider (`src/SamsaraServiceProvider.php`)
- [x] Rewrite service provider
  - [x] `boot()` - Publish config
  - [x] `register()` - Bind Samsara singleton
  - [x] Handle EU region configuration

### 1.5 Facade (`src/Facades/Samsara.php`)
- [x] Create facade class
  - [x] `getFacadeAccessor()` - Return Samsara class
  - [ ] `static fake(): SamsaraFake` - Testing support (Phase 9)
  - [x] Add `@method` docblocks for IDE support

---

## Phase 2: Exception Hierarchy

### 2.1 Exceptions (`src/Exceptions/`)
- [x] `SamsaraException.php` - Base exception
  - [x] `$context` property
  - [x] `getContext(): array`
- [x] `AuthenticationException.php` - 401 errors
- [x] `AuthorizationException.php` - 403 errors
- [x] `NotFoundException.php` - 404 errors
- [x] `ValidationException.php` - 422 errors
  - [x] `$errors` property
  - [x] `getErrors(): array`
- [x] `RateLimitException.php` - 429 errors
  - [x] `$retryAfter` property
  - [x] `getRetryAfter(): ?int`
- [x] `ServerException.php` - 5xx errors
- [x] `ConnectionException.php` - Network errors

---

## Phase 3: Base Components

### 3.1 Contracts (`src/Contracts/`)
- [x] `EntityInterface.php`
- [x] `ResourceInterface.php`
- [x] `QueryBuilderInterface.php`

### 3.2 Concerns/Traits (`src/Concerns/`)
- [x] `Makeable.php` - Static `make()` factory
- [ ] `HasPagination.php` - Pagination helpers (Phase 4)
- [ ] `HasFiltering.php` - Filter helpers (Phase 4)
- [x] `InteractsWithTime.php` - Date/time formatting

### 3.3 Base Entity (`src/Data/Entity.php`)
- [x] Create `Entity` class extending `Illuminate\Support\Fluent`
  - [x] Extends `Fluent` (provides ArrayAccess, Arrayable, Jsonable, JsonSerializable, magic methods)
  - [x] `static make(array $attributes = []): static` (inherited from Fluent)
  - [x] `fill(array $attributes): static` (inherited from Fluent)
  - [x] `getId(): ?string`
  - [x] Note: Fluent handles `toArray()`, `toJson()`, `__get`, `__set`, `offsetGet`, etc.

### 3.4 Entity Collection (`src/Data/EntityCollection.php`)
- [x] Create `EntityCollection` class extending Illuminate Collection
  - [x] `findById(string $id): ?Entity`
  - [x] `ids(): array`

### 3.5 Base Resource (`src/Resources/Resource.php`)
- [x] Create `Resource` class
  - [x] `$samsara` property
  - [x] `$endpoint` property
  - [x] `$entity` property
  - [x] `__construct(Samsara $samsara)`
  - [x] `client(): PendingRequest`
  - [ ] `query(): Builder` (Phase 4)
  - [x] `all(): EntityCollection`
  - [x] `find(string $id): ?object`
  - [x] `create(array $data): object`
  - [x] `update(string $id, array $data): object`
  - [x] `delete(string $id): bool`
  - [x] `mapToEntity(array $data): object`
  - [x] `mapToEntities(array $data): EntityCollection`
  - [x] `getEndpoint(): string`
  - [x] `handleError(Response $response): void`

---

## Phase 4: Query Builder

### 4.1 Main Builder (`src/Query/Builder.php`)
- [x] Create `Builder` class
  - [x] `$resource` property
  - [x] `$filters` property
  - [x] `$limit` property
  - [x] `$cursor` property
  - [x] `$expand` property
  - [x] `$types` property
  - [x] `$decorations` property
  - [x] Constructor and fluent methods (all return `static` for proper inheritance):
    - [x] `whereTag(array|string $tagIds): static`
    - [x] `whereParentTag(array|string $parentTagIds): static`
    - [x] `whereAttribute(array|string $attributeValueIds): static`
    - [x] `whereVehicle(array|string $vehicleIds): static`
    - [x] `whereDriver(array|string $driverIds): static`
    - [x] `updatedAfter(DateTimeInterface|string $time): static`
    - [x] `createdAfter(DateTimeInterface|string $time): static`
    - [x] `startTime(DateTimeInterface|string $time): static`
    - [x] `endTime(DateTimeInterface|string $time): static`
    - [x] `between(DateTimeInterface|string $start, DateTimeInterface|string $end): static`
    - [x] `types(array|string $types): static`
    - [x] `withDecorations(array|string $decorations): static`
    - [x] `expand(array|string $expand): static`
    - [x] `limit(int $limit): static`
    - [x] `take(int $count): static`
    - [x] `after(string $cursor): static`
    - [x] `where(string $key, mixed $value): static`
  - [x] Execution methods:
    - [x] `get(): EntityCollection`
    - [x] `paginate(?int $perPage = null): CursorPaginator`
    - [x] `lazy(?int $chunkSize = null): LazyCollection`
    - [x] `first(): ?object`
    - [x] `buildQuery(): array`
  - [x] `formatTime(DateTimeInterface|string $time): string` (via InteractsWithTime trait)

### 4.2 Pagination (`src/Query/Pagination/`)
- [x] `Cursor.php`
  - [x] `$endCursor` property
  - [x] `$hasNextPage` property
- [x] `CursorPaginator.php`
  - [x] Iterator implementation
  - [x] `nextPage(): ?CursorPaginator`
  - [x] `hasMorePages(): bool`

---

## Phase 5: Enums

### 5.1 Core Enums (`src/Enums/`)
- [x] `DriverActivationStatus.php` (active, deactivated)
- [x] `DutyStatus.php` (offDuty, sleeperBerth, driving, onDuty, yardMove, personalConveyance)
- [x] `EngineState.php` (On, Off, Idle)
- [x] `VehicleStatType.php` (gps, engineStates, fuelPercents, obdOdometerMeters, etc. - 50+ types)
- [x] `WebhookEvent.php` (70+ webhook event types)
- [x] `DocumentType.php`
- [x] `SafetyEventType.php`
- [x] `HosLogType.php`
- [x] `MaintenanceStatus.php`
- [x] `RouteState.php`
- [x] `AlertType.php`
- [x] `AssetType.php`

---

## Phase 6: Data/Entities (DTOs)

### 6.1 Address Entities (`src/Data/Address/`)
- [x] `Address.php`
  - [x] Properties: id, name, formattedAddress, geofence, notes, tagIds, externalIds, contacts
  - [x] Nested: `AddressGeofence`
- [x] `AddressGeofence.php`
  - [x] Properties: circle, polygon, settings

### 6.2 Driver Entities (`src/Data/Driver/`)
- [x] `Driver.php`
  - [x] Properties: id, name, username, phone, licenseNumber, licenseState, timezone, etc.
  - [x] Casts: createdAtTime, updatedAtTime, driverActivationStatus
  - [x] Nested: eldSettings, carrierSettings, staticAssignedVehicle
  - [x] Methods: `isActive()`, `isDeactivated()`, `getDisplayName()`
- [x] `EldSettings.php`
- [x] `CarrierSettings.php`
- [x] `StaticAssignedVehicle.php`

### 6.3 Vehicle Entities (`src/Data/Vehicle/`)
- [x] `Vehicle.php`
  - [x] Properties: id, name, vin, make, model, year, licensePlate, etc.
  - [x] Nested: gateway, tags, attributes
- [x] `VehicleStats.php`
  - [x] Properties: id, name, time, gps, engineState, fuelPercent, etc.
  - [x] Nested: GpsLocation
- [x] `GpsLocation.php`
  - [x] Properties: latitude, longitude, headingDegrees, speedMilesPerHour, time
- [x] `Gateway.php`
- [x] `StaticAssignedDriver.php`

### 6.4 Equipment Entities (`src/Data/Equipment/`)
- [x] `Equipment.php`
- [ ] `EquipmentStats.php`
- [ ] `EquipmentLocation.php`

### 6.5 Trailer Entities (`src/Data/Trailer/`)
- [x] `Trailer.php`
- [ ] `TrailerStats.php`

### 6.6 Route Entities (`src/Data/Route/`)
- [x] `Route.php`
- [x] `RouteStop.php`
- [x] `RouteSettings.php`

### 6.7 HOS Entities (`src/Data/HoursOfService/`)
- [x] `HosLog.php`
- [x] `HosClock.php`
- [x] `HosViolation.php`
- [x] `HosDailyLog.php`

### 6.8 Safety Entities (`src/Data/Safety/`)
- [x] `SafetyEvent.php`
- [x] `SafetyScore.php`
- [x] `CoachingSession.php`

### 6.9 Maintenance Entities (`src/Data/Maintenance/`)
- [x] `Dvir.php` (Driver Vehicle Inspection Report)
- [x] `Defect.php`
- [x] `DefectType.php`
- [x] `WorkOrder.php`
- [x] `ServiceTask.php`

### 6.10 Document Entities (`src/Data/Document/`)
- [x] `Document.php`
- [x] `DocumentType.php`

### 6.11 User Entities (`src/Data/User/`)
- [x] `User.php`
- [x] `UserRole.php`

### 6.12 Contact Entities (`src/Data/Contact/`)
- [x] `Contact.php`

### 6.13 Tag Entities (`src/Data/Tag/`)
- [x] `Tag.php`

### 6.14 Attribute Entities (`src/Data/Attribute/`)
- [x] `Attribute.php`
- [x] `AttributeValue.php`

### 6.15 Asset Entities (`src/Data/Asset/`)
- [x] `Asset.php`
- [x] `AssetLocation.php`

### 6.16 Industrial Entities (`src/Data/Industrial/`)
- [x] `IndustrialAsset.php`
- [x] `DataInput.php`
- [x] `DataPoint.php`
- [x] `Machine.php`
- [x] `VisionCamera.php`
- [x] `VisionRun.php`

### 6.17 Webhook Entities (`src/Data/Webhook/`)
- [x] `Webhook.php`

### 6.18 Alert Entities (`src/Data/Alert/`)
- [x] `AlertConfiguration.php`
- [x] `AlertIncident.php`

### 6.19 Trip Entities (`src/Data/Trip/`)
- [x] `Trip.php`

### 6.20 Form Entities (`src/Data/Form/`)
- [x] `FormSubmission.php`
- [x] `FormTemplate.php`

### 6.21 Organization Entities (`src/Data/Organization/`)
- [x] `Organization.php`

### 6.22 Gateway Entities (`src/Data/Gateway/`)
- [x] `Gateway.php` (already exists in src/Data/Vehicle/)

### 6.23 LiveShare Entities (`src/Data/LiveShare/`)
- [x] `LiveShare.php`

---

## Phase 7: Resources

### 7.1 Fleet Resources (`src/Resources/Fleet/`)

#### DriversResource
- [x] Create `DriversResource.php`
  - [x] Endpoint: `/fleet/drivers`
  - [x] Entity: `Driver::class`
  - [x] Methods:
    - [x] `active(): Builder` - Filter active drivers
    - [x] `deactivated(): Builder` - Filter deactivated drivers
    - [x] `findByExternalId(string $key, string $value): ?Driver`
    - [x] `deactivate(string $id): Driver`
    - [x] `activate(string $id): Driver`
    - [x] `remoteSignOut(string $id): void`
    - [x] `createAuthToken(string $id): string`
    - [x] `getQrCodes(): Collection`
    - [x] `createQrCode(array $data): object`
    - [x] `deleteQrCode(string $id): bool`
- [x] Add `drivers()` accessor to `Samsara.php`

#### VehiclesResource
- [x] Create `VehiclesResource.php`
  - [x] Endpoint: `/fleet/vehicles`
  - [x] Entity: `Vehicle::class`
  - [x] Standard CRUD methods
  - [x] `findByExternalId(string $key, string $value): ?Vehicle`
  - [x] `query(): Builder`
- [x] Add `vehicles()` accessor to `Samsara.php`

#### TrailersResource
- [x] Create `TrailersResource.php`
  - [x] Endpoint: `/fleet/trailers`
  - [x] Entity: `Trailer::class`
  - [x] Standard CRUD methods
  - [x] `findByExternalId(string $key, string $value): ?Trailer`
  - [x] `query(): Builder`
- [x] Add `trailers()` accessor to `Samsara.php`

#### EquipmentResource
- [x] Create `EquipmentResource.php`
  - [x] Endpoint: `/fleet/equipment`
  - [x] Entity: `Equipment::class`
  - [x] Methods:
    - [x] Standard CRUD
    - [x] `findByExternalId(string $key, string $value): ?Equipment`
    - [x] `query(): Builder`
    - [x] `locations(): Builder` - Equipment locations
    - [x] `locationsHistory(): Builder`
    - [x] `locationsFeed(): Builder`
    - [x] `stats(): Builder` - Equipment stats
    - [x] `statsHistory(): Builder`
    - [x] `statsFeed(): Builder`
- [x] Add `equipment()` accessor to `Samsara.php`

### 7.2 Telematics Resources (`src/Resources/Telematics/`) ✅

#### VehicleStatsResource ✅
- [x] Create `VehicleStatsResource.php`
  - [x] Endpoint: `/fleet/vehicles/stats`
  - [x] Entity: `VehicleStats::class`
  - [x] Methods:
    - [x] `current(): Builder`
    - [x] `feed(): Builder`
    - [x] `history(): Builder`
    - [x] `gps(): Builder`
    - [x] `engineStates(): Builder`
    - [x] `fuelPercents(): Builder`
    - [x] `odometer(): Builder`
- [x] Add `vehicleStats()` accessor to `Samsara.php`

#### VehicleLocationsResource ✅
- [x] Create `VehicleLocationsResource.php`
  - [x] Endpoint: `/fleet/vehicles/locations`
  - [x] Methods:
    - [x] `current(): Builder`
    - [x] `feed(): Builder`
    - [x] `history(): Builder`
- [x] Add `vehicleLocations()` accessor to `Samsara.php`

#### TripsResource ✅
- [x] Create `TripsResource.php`
  - [x] Endpoint: `/trips/stream`
  - [x] Entity: `Trip::class`
  - [x] Methods:
    - [x] `query(): Builder`
    - [x] `completed(): Builder`
    - [x] `inProgress(): Builder`
- [x] Add `trips()` accessor to `Samsara.php`

### 7.3 Safety Resources (`src/Resources/Safety/`) ✅

#### HoursOfServiceResource ✅
- [x] Create `HoursOfServiceResource.php`
  - [x] Endpoint: `/fleet/hos/logs`
  - [x] Entity: `HosLog::class`
  - [x] Methods:
    - [x] `logs(): Builder` - `/fleet/hos/logs`
    - [x] `dailyLogs(): Builder` - `/fleet/hos/daily-logs`
    - [x] `clocks(): Builder` - `/fleet/hos/clocks`
    - [x] `violations(): Builder` - `/fleet/hos/violations`
- [x] Add `hoursOfService()` accessor to `Samsara.php`

#### MaintenanceResource ✅
- [x] Create `MaintenanceResource.php`
  - [x] Endpoint: `/dvirs/stream`
  - [x] Entity: `Dvir::class`
  - [x] Methods:
    - [x] `dvirs(): Builder` - `/dvirs/stream`
    - [x] `createDvir(array $data): Dvir`
    - [x] `defects(): Builder` - `/defects/stream`
- [x] Add `maintenance()` accessor to `Samsara.php`

#### SafetyEventsResource ✅
- [x] Create `SafetyEventsResource.php`
  - [x] Endpoint: `/fleet/safety-events`
  - [x] Entity: `SafetyEvent::class`
  - [x] Methods:
    - [x] `query(): Builder`
    - [x] `auditLogs(): Builder` - `/fleet/safety-events/audit-logs/feed`
- [x] Add `safetyEvents()` accessor to `Samsara.php`

### 7.4 Dispatch Resources (`src/Resources/Dispatch/`) ✅

#### RoutesResource ✅
- [x] Create `RoutesResource.php`
  - [x] Endpoint: `/fleet/routes`
  - [x] Entity: `Route::class`
  - [x] Methods:
    - [x] Standard CRUD (create, find, update, delete, all)
    - [x] `query(): Builder`
    - [x] `auditLogs(): Builder` - `/fleet/routes/audit-logs/feed`
- [x] Add `routes()` accessor to `Samsara.php`

#### AddressesResource ✅
- [x] Create `AddressesResource.php`
  - [x] Endpoint: `/addresses`
  - [x] Entity: `Address::class`
  - [x] Standard CRUD methods
  - [x] `query(): Builder`
- [x] Add `addresses()` accessor to `Samsara.php`

### 7.5 Organization Resources (`src/Resources/Organization/`) ✅

#### UsersResource ✅
- [x] Create `UsersResource.php`
  - [x] Endpoint: `/users`
  - [x] Entity: `User::class`
  - [x] Standard CRUD methods
  - [x] `query(): Builder`
- [x] Add `users()` accessor to `Samsara.php`

#### ContactsResource ✅
- [x] Create `ContactsResource.php`
  - [x] Endpoint: `/contacts`
  - [x] Entity: `Contact::class`
  - [x] Standard CRUD methods
  - [x] `query(): Builder`
- [x] Add `contacts()` accessor to `Samsara.php`

#### TagsResource ✅
- [x] Create `TagsResource.php`
  - [x] Endpoint: `/tags`
  - [x] Entity: `Tag::class`
  - [x] Standard CRUD methods
  - [x] `query(): Builder`
- [x] Add `tags()` accessor to `Samsara.php`

### 7.6 Industrial Resources (`src/Resources/Industrial/`) ✅

#### IndustrialResource ✅
- [x] Create `IndustrialResource.php`
  - [x] Methods:
    - [x] `assets(): Builder` - `/industrial/assets`
    - [x] `createAsset(array $data): IndustrialAsset`
    - [x] `updateAsset(string $id, array $data): IndustrialAsset`
    - [x] `deleteAsset(string $id): bool`
    - [x] `dataInputs(): Builder` - `/industrial/data-inputs`
    - [x] `dataPoints(): Builder`
    - [x] `dataPointsFeed(): Builder`
    - [x] `dataPointsHistory(): Builder`
- [x] Add `industrial()` accessor to `Samsara.php`

#### SensorsResource ✅
- [x] Create `SensorsResource.php` (Legacy v1)
  - [x] Methods:
    - [x] `list(array $data): EntityCollection`
    - [x] `cargo(array $data): array`
    - [x] `door(array $data): array`
    - [x] `humidity(array $data): array`
    - [x] `temperature(array $data): array`
    - [x] `history(array $data): array`
- [x] Add `sensors()` accessor to `Samsara.php`

#### AssetsResource ✅
- [x] Create `AssetsResource.php`
  - [x] Endpoint: `/assets`
  - [x] Methods:
    - [x] Standard CRUD
    - [x] `depreciation(): Builder`
    - [x] `inputsStream(): Builder`
    - [x] `locationAndSpeedStream(): Builder`
    - [x] Legacy: locations, reefers
- [x] Add `assets()` accessor to `Samsara.php`

### 7.7 Integrations Resources (`src/Resources/Integrations/`) ✅

#### WebhooksResource ✅
- [x] Create `WebhooksResource.php`
  - [x] Endpoint: `/webhooks`
  - [x] Entity: `Webhook::class`
  - [x] Standard CRUD methods
- [x] Add `webhooks()` accessor to `Samsara.php`

#### GatewaysResource ✅
- [x] Create `GatewaysResource.php`
  - [x] Endpoint: `/gateways`
  - [x] Entity: `Gateway::class`
  - [x] Methods:
    - [x] `all(): EntityCollection`
    - [x] `create(array $data): Gateway`
    - [x] `delete(string $id): bool`
- [x] Add `gateways()` accessor to `Samsara.php`

#### LiveSharingLinksResource ✅
- [x] Create `LiveSharingLinksResource.php`
  - [x] Endpoint: `/live-shares`
  - [x] Entity: `LiveShare::class`
  - [x] Standard CRUD methods
- [x] Add `liveShares()` accessor to `Samsara.php`

### 7.8 Additional Resources DONE

#### AlertsResource DONE
- [x] Create `AlertsResource.php`
  - [x] Methods:
    - [x] `configurations(): Builder`
    - [x] `createConfiguration(array $data): AlertConfiguration`
    - [x] `updateConfiguration(string $id, array $data): AlertConfiguration`
    - [x] `deleteConfigurations(array $ids): bool`
    - [x] `incidents(): Builder`
- [x] Add `alerts()` accessor to `Samsara.php`

#### FormsResource DONE
- [x] Create `FormsResource.php`
  - [x] Methods:
    - [x] `submissions(): Builder`
    - [x] `submissionsStream(): Builder`
    - [x] `createSubmission(array $data): FormSubmission`
    - [x] `updateSubmission(string $id, array $data): FormSubmission`
    - [x] `templates(): EntityCollection`
    - [x] `exportPdf(array $data): array`
    - [x] `getPdfExport(string $id): array`
- [x] Add `forms()` accessor to `Samsara.php`

#### IftaResource DONE
- [x] Create `IftaResource.php`
  - [x] Methods:
    - [x] `jurisdictionReport(): Builder`
    - [x] `vehicleReport(): Builder`
    - [x] `detailCsv(array $data): array`
    - [x] `getDetailCsv(string $id): array`
- [x] Add `ifta()` accessor to `Samsara.php`

#### IdlingResource DONE
- [x] Create `IdlingResource.php`
  - [x] Endpoint: `/idling/events`
  - [x] Methods:
    - [x] `events(): Builder`
- [x] Add `idling()` accessor to `Samsara.php`

#### IssuesResource DONE
- [x] Create `IssuesResource.php`
  - [x] Methods:
    - [x] `all(): EntityCollection`
    - [x] `stream(): Builder`
    - [x] `update(string $id, array $data): Entity`
- [x] Add `issues()` accessor to `Samsara.php`

#### FuelAndEnergyResource DONE
- [x] Create `FuelAndEnergyResource.php`
  - [x] Methods:
    - [x] `driverEfficiency(): Builder`
    - [x] `vehicleEfficiency(): Builder`
    - [x] `driversFuelEnergyReport(array $data): array`
    - [x] `vehiclesFuelEnergyReport(array $data): array`
    - [x] `createFuelPurchase(array $data): array`
- [x] Add `fuelAndEnergy()` accessor to `Samsara.php`

#### TachographResource (EU Only) DONE
- [x] Create `TachographResource.php`
  - [x] Methods:
    - [x] `driverActivityHistory(): Builder`
    - [x] `driverFilesHistory(): Builder`
    - [x] `vehicleFilesHistory(): Builder`
- [x] Add `tachograph()` accessor to `Samsara.php`

#### SpeedingResource DONE
- [x] Create `SpeedingResource.php`
  - [x] Methods:
    - [x] `intervalsStream(): Builder`
- [x] Add `speeding()` accessor to `Samsara.php`

#### CarrierProposedAssignmentsResource DONE
- [x] Create `CarrierProposedAssignmentsResource.php`
  - [x] Endpoint: `/fleet/carrier-proposed-assignments`
  - [x] Methods:
    - [x] `all(): EntityCollection`
    - [x] `create(array $data): Entity`
    - [x] `delete(string $id): bool`
- [x] Add `carrierProposedAssignments()` accessor to `Samsara.php`

#### DriverVehicleAssignmentsResource DONE
- [x] Create `DriverVehicleAssignmentsResource.php`
  - [x] Endpoint: `/fleet/driver-vehicle-assignments`
  - [x] Methods: all, create, update, delete, query
- [x] Add `driverVehicleAssignments()` accessor to `Samsara.php`

#### DriverTrailerAssignmentsResource DONE
- [x] Create `DriverTrailerAssignmentsResource.php`
  - [x] Endpoint: `/driver-trailer-assignments`
  - [x] Methods: all, create, update, query
- [x] Add `driverTrailerAssignments()` accessor to `Samsara.php`

#### TrailerAssignmentsResource (Legacy) DONE
- [x] Create `TrailerAssignmentsResource.php`
  - [x] Methods:
    - [x] `all(): EntityCollection`
    - [x] `forTrailer(string $trailerId): EntityCollection`
- [x] Add `trailerAssignments()` accessor to `Samsara.php`

#### WorkOrdersResource DONE
- [x] Create `WorkOrdersResource.php`
  - [x] Endpoint: `/maintenance/work-orders`
  - [x] Methods:
    - [x] Standard CRUD
    - [x] `stream(): Builder`
    - [x] `serviceTasks(): EntityCollection`
    - [x] `uploadInvoiceScan(array $data): array`
- [x] Add `workOrders()` accessor to `Samsara.php`

#### CameraMediaResource DONE
- [x] Create `CameraMediaResource.php`
  - [x] Methods:
    - [x] `get(): EntityCollection`
    - [x] `retrieve(array $data): array`
    - [x] `getRetrieval(string $id): array`
- [x] Add `cameraMedia()` accessor to `Samsara.php`

#### HubsResource DONE
- [x] Create `HubsResource.php`
  - [x] Methods:
    - [x] Standard CRUD
    - [x] `query(): Builder`
- [x] Add `hubs()` accessor to `Samsara.php`

#### SettingsResource DONE
- [x] Create `SettingsResource.php`
  - [x] Methods:
    - [x] `compliance(): array`
    - [x] `updateCompliance(array $data): array`
    - [x] `driverApp(): array`
    - [x] `updateDriverApp(array $data): array`
    - [x] `safety(): array`
- [x] Add `settings()` accessor to `Samsara.php`

#### RouteEventsResource DONE
- [x] Create `RouteEventsResource.php`
  - [x] Methods:
    - [x] `query(): Builder`
- [x] Add `routeEvents()` accessor to `Samsara.php`

---

## Phase 8: Beta & Preview APIs DONE

### 8.1 Beta Resources (`src/Resources/Beta/`) DONE

- [x] Create `BetaResource.php` as umbrella for beta endpoints
  - [x] AEMP fleet
  - [x] HOS ELD events
  - [x] Trailer stats (current, feed, history)
  - [x] Vehicle immobilizer stream
  - [x] Industrial jobs CRUD
  - [x] Detections stream
  - [x] Devices list
  - [x] Qualification records/types
  - [x] Readings (definitions, history, latest, create)
  - [x] Reports (configs, datasets, runs)
  - [x] Safety scores (drivers, vehicles)
  - [x] Training (assignments, courses)
- [x] Add `beta()` accessor to `Samsara.php`

### 8.2 Preview Resources (`src/Resources/Preview/`) DONE

- [x] Create `PreviewResource.php`
  - [x] Create driver auth token
  - [x] Vehicle lock/unlock
- [x] Add `preview()` accessor to `Samsara.php`

### 8.3 Legacy Resources (`src/Resources/Legacy/`) DONE

- [x] Create `LegacyResource.php` as umbrella for v1 endpoints
  - [x] Fleet assets
  - [x] Asset locations
  - [x] Asset reefers
  - [x] Dispatch routes
  - [x] Driver safety score
  - [x] HOS authentication logs
  - [x] Maintenance list
  - [x] Messages
  - [x] Trips
  - [x] Vehicle harsh event
  - [x] Vehicle safety score
  - [x] Vision cameras
  - [x] Machines
- [x] Add `legacy()` accessor to `Samsara.php`

---

## Phase 9: Testing

### 9.1 Testing Infrastructure (`src/Testing/`) DONE
- [x] Create `SamsaraFake.php`
  - [x] `$responses` property - Queued responses
  - [x] `$fakeHttp` property - Faked HTTP client
  - [x] `fakeResponse(string $endpoint, array $data, int $status = 200): static`
  - [x] `fakeDrivers(array $drivers): static`
  - [x] `fakeVehicles(array $vehicles): static`
  - [x] `fakeVehicleStats(array $stats): static`
  - [x] `assertRequested(string $endpoint): static`
  - [x] `assertRequestedWithParams(string $endpoint, array $params): static`
  - [x] `assertNothingRequested(): static`
  - [x] `getRecordedRequests(): array`
- [x] Create `Fixtures.php` loader class

### 9.2 Test Fixtures (`src/Testing/Fixtures/`) DONE
- [x] `drivers.json`
- [x] `vehicles.json`
- [x] `vehicle-stats.json`
- [x] `trailers.json`
- [x] `equipment.json`
- [x] `routes.json`
- [x] `addresses.json`
- [x] `hos-logs.json`
- [x] `dvirs.json`
- [x] `safety-events.json`
- [x] `webhooks.json`
- [x] `users.json`
- [x] `tags.json`

### 9.3 Unit Tests (`tests/Unit/`) DONE
- [x] `SamsaraTest.php` - Main client tests (37 tests)
- [x] `Data/EntityTest.php` - Entity base class tests
- [x] `Data/EntityCollectionTest.php` - Collection tests
- [x] `Query/BuilderTest.php` - Query builder tests
- [x] `Query/Pagination/CursorPaginatorTest.php` - Paginator tests
- [x] `Resources/ResourceTest.php` - Base resource tests
- [x] 100+ additional tests for all resources, data classes, and enums

### 9.4 Integration/Feature Tests DONE
Note: Unit tests include HTTP mocking and test full integration paths.
Resource tests (DriversResourceTest, VehiclesResourceTest, etc.) test:
- [x] HTTP client integration with mocked responses
- [x] Entity mapping from API responses
- [x] Error handling (404, etc.)
- [x] Query builder integration
- [x] All CRUD operations

---

## Phase 10: Refactoring & Quality Assurance DONE

### 10.1 Code Review DONE
- [x] Review all code for consistency and patterns
- [x] Identify and document edge cases
- [x] Review error handling across all resources
- [x] Ensure consistent naming conventions

### 10.2 Refactoring DONE
- [x] Extract common patterns into traits/base classes (Makeable, InteractsWithTime)
- [x] Reduce code duplication across resources (base Resource class)
- [x] Improve method/variable naming where needed
- [x] Simplify complex methods (break into smaller pieces)
- [x] Review and optimize class dependencies

### 10.3 Static Analysis DONE
- [x] Configure PHPStan (level 8)
- [x] Run PHPStan and fix all issues (0 errors)
- [x] Add missing type hints where detected
- [x] Fix any deprecated usage

### 10.4 Code Style DONE
- [x] Configure Laravel Pint
- [x] Run Pint and fix all style issues
- [x] Ensure consistent formatting across codebase
- [x] Review and standardize docblock format

### 10.5 Performance DONE
- [x] Profile HTTP request/response handling (Laravel HTTP client with retry)
- [x] Optimize lazy collection implementation (uses LazyCollection for streaming)
- [x] Review memory usage for large datasets (pagination support)
- [x] Identify and optimize hot paths (caching resource instances)

### 10.6 Security Audit DONE
- [x] Review for injection vulnerabilities (none - uses HTTP client properly)
- [x] Audit token/credential handling (token passed via Bearer auth)
- [x] Check for data leaks in error messages (context is controlled)
- [x] Review input validation (handled by API)

### 10.7 Dependency Audit DONE
- [x] Review all dependencies for security issues (composer audit: no issues)
- [x] Update dependencies to latest stable versions
- [x] Remove any unused dependencies (none)
- [x] Document minimum version requirements (PHP 8.1, Laravel 10+)

---

## Phase 11: Documentation

### 11.1 README & Getting Started DONE
- [x] Write comprehensive README.md
  - [x] Project overview and features
  - [x] Installation instructions
  - [x] Quick start examples
  - [x] Available resources documentation
  - [x] Query builder documentation
  - [x] Entity documentation
  - [x] Error handling documentation
  - [x] Testing documentation
- [ ] Create `docs/getting-started.md` - Full setup guide
- [ ] Create `docs/configuration.md` - All config options

### 11.2 Resource Documentation (`docs/resources/`)
- [ ] `docs/resources/drivers.md` - Drivers resource guide
- [ ] `docs/resources/vehicles.md` - Vehicles resource guide
- [ ] `docs/resources/vehicle-stats.md` - Vehicle stats guide
- [ ] `docs/resources/equipment.md` - Equipment resource guide
- [ ] `docs/resources/routes.md` - Routes resource guide
- [ ] `docs/resources/hours-of-service.md` - HOS guide
- [ ] `docs/resources/maintenance.md` - Maintenance guide
- [ ] `docs/resources/safety.md` - Safety resource guide
- [ ] `docs/resources/webhooks.md` - Webhooks guide
- [ ] (Add documentation for remaining resources)

### 11.3 Guides & Tutorials
- [ ] `docs/query-builder.md` - Query builder complete guide
- [ ] `docs/pagination.md` - Pagination and lazy loading
- [ ] `docs/testing.md` - Testing with SamsaraFake
- [ ] `docs/error-handling.md` - Exception handling guide
- [ ] `docs/upgrading.md` - Migration from previous versions

### 11.4 API Reference (PHPDoc)
- [ ] Complete PHPDoc on all public classes
- [ ] Complete PHPDoc on all public methods
- [ ] Add `@method` annotations to Samsara.php for IDE support
- [ ] Add `@property` annotations to Entity classes
- [ ] Add `@throws` annotations for exceptions
- [ ] Configure phpDocumentor for generated docs
- [ ] Generate and publish API reference docs

### 11.5 AI-Friendly Documentation (Laravel Boost)

Following [Laravel's AI Package Guidelines](https://laravel.com/docs/12.x/ai#package-guidelines):

#### Directory Structure
- [ ] Create `resources/boost/guidelines/` directory

#### Core Guidelines (`resources/boost/guidelines/core.blade.php`)
- [ ] Create `core.blade.php` with:
  - [ ] Package name and description
  - [ ] Installation instructions
  - [ ] Quick start examples with `<code-snippet>` tags
  - [ ] Configuration overview
  - [ ] Basic usage patterns

#### Resources Guidelines (`resources/boost/guidelines/resources.blade.php`)
- [ ] Create `resources.blade.php` with:
  - [ ] List of all 40+ resources
  - [ ] CRUD method examples for each resource type
  - [ ] Code snippets for common operations:
    - [ ] Drivers: list, find, create, update, deactivate
    - [ ] Vehicles: list, find, update
    - [ ] Vehicle Stats: current, feed, history
    - [ ] Routes: CRUD operations
    - [ ] HOS: logs, clocks, violations
  - [ ] Resource-specific features and methods

#### Query Builder Guidelines (`resources/boost/guidelines/query-builder.blade.php`)
- [ ] Create `query-builder.blade.php` with:
  - [ ] All filter methods (`whereTag`, `whereDriver`, `whereVehicle`, etc.)
  - [ ] Time range methods (`between`, `startTime`, `endTime`)
  - [ ] Pagination methods (`limit`, `after`, `paginate`)
  - [ ] Execution methods (`get`, `first`, `lazy`)
  - [ ] Code snippets for each method
  - [ ] Chaining examples

#### Entities Guidelines (`resources/boost/guidelines/entities.blade.php`)
- [ ] Create `entities.blade.php` with:
  - [ ] Entity base class usage (extends Fluent)
  - [ ] Common entity properties
  - [ ] EntityCollection methods
  - [ ] Code snippets for working with entities

#### Testing Guidelines (`resources/boost/guidelines/testing.blade.php`)
- [ ] Create `testing.blade.php` with:
  - [ ] SamsaraFake setup
  - [ ] Mocking responses
  - [ ] Assertion methods
  - [ ] Code snippets for test scenarios

### 11.6 Laravel Boost Format Requirements
- [ ] Use `.blade.php` file extension
- [ ] Wrap code examples in `@verbatim` blocks
- [ ] Use `<code-snippet name="..." lang="php">` for all examples
- [ ] Keep descriptions concise and actionable
- [ ] Focus on best practices and common patterns
- [ ] Include practical, copy-paste ready examples
- [ ] Test guidelines are discovered by `boost:install`

---

## Phase 12: Release

### 12.1 Cleanup DONE
- [x] Remove old Saloon-based classes (src/Resource.php, src/Resource/, src/Requests/)
- [x] Remove old Request classes (src/Requests/)
- [x] Remove old Entity classes (src/Entities/)
- [x] Remove old backup files (src/Samsara.php.bak)
- [x] Remove empty directories (src/Http/)
- [x] Clean up temporary/debug code

### 12.2 Package Configuration DONE
- [x] Update composer.json
  - [x] Remove saloon dependency (already done)
  - [x] Update description
  - [x] Update keywords
  - [x] Verify autoload configuration
- [x] Verify Laravel package discovery
- [x] Test fresh installation (all 1185 tests pass)

### 12.3 Changelog & Versioning
- [ ] Write comprehensive CHANGELOG.md
- [ ] Document all breaking changes
- [ ] Document new features
- [ ] Document deprecations
- [ ] Choose version number (2.0.0 for breaking change)

### 12.4 Release Preparation
- [ ] Create release branch
- [ ] Final test suite run
- [ ] Final static analysis run
- [ ] Review all documentation
- [ ] Tag release version
- [ ] Publish to Packagist

### 12.5 Post-Release
- [ ] Announce release
- [ ] Monitor for issues
- [ ] Prepare hotfix process if needed

---

## Summary

**Total Tasks: ~450+**

| Phase | Category | Est. Tasks |
|-------|----------|------------|
| 1 | Core Infrastructure | 20 |
| 2 | Exceptions | 8 |
| 3 | Base Components | 25 |
| 4 | Query Builder | 30 |
| 5 | Enums | 15 |
| 6 | Data/Entities | 60 |
| 7 | Resources | 150 |
| 8 | Beta/Preview/Legacy | 40 |
| 9 | Testing | 50 |
| 10 | Refactoring & QA | 30 |
| 11 | Documentation | 45 |
| 12 | Release | 20 |

**Priority Order:**
1. Phases 1-4 (Core Infrastructure) - Must be done first
2. Phases 5-6 (Enums & Entities) - Can be incremental
3. Phase 7 (Resources) - Can be done per-resource
4. Phases 8-9 (Beta, Testing) - Can be parallel
5. Phase 10 (Refactoring & QA) - After implementation complete
6. Phase 11 (Documentation) - Can start during Phase 10
7. Phase 12 (Release) - Final step
