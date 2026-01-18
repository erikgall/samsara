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
- [ ] `DefectType.php`
- [x] `WorkOrder.php`
- [ ] `ServiceTask.php`

### 6.10 Document Entities (`src/Data/Document/`)
- [ ] `Document.php`
- [ ] `DocumentType.php`

### 6.11 User Entities (`src/Data/User/`)
- [ ] `User.php`
- [ ] `UserRole.php`

### 6.12 Contact Entities (`src/Data/Contact/`)
- [x] `Contact.php`

### 6.13 Tag Entities (`src/Data/Tag/`)
- [x] `Tag.php`

### 6.14 Attribute Entities (`src/Data/Attribute/`)
- [ ] `Attribute.php`
- [ ] `AttributeValue.php`

### 6.15 Asset Entities (`src/Data/Asset/`)
- [ ] `Asset.php`
- [ ] `AssetLocation.php`

### 6.16 Industrial Entities (`src/Data/Industrial/`)
- [ ] `IndustrialAsset.php`
- [ ] `DataInput.php`
- [ ] `DataPoint.php`
- [ ] `Machine.php`
- [ ] `VisionCamera.php`
- [ ] `VisionRun.php`

### 6.17 Webhook Entities (`src/Data/Webhook/`)
- [ ] `Webhook.php`

### 6.18 Alert Entities (`src/Data/Alert/`)
- [ ] `AlertConfiguration.php`
- [ ] `AlertIncident.php`

### 6.19 Trip Entities (`src/Data/Trip/`)
- [ ] `Trip.php`

### 6.20 Form Entities (`src/Data/Form/`)
- [ ] `FormSubmission.php`
- [ ] `FormTemplate.php`

### 6.21 Organization Entities (`src/Data/Organization/`)
- [ ] `Organization.php`

### 6.22 Gateway Entities (`src/Data/Gateway/`)
- [ ] `Gateway.php`

### 6.23 LiveShare Entities (`src/Data/LiveShare/`)
- [ ] `LiveShare.php`

---

## Phase 7: Resources

### 7.1 Fleet Resources (`src/Resources/Fleet/`)

#### DriversResource
- [ ] Create `DriversResource.php`
  - [ ] Endpoint: `/fleet/drivers`
  - [ ] Entity: `Driver::class`
  - [ ] Methods:
    - [ ] `active(): Builder` - Filter active drivers
    - [ ] `deactivated(): Builder` - Filter deactivated drivers
    - [ ] `findByExternalId(string $key, string $value): ?Driver`
    - [ ] `deactivate(string $id): Driver`
    - [ ] `activate(string $id): Driver`
    - [ ] `remoteSignOut(string $id): void`
    - [ ] `createAuthToken(string $id): string`
    - [ ] `getQrCodes(): EntityCollection`
    - [ ] `createQrCode(array $data): object`
    - [ ] `deleteQrCode(string $id): bool`
- [ ] Add `drivers()` accessor to `Samsara.php`

#### VehiclesResource
- [ ] Create `VehiclesResource.php`
  - [ ] Endpoint: `/fleet/vehicles`
  - [ ] Entity: `Vehicle::class`
  - [ ] Standard CRUD methods
- [ ] Add `vehicles()` accessor to `Samsara.php`

#### TrailersResource
- [ ] Create `TrailersResource.php`
  - [ ] Endpoint: `/fleet/trailers`
  - [ ] Entity: `Trailer::class`
  - [ ] Methods: CRUD + trailer-specific
- [ ] Add `trailers()` accessor to `Samsara.php`

#### EquipmentResource
- [ ] Create `EquipmentResource.php`
  - [ ] Endpoint: `/fleet/equipment`
  - [ ] Entity: `Equipment::class`
  - [ ] Methods:
    - [ ] Standard CRUD
    - [ ] `locations(): Builder` - Equipment locations
    - [ ] `locationsHistory(): Builder`
    - [ ] `locationsFeed(): Builder`
    - [ ] `stats(): Builder` - Equipment stats
    - [ ] `statsHistory(): Builder`
    - [ ] `statsFeed(): Builder`
- [ ] Add `equipment()` accessor to `Samsara.php`

### 7.2 Telematics Resources (`src/Resources/Telematics/`)

#### VehicleStatsResource
- [ ] Create `VehicleStatsResource.php`
  - [ ] Endpoint: `/fleet/vehicles/stats`
  - [ ] Entity: `VehicleStats::class`
  - [ ] Methods:
    - [ ] `current(): VehicleStatsQueryBuilder`
    - [ ] `feed(): VehicleStatsQueryBuilder`
    - [ ] `history(): VehicleStatsQueryBuilder`
    - [ ] `gps(): VehicleStatsQueryBuilder`
    - [ ] `engineStates(): VehicleStatsQueryBuilder`
    - [ ] `fuelPercents(): VehicleStatsQueryBuilder`
- [ ] Create `VehicleStatsQueryBuilder.php` (specialized builder)
  - [ ] `types(array $types): self`
  - [ ] `withGps(): self`
  - [ ] `withEngineStates(): self`
  - [ ] `withOdometer(): self`
  - [ ] `withFuelPercent(): self`
- [ ] Add `vehicleStats()` accessor to `Samsara.php`

#### VehicleLocationsResource
- [ ] Create `VehicleLocationsResource.php`
  - [ ] Endpoint: `/fleet/vehicles/locations`
  - [ ] Methods:
    - [ ] `current(): Builder`
    - [ ] `feed(): Builder`
    - [ ] `history(): Builder`
- [ ] Add `vehicleLocations()` accessor to `Samsara.php`

#### TripsResource
- [ ] Create `TripsResource.php`
  - [ ] Endpoint: `/trips/stream`
  - [ ] Entity: `Trip::class`
  - [ ] Methods:
    - [ ] `stream(): Builder`
    - [ ] Legacy: `get(array $params): EntityCollection`
- [ ] Add `trips()` accessor to `Samsara.php`

### 7.3 Safety Resources (`src/Resources/Safety/`)

#### HoursOfServiceResource
- [ ] Create `HoursOfServiceResource.php`
  - [ ] Methods:
    - [ ] `logs(): Builder` - `/fleet/hos/logs`
    - [ ] `dailyLogs(): Builder` - `/fleet/hos/daily-logs`
    - [ ] `clocks(): Builder` - `/fleet/hos/clocks`
    - [ ] `violations(): Builder` - `/fleet/hos/violations`
    - [ ] `authenticationLogs(): Builder` (legacy)
    - [ ] `setDutyStatus(string $driverId, string $status): void` (legacy)
- [ ] Add `hoursOfService()` accessor to `Samsara.php`

#### MaintenanceResource
- [ ] Create `MaintenanceResource.php`
  - [ ] Methods:
    - [ ] `dvirs(): Builder` - `/dvirs/stream`
    - [ ] `createDvir(array $data): Dvir`
    - [ ] `updateDvir(string $id, array $data): Dvir`
    - [ ] `defects(): Builder` - `/defects/stream`
    - [ ] `defectTypes(): EntityCollection`
    - [ ] `updateDefect(string $id, array $data): Defect`
    - [ ] Legacy: `dvirsHistory()`, `defectsHistory()`
- [ ] Add `maintenance()` accessor to `Samsara.php`

#### SafetyResource
- [ ] Create `SafetyResource.php`
  - [ ] Methods:
    - [ ] `events(): Builder` - `/safety-events`
    - [ ] `eventsStream(): Builder` - `/safety-events/stream`
    - [ ] `driverScores(): Builder` (beta)
    - [ ] `vehicleScores(): Builder` (beta)
    - [ ] Legacy: `driverScore()`, `vehicleScore()`, `harshEvent()`
- [ ] Add `safety()` accessor to `Samsara.php`

#### CoachingResource
- [ ] Create `CoachingResource.php`
  - [ ] Methods:
    - [ ] `sessions(): Builder` - `/coaching/sessions/stream`
    - [ ] `driverCoachAssignments(): EntityCollection`
    - [ ] `updateDriverCoachAssignments(array $data): void`
- [ ] Add `coaching()` accessor to `Samsara.php`

### 7.4 Dispatch Resources (`src/Resources/Dispatch/`)

#### RoutesResource
- [ ] Create `RoutesResource.php`
  - [ ] Endpoint: `/fleet/routes`
  - [ ] Entity: `Route::class`
  - [ ] Methods:
    - [ ] Standard CRUD
    - [ ] `auditLogs(): Builder` - `/fleet/routes/audit-logs/feed`
    - [ ] `hubRoutes(): Builder` - `/hub/plan/routes`
    - [ ] Legacy: `deleteByExternalId()`
- [ ] Add `routes()` accessor to `Samsara.php`

#### AddressesResource
- [ ] Create `AddressesResource.php`
  - [ ] Endpoint: `/addresses`
  - [ ] Entity: `Address::class`
  - [ ] Standard CRUD methods
- [ ] Add `addresses()` accessor to `Samsara.php`

#### DocumentsResource
- [ ] Create `DocumentsResource.php`
  - [ ] Endpoint: `/fleet/documents`
  - [ ] Methods:
    - [ ] Standard CRUD
    - [ ] `types(): EntityCollection` - Document types
    - [ ] `createPdf(array $data): object`
    - [ ] `getPdf(string $id): object`
- [ ] Add `documents()` accessor to `Samsara.php`

#### MessagesResource
- [ ] Create `MessagesResource.php` (Legacy)
  - [ ] Endpoint: `/v1/fleet/messages`
  - [ ] Methods: `get()`, `send()`
- [ ] Add `messages()` accessor to `Samsara.php`

### 7.5 Organization Resources (`src/Resources/Organization/`)

#### UsersResource
- [ ] Create `UsersResource.php`
  - [ ] Endpoint: `/users`
  - [ ] Entity: `User::class`
  - [ ] Methods:
    - [ ] Standard CRUD
    - [ ] `roles(): EntityCollection` - `/user-roles`
- [ ] Add `users()` accessor to `Samsara.php`

#### ContactsResource
- [ ] Create `ContactsResource.php`
  - [ ] Endpoint: `/contacts`
  - [ ] Entity: `Contact::class`
  - [ ] Standard CRUD methods
- [ ] Add `contacts()` accessor to `Samsara.php`

#### TagsResource
- [ ] Create `TagsResource.php`
  - [ ] Endpoint: `/tags`
  - [ ] Entity: `Tag::class`
  - [ ] Methods:
    - [ ] Standard CRUD
    - [ ] `replace(string $id, array $data): Tag` (PUT endpoint)
- [ ] Add `tags()` accessor to `Samsara.php`

#### AttributesResource
- [ ] Create `AttributesResource.php`
  - [ ] Endpoint: `/attributes`
  - [ ] Entity: `Attribute::class`
  - [ ] Standard CRUD methods
- [ ] Add `attributes()` accessor to `Samsara.php`

#### OrganizationInfoResource
- [ ] Create `OrganizationInfoResource.php`
  - [ ] Endpoint: `/me`
  - [ ] Methods:
    - [ ] `get(): Organization`
- [ ] Add `organization()` accessor to `Samsara.php`

### 7.6 Industrial Resources (`src/Resources/Industrial/`)

#### IndustrialResource
- [ ] Create `IndustrialResource.php`
  - [ ] Methods:
    - [ ] `assets(): Builder` - `/industrial/assets`
    - [ ] `createAsset(array $data): IndustrialAsset`
    - [ ] `updateAsset(string $id, array $data): IndustrialAsset`
    - [ ] `deleteAsset(string $id): bool`
    - [ ] `dataInputs(): Builder` - `/industrial/data-inputs`
    - [ ] `dataPoints(): Builder`
    - [ ] `dataPointsFeed(): Builder`
    - [ ] `dataPointsHistory(): Builder`
    - [ ] Vision methods (legacy v1 endpoints)
    - [ ] Machine methods (legacy v1 endpoints)
- [ ] Add `industrial()` accessor to `Samsara.php`

#### SensorsResource
- [ ] Create `SensorsResource.php` (Legacy v1)
  - [ ] Methods:
    - [ ] `list(array $data): EntityCollection`
    - [ ] `cargo(array $data): object`
    - [ ] `door(array $data): object`
    - [ ] `humidity(array $data): object`
    - [ ] `temperature(array $data): object`
    - [ ] `history(array $data): object`
- [ ] Add `sensors()` accessor to `Samsara.php`

#### AssetsResource
- [ ] Create `AssetsResource.php`
  - [ ] Endpoint: `/assets`
  - [ ] Methods:
    - [ ] Standard CRUD
    - [ ] `depreciation(): Builder`
    - [ ] `inputsStream(): Builder`
    - [ ] `locationAndSpeedStream(): Builder`
    - [ ] Legacy: locations, reefers
- [ ] Add `assets()` accessor to `Samsara.php`

### 7.7 Integrations Resources (`src/Resources/Integrations/`)

#### WebhooksResource
- [ ] Create `WebhooksResource.php`
  - [ ] Endpoint: `/webhooks`
  - [ ] Entity: `Webhook::class`
  - [ ] Standard CRUD methods
- [ ] Add `webhooks()` accessor to `Samsara.php`

#### GatewaysResource
- [ ] Create `GatewaysResource.php`
  - [ ] Endpoint: `/gateways`
  - [ ] Entity: `Gateway::class`
  - [ ] Methods:
    - [ ] `all(): EntityCollection`
    - [ ] `create(array $data): Gateway`
    - [ ] `delete(string $id): bool`
- [ ] Add `gateways()` accessor to `Samsara.php`

#### LiveSharingLinksResource
- [ ] Create `LiveSharingLinksResource.php`
  - [ ] Endpoint: `/live-shares`
  - [ ] Entity: `LiveShare::class`
  - [ ] Standard CRUD methods
- [ ] Add `liveShares()` accessor to `Samsara.php`

### 7.8 Additional Resources

#### AlertsResource
- [ ] Create `AlertsResource.php`
  - [ ] Methods:
    - [ ] `configurations(): Builder`
    - [ ] `createConfiguration(array $data): AlertConfiguration`
    - [ ] `updateConfiguration(array $data): AlertConfiguration`
    - [ ] `deleteConfiguration(array $ids): bool`
    - [ ] `incidents(): Builder`
- [ ] Add `alerts()` accessor to `Samsara.php`

#### FormsResource
- [ ] Create `FormsResource.php`
  - [ ] Methods:
    - [ ] `submissions(): Builder`
    - [ ] `submissionsStream(): Builder`
    - [ ] `createSubmission(array $data): FormSubmission`
    - [ ] `updateSubmission(string $id, array $data): FormSubmission`
    - [ ] `templates(): EntityCollection`
    - [ ] `exportPdf(array $data): object`
    - [ ] `getPdfExport(string $id): object`
- [ ] Add `forms()` accessor to `Samsara.php`

#### IftaResource
- [ ] Create `IftaResource.php`
  - [ ] Methods:
    - [ ] `jurisdictionReport(): object`
    - [ ] `vehicleReport(): object`
    - [ ] `detailCsv(): object`
    - [ ] `getDetailCsv(string $id): object`
- [ ] Add `ifta()` accessor to `Samsara.php`

#### IdlingResource
- [ ] Create `IdlingResource.php`
  - [ ] Endpoint: `/idling/events`
  - [ ] Methods:
    - [ ] `events(): Builder`
- [ ] Add `idling()` accessor to `Samsara.php`

#### IssuesResource
- [ ] Create `IssuesResource.php`
  - [ ] Methods:
    - [ ] `all(): EntityCollection`
    - [ ] `stream(): Builder`
    - [ ] `update(string $id, array $data): object`
- [ ] Add `issues()` accessor to `Samsara.php`

#### FuelAndEnergyResource
- [ ] Create `FuelAndEnergyResource.php`
  - [ ] Methods:
    - [ ] `driverEfficiency(): Builder`
    - [ ] `vehicleEfficiency(): Builder`
    - [ ] `driversFuelEnergyReport(): object`
    - [ ] `vehiclesFuelEnergyReport(): object`
    - [ ] `createFuelPurchase(array $data): object`
- [ ] Add `fuelAndEnergy()` accessor to `Samsara.php`

#### TachographResource (EU Only)
- [ ] Create `TachographResource.php`
  - [ ] Methods:
    - [ ] `driverActivityHistory(): Builder`
    - [ ] `driverFilesHistory(): Builder`
    - [ ] `vehicleFilesHistory(): Builder`
- [ ] Add `tachograph()` accessor to `Samsara.php`

#### SpeedingResource
- [ ] Create `SpeedingResource.php`
  - [ ] Methods:
    - [ ] `intervalsStream(): Builder`
- [ ] Add `speeding()` accessor to `Samsara.php`

#### CarrierProposedAssignmentsResource
- [ ] Create `CarrierProposedAssignmentsResource.php`
  - [ ] Endpoint: `/fleet/carrier-proposed-assignments`
  - [ ] Methods:
    - [ ] `all(): EntityCollection`
    - [ ] `create(array $data): object`
    - [ ] `delete(string $id): bool`
- [ ] Add `carrierProposedAssignments()` accessor to `Samsara.php`

#### DriverVehicleAssignmentsResource
- [ ] Create `DriverVehicleAssignmentsResource.php`
  - [ ] Endpoint: `/fleet/driver-vehicle-assignments`
  - [ ] Methods: GET, POST, PATCH, DELETE
- [ ] Add `driverVehicleAssignments()` accessor to `Samsara.php`

#### DriverTrailerAssignmentsResource
- [ ] Create `DriverTrailerAssignmentsResource.php`
  - [ ] Endpoint: `/driver-trailer-assignments`
  - [ ] Methods: GET, POST, PATCH
- [ ] Add `driverTrailerAssignments()` accessor to `Samsara.php`

#### TrailerAssignmentsResource (Legacy)
- [ ] Create `TrailerAssignmentsResource.php`
  - [ ] Methods:
    - [ ] `all(): EntityCollection`
    - [ ] `forTrailer(string $trailerId): EntityCollection`
- [ ] Add `trailerAssignments()` accessor to `Samsara.php`

#### WorkOrdersResource
- [ ] Create `WorkOrdersResource.php`
  - [ ] Endpoint: `/maintenance/work-orders`
  - [ ] Methods:
    - [ ] Standard CRUD
    - [ ] `stream(): Builder`
    - [ ] `serviceTasks(): EntityCollection`
    - [ ] `uploadInvoiceScan(array $data): object`
- [ ] Add `workOrders()` accessor to `Samsara.php`

#### CameraMediaResource
- [ ] Create `CameraMediaResource.php`
  - [ ] Methods:
    - [ ] `get(): EntityCollection`
    - [ ] `retrieve(array $data): object`
    - [ ] `getRetrieval(string $id): object`
- [ ] Add `cameraMedia()` accessor to `Samsara.php`

#### HubsResource
- [ ] Create `HubsResource.php`
  - [ ] Methods:
    - [ ] `all(): EntityCollection` - `/hubs`
    - [ ] `locations(): EntityCollection`
    - [ ] `createLocation(array $data): object`
    - [ ] `updateLocation(string $id, array $data): object`
    - [ ] `capacities(): EntityCollection`
    - [ ] `skills(): EntityCollection`
    - [ ] `createPlan(array $data): object`
    - [ ] `plans(): EntityCollection`
    - [ ] `createPlanOrders(array $data): object`
- [ ] Add `hubs()` accessor to `Samsara.php`

#### SettingsResource
- [ ] Create `SettingsResource.php`
  - [ ] Methods:
    - [ ] `compliance(): object`
    - [ ] `updateCompliance(array $data): object`
    - [ ] `driverApp(): object`
    - [ ] `updateDriverApp(array $data): object`
    - [ ] `safety(): object`
- [ ] Add `settings()` accessor to `Samsara.php`

#### RouteEventsResource
- [ ] Create `RouteEventsResource.php`
  - [ ] Methods:
    - [ ] `stream(): Builder`
- [ ] Add `routeEvents()` accessor to `Samsara.php`

---

## Phase 8: Beta & Preview APIs

### 8.1 Beta Resources (`src/Resources/Beta/`)

- [ ] Create `BetaResource.php` as umbrella for beta endpoints
  - [ ] Asset depreciation
  - [ ] Asset inputs stream
  - [ ] AEMP fleet
  - [ ] Driver efficiency
  - [ ] Equipment updates
  - [ ] HOS ELD events
  - [ ] Trailer stats (current, feed, history)
  - [ ] Vehicle immobilizer
  - [ ] Industrial jobs CRUD
  - [ ] Detections stream
  - [ ] Devices list
  - [ ] Fleet vehicles immobilizer stream
  - [ ] Functions runs
  - [ ] HOS daily logs meta-data
  - [ ] Hub custom properties
  - [ ] Hub plan orders
  - [ ] Qualification records/types
  - [ ] Readings (definitions, history, latest, create)
  - [ ] Reports (configs, datasets, runs)
  - [ ] Safety scores (drivers, vehicles, tags)
  - [ ] Training (assignments, courses)
- [ ] Add `beta()` accessor to `Samsara.php`

### 8.2 Preview Resources (`src/Resources/Preview/`)

- [ ] Create `PreviewResource.php`
  - [ ] Create driver auth token (new method)
  - [ ] Vehicle lock/unlock
- [ ] Add `preview()` accessor to `Samsara.php`

### 8.3 Legacy Resources (`src/Resources/Legacy/`)

- [ ] Create `LegacyResource.php` as umbrella for v1 endpoints
  - [ ] Fleet assets
  - [ ] Asset locations
  - [ ] Asset reefers
  - [ ] Dispatch routes
  - [ ] Driver safety score
  - [ ] HOS authentication logs
  - [ ] Maintenance list
  - [ ] Messages
  - [ ] Trailer assignments
  - [ ] Trips
  - [ ] Vehicle harsh event
  - [ ] Vehicle safety score
  - [ ] Vision cameras
  - [ ] Machines
  - [ ] Sensors
- [ ] Add `legacy()` accessor to `Samsara.php`

---

## Phase 9: Testing

### 9.1 Testing Infrastructure (`src/Testing/`)
- [ ] Create `SamsaraFake.php`
  - [ ] `$responses` property - Queued responses
  - [ ] `$recordedRequests` property
  - [ ] `fakeResponse(string $endpoint, array $data, int $status = 200): self`
  - [ ] `fakeDrivers(array $drivers): self`
  - [ ] `fakeVehicles(array $vehicles): self`
  - [ ] `fakeVehicleStats(array $stats): self`
  - [ ] `assertRequested(string $endpoint): self`
  - [ ] `assertRequestedWithParams(string $endpoint, array $params): self`
  - [ ] `assertNothingRequested(): self`
  - [ ] `getRecordedRequests(): array`
- [ ] Create `MockResponse.php` helper class

### 9.2 Test Fixtures (`src/Testing/Fixtures/`)
- [ ] `drivers.json`
- [ ] `vehicles.json`
- [ ] `vehicle-stats.json`
- [ ] `trailers.json`
- [ ] `equipment.json`
- [ ] `routes.json`
- [ ] `addresses.json`
- [ ] `hos-logs.json`
- [ ] `dvirs.json`
- [ ] `safety-events.json`
- [ ] `webhooks.json`
- [ ] `users.json`
- [ ] `tags.json`
- [ ] (Add more as needed)

### 9.3 Unit Tests (`tests/Unit/`)
- [ ] `Client/SamsaraTest.php`
- [ ] `Data/EntityTest.php`
- [ ] `Data/EntityCollectionTest.php`
- [ ] `Query/BuilderTest.php`
- [ ] `Query/Pagination/CursorPaginatorTest.php`
- [ ] `Resources/ResourceTest.php`

### 9.4 Feature Tests (`tests/Feature/`)
- [ ] `DriversTest.php`
- [ ] `VehiclesTest.php`
- [ ] `VehicleStatsTest.php`
- [ ] `RoutesTest.php`
- [ ] `AddressesTest.php`
- [ ] `HoursOfServiceTest.php`
- [ ] `MaintenanceTest.php`
- [ ] `SafetyTest.php`
- [ ] `WebhooksTest.php`
- [ ] `TagsTest.php`
- [ ] (Add more as needed)

---

## Phase 10: Refactoring & Quality Assurance

### 10.1 Code Review
- [ ] Review all code for consistency and patterns
- [ ] Identify and document edge cases
- [ ] Review error handling across all resources
- [ ] Ensure consistent naming conventions

### 10.2 Refactoring
- [ ] Extract common patterns into traits/base classes
- [ ] Reduce code duplication across resources
- [ ] Improve method/variable naming where needed
- [ ] Simplify complex methods (break into smaller pieces)
- [ ] Review and optimize class dependencies

### 10.3 Static Analysis
- [ ] Configure PHPStan (level 8)
- [ ] Run PHPStan and fix all issues
- [ ] Add missing type hints where detected
- [ ] Fix any deprecated usage

### 10.4 Code Style
- [ ] Configure Laravel Pint
- [ ] Run Pint and fix all style issues
- [ ] Ensure consistent formatting across codebase
- [ ] Review and standardize docblock format

### 10.5 Performance
- [ ] Profile HTTP request/response handling
- [ ] Optimize lazy collection implementation
- [ ] Review memory usage for large datasets
- [ ] Identify and optimize hot paths

### 10.6 Security Audit
- [ ] Review for injection vulnerabilities
- [ ] Audit token/credential handling
- [ ] Check for data leaks in error messages
- [ ] Review input validation

### 10.7 Dependency Audit
- [ ] Review all dependencies for security issues
- [ ] Update dependencies to latest stable versions
- [ ] Remove any unused dependencies
- [ ] Document minimum version requirements

---

## Phase 11: Documentation

### 11.1 README & Getting Started
- [ ] Write comprehensive README.md
  - [ ] Project overview and features
  - [ ] Installation instructions
  - [ ] Quick start examples
  - [ ] Links to full documentation
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

### 12.1 Cleanup
- [ ] Remove old Saloon-based classes
- [ ] Remove old Request classes
- [ ] Remove old Entity classes (if replaced)
- [ ] Remove any deprecated code
- [ ] Clean up temporary/debug code

### 12.2 Package Configuration
- [ ] Update composer.json
  - [ ] Remove saloon dependency
  - [ ] Update description
  - [ ] Update keywords
  - [ ] Verify autoload configuration
- [ ] Verify Laravel package discovery
- [ ] Test fresh installation

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
