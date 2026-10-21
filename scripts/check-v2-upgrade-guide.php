<?php

declare(strict_types=1);

const BASELINE_TAG = 'v1.1.0';
const AUDITED_HEAD = '1750395c98c1724c40c82ba82b96b4866dd2106b';
const LEDGER_PATH = __DIR__.'/../docs/api-sync/v2-upgrade-disposition.json';
const GUIDE_PATH = __DIR__.'/../docs/upgrade-guide-v2.md';

/** @return list<string> */
function listedPaths(string $paths): array
{
    return array_values(array_filter(array_map('trim', explode("\n", trim($paths)))));
}

$upgradeActions = listedPaths(<<<'PATHS'
config/samsara.php
src/Contracts/QueryBuilderInterface.php
src/Concerns/InteractsWithTime.php
src/Data/Alert/AlertConfiguration.php
src/Data/Alert/AlertIncident.php
src/Data/Asset/Asset.php
src/Data/Driver/DriverAuthToken.php
src/Data/Driver/DriverQrCode.php
src/Data/Driver/DriverRemoteSignOutResult.php
src/Data/Equipment/EquipmentGpsLocation.php
src/Data/Equipment/EquipmentLocation.php
src/Data/Equipment/EquipmentLocationResult.php
src/Data/Equipment/EquipmentLocationSeries.php
src/Data/Equipment/EquipmentStatValue.php
src/Data/Equipment/EquipmentStats.php
src/Data/Equipment/EquipmentStatsSeries.php
src/Data/Form/FormSubmission.php
src/Data/Form/FormTemplate.php
src/Data/HoursOfService/HosClocksForDriver.php
src/Data/HoursOfService/HosLogsForDriver.php
src/Data/HoursOfService/HosViolation.php
src/Data/HoursOfService/HosViolationGroup.php
src/Data/Industrial/DataInput.php
src/Data/Industrial/DataPoint.php
src/Data/LiveShare/LiveShare.php
src/Data/Maintenance/Defect.php
src/Data/Route/Route.php
src/Data/Route/RouteAuditLog.php
src/Data/Route/RouteStop.php
src/Data/Safety/SafetyEventActivity.php
src/Data/Tag/Tag.php
src/Data/Trailer/Trailer.php
src/Data/Trip/Trip.php
src/Data/User/User.php
src/Data/Vehicle/GpsLocation.php
src/Data/Vehicle/Vehicle.php
src/Data/Vehicle/VehicleLocation.php
src/Data/Vehicle/VehicleLocationResult.php
src/Data/Vehicle/VehicleLocationSeries.php
src/Data/Vehicle/VehicleStatValue.php
src/Data/Vehicle/VehicleStatsSeries.php
src/Data/Webhook/Webhook.php
src/Enums/DutyStatus.php
src/Enums/SafetyEventType.php
src/Enums/VehicleStatType.php
src/Exceptions/ValidationException.php
src/Exceptions/InvalidSignatureException.php
src/Facades/Samsara.php
src/Http/ResponseDecoder.php
src/Query/Builder.php
src/Query/Pagination/CursorPaginator.php
src/Resources/Additional/AlertsResource.php
src/Resources/Additional/CameraMediaResource.php
src/Resources/Additional/CarrierProposedAssignmentsResource.php
src/Resources/Additional/DriverTrailerAssignmentsResource.php
src/Resources/Additional/DriverVehicleAssignmentsResource.php
src/Resources/Additional/FormsResource.php
src/Resources/Additional/FuelAndEnergyResource.php
src/Resources/Additional/HubsResource.php
src/Resources/Additional/IdlingResource.php
src/Resources/Additional/IftaResource.php
src/Resources/Additional/IssuesResource.php
src/Resources/Additional/RouteEventsResource.php
src/Resources/Additional/SettingsResource.php
src/Resources/Additional/SpeedingResource.php
src/Resources/Additional/TachographResource.php
src/Resources/Additional/TrailerAssignmentsResource.php
src/Resources/Additional/WorkOrdersResource.php
src/Resources/Beta/BetaResource.php
src/Resources/Dispatch/AddressesResource.php
src/Resources/Dispatch/RoutesResource.php
src/Resources/Fleet/DriversResource.php
src/Resources/Fleet/EquipmentResource.php
src/Resources/Fleet/TrailersResource.php
src/Resources/Fleet/VehiclesResource.php
src/Resources/Industrial/AssetsResource.php
src/Resources/Industrial/IndustrialResource.php
src/Resources/Industrial/SensorsResource.php
src/Resources/Integrations/GatewaysResource.php
src/Resources/Integrations/LiveSharingLinksResource.php
src/Resources/Integrations/WebhooksResource.php
src/Resources/Legacy/LegacyResource.php
src/Resources/Organization/ContactsResource.php
src/Resources/Organization/TagsResource.php
src/Resources/Organization/UsersResource.php
src/Resources/Preview/PreviewResource.php
src/Resources/Resource.php
src/Resources/Safety/HoursOfServiceResource.php
src/Resources/Safety/MaintenanceResource.php
src/Resources/Safety/SafetyEventsResource.php
src/Resources/Telematics/TripsResource.php
src/Resources/Telematics/VehicleLocationsResource.php
src/Resources/Telematics/VehicleStatsResource.php
src/Data/EntityCollection.php
src/Samsara.php
src/SamsaraServiceProvider.php
src/Webhooks/VerifyWebhookSignature.php
src/Webhooks/WebhookSignatureVerifier.php
PATHS);

$supportingEvidence = listedPaths(<<<'PATHS'
.github/workflows/tests.yml
.prettierrc
composer.lock
DEVELOPMENT_CHANGELOG.md
contracts/samsara/README.md
contracts/samsara/samsara-api-2026-08-10.json
contracts/samsara/samsara-api-2026-08-10.json.metadata.json
docs/api-sync/contract-drift.md
docs/api-sync/contract-inventory.json
docs/api-sync/documentation-coverage.md
docs/api-sync/v2-upgrade-disposition.json
package.json
scripts/check-samsara-contract-reconciliation.php
scripts/check-v2-upgrade-guide.php
scripts/validate-samsara-snapshot.php
tests/Maintenance/ApiSnapshotValidatorTest.php
tests/Unit/BoostDocumentationTest.php
tests/Unit/Data/Alert/AlertConfigurationTest.php
tests/Unit/Data/Alert/AlertIncidentTest.php
tests/Unit/Data/Asset/AssetLocationTest.php
tests/Unit/Data/Asset/AssetTest.php
tests/Unit/Data/Driver/DriverTest.php
tests/Unit/Data/Equipment/EquipmentGpsLocationTest.php
tests/Unit/Data/Equipment/EquipmentLocationTest.php
tests/Unit/Data/Equipment/EquipmentStatsTest.php
tests/Unit/Data/Form/FormSubmissionTest.php
tests/Unit/Data/Form/FormTemplateTest.php
tests/Unit/Data/HoursOfService/HosLogTest.php
tests/Unit/Data/Industrial/DataInputTest.php
tests/Unit/Data/LiveShare/LiveShareTest.php
tests/Unit/Data/Maintenance/DefectTest.php
tests/Unit/Data/Maintenance/DvirTest.php
tests/Unit/Data/Route/RouteStopTest.php
tests/Unit/Data/Route/RouteTest.php
tests/Unit/Data/Safety/SafetyEventTest.php
tests/Unit/Data/Tag/TagTest.php
tests/Unit/Data/Trailer/TrailerTest.php
tests/Unit/Data/Trip/TripTest.php
tests/Unit/Data/User/UserTest.php
tests/Unit/Data/Vehicle/GatewayTest.php
tests/Unit/Data/Vehicle/GpsLocationTest.php
tests/Unit/Data/Vehicle/VehicleTest.php
tests/Unit/Data/Webhook/WebhookTest.php
tests/Unit/Enums/DutyStatusTest.php
tests/Unit/Exceptions/HttpExceptionsTest.php
tests/Unit/Facades/SamsaraFacadeTest.php
tests/Unit/Http/QueryStringTest.php
tests/Unit/Query/BuilderTest.php
tests/Unit/Resources/Additional/AlertsResourceTest.php
tests/Unit/Resources/Additional/CameraMediaResourceTest.php
tests/Unit/Resources/Additional/CurrentContractAResourceTest.php
tests/Unit/Resources/Additional/CurrentContractBResourceTest.php
tests/Unit/Resources/Additional/DriverTrailerAssignmentsResourceTest.php
tests/Unit/Resources/Additional/DriverVehicleAssignmentsResourceTest.php
tests/Unit/Resources/Additional/FormsResourceTest.php
tests/Unit/Resources/Additional/FuelAndEnergyResourceTest.php
tests/Unit/Resources/Additional/GenericCapabilitiesTest.php
tests/Unit/Resources/Additional/HubsResourceTest.php
tests/Unit/Resources/Additional/IftaResourceTest.php
tests/Unit/Resources/Additional/IssuesResourceTest.php
tests/Unit/Resources/Additional/RouteEventsResourceTest.php
tests/Unit/Resources/Additional/SettingsResourceTest.php
tests/Unit/Resources/Additional/TachographResourceTest.php
tests/Unit/Resources/Additional/TrailerAssignmentsResourceTest.php
tests/Unit/Resources/Additional/WorkOrdersResourceTest.php
tests/Unit/Resources/Beta/BetaResourceTest.php
tests/Unit/Resources/Dispatch/AddressesResourceTest.php
tests/Unit/Resources/Dispatch/RoutesResourceTest.php
tests/Unit/Resources/Fleet/DriversResourceTest.php
tests/Unit/Resources/Fleet/EquipmentResourceTest.php
tests/Unit/Resources/Fleet/TrailersResourceTest.php
tests/Unit/Resources/Fleet/VehiclesResourceTest.php
tests/Unit/Resources/Industrial/AssetsResourceTest.php
tests/Unit/Resources/Industrial/IndustrialResourceTest.php
tests/Unit/Resources/Industrial/SensorsResourceTest.php
tests/Unit/Resources/Integrations/GatewaysResourceTest.php
tests/Unit/Resources/Integrations/LiveSharingLinksResourceTest.php
tests/Unit/Resources/Integrations/WebhooksResourceTest.php
tests/Unit/Resources/Legacy/LegacyResourceTest.php
tests/Unit/Resources/Organization/ContactsResourceTest.php
tests/Unit/Resources/Organization/TagsResourceTest.php
tests/Unit/Resources/Organization/UsersResourceTest.php
tests/Unit/Resources/OwnedDomainCapabilitiesTest.php
tests/Unit/Resources/OwnedGenericOperationContractTest.php
tests/Unit/Resources/Preview/PreviewResourceTest.php
tests/Unit/Resources/ResourceTest.php
tests/Unit/Resources/Safety/CurrentContractResourceTest.php
tests/Unit/Resources/Safety/HoursOfServiceResourceTest.php
tests/Unit/Resources/Safety/MaintenanceResourceTest.php
tests/Unit/Resources/Safety/SafetyEventsResourceTest.php
tests/Unit/Resources/Telematics/TripsResourceTest.php
tests/Unit/Resources/Telematics/VehicleLocationsResourceTest.php
tests/Unit/Resources/Telematics/VehicleStatsResourceTest.php
tests/Unit/SamsaraServiceProviderTest.php
tests/Unit/SamsaraTest.php
PATHS);

$additiveNoAction = listedPaths(<<<'PATHS'
.gitattributes
composer.json
src/Data/Address/Address.php
src/Data/Attribute/Attribute.php
src/Data/Attribute/AttributeValue.php
src/Data/Document/Document.php
src/Data/Document/DocumentType.php
src/Data/Asset/AssetLocation.php
src/Data/Driver/Driver.php
src/Data/Driver/EldSettings.php
src/Data/Equipment/Equipment.php
src/Data/Maintenance/Dvir.php
src/Data/HoursOfService/HosDailyLog.php
src/Data/HoursOfService/HosLog.php
src/Data/Industrial/IndustrialAsset.php
src/Data/Industrial/Machine.php
src/Data/Industrial/VisionCamera.php
src/Data/Industrial/VisionRun.php
src/Data/Maintenance/DefectType.php
src/Data/Maintenance/ServiceTask.php
src/Data/Maintenance/WorkOrder.php
src/Data/Organization/Organization.php
src/Resources/Organization/OrganizationResource.php
src/Data/Safety/CoachingSession.php
src/Data/Safety/SafetyEvent.php
src/Data/Safety/SafetyScore.php
src/Data/Trailer/TrailerStats.php
src/Data/User/UserRole.php
src/Data/Vehicle/Gateway.php
src/Data/Vehicle/VehicleStats.php
src/Enums/WebhookEvent.php
src/Http/QueryString.php
src/Http/RequestExecutor.php
PATHS);

/** @return list<string> */
function guideSections(string $path): array
{
    $resourceSections = [
        'src/Resources/Additional/AlertsResource.php'                     => ['mig-02', 'mig-05', 'mig-06', 'mig-07', 'mig-11', 'mig-16'],
        'src/Resources/Additional/CameraMediaResource.php'                => ['mig-02', 'mig-06', 'mig-12', 'mig-16'],
        'src/Resources/Additional/CarrierProposedAssignmentsResource.php' => ['mig-02', 'mig-15'],
        'src/Resources/Additional/DriverTrailerAssignmentsResource.php'   => ['mig-02', 'mig-05', 'mig-15'],
        'src/Resources/Additional/DriverVehicleAssignmentsResource.php'   => ['mig-02', 'mig-05', 'mig-15'],
        'src/Resources/Additional/FormsResource.php'                      => ['mig-02', 'mig-05', 'mig-07', 'mig-13', 'mig-16'],
        'src/Resources/Additional/FuelAndEnergyResource.php'              => ['mig-02', 'mig-05', 'mig-16'],
        'src/Resources/Additional/HubsResource.php'                       => ['mig-02'],
        'src/Resources/Additional/IdlingResource.php'                     => ['mig-02'],
        'src/Resources/Additional/IftaResource.php'                       => ['mig-02', 'mig-16'],
        'src/Resources/Additional/IssuesResource.php'                     => ['mig-02', 'mig-05'],
        'src/Resources/Additional/RouteEventsResource.php'                => ['mig-02', 'mig-16'],
        'src/Resources/Additional/SettingsResource.php'                   => ['mig-02', 'mig-16'],
        'src/Resources/Additional/SpeedingResource.php'                   => ['mig-02', 'mig-05'],
        'src/Resources/Additional/TachographResource.php'                 => ['mig-02', 'mig-05', 'mig-16'],
        'src/Resources/Additional/TrailerAssignmentsResource.php'         => ['mig-02', 'mig-15'],
        'src/Resources/Additional/WorkOrdersResource.php'                 => ['mig-02', 'mig-05', 'mig-06', 'mig-07', 'mig-15'],
        'src/Resources/Beta/BetaResource.php'                             => ['mig-02', 'mig-05', 'mig-07', 'mig-16', 'mig-17'],
        'src/Resources/Dispatch/AddressesResource.php'                    => ['mig-02'],
        'src/Resources/Dispatch/RoutesResource.php'                       => ['mig-02', 'mig-05', 'mig-07', 'mig-08', 'mig-14'],
        'src/Resources/Fleet/DriversResource.php'                         => ['mig-02', 'mig-10', 'mig-14', 'mig-16'],
        'src/Resources/Fleet/EquipmentResource.php'                       => ['mig-02', 'mig-05', 'mig-07', 'mig-08', 'mig-14'],
        'src/Resources/Fleet/TrailersResource.php'                        => ['mig-02', 'mig-08', 'mig-14'],
        'src/Resources/Fleet/VehiclesResource.php'                        => ['mig-02', 'mig-06', 'mig-08', 'mig-14'],
        'src/Resources/Industrial/AssetsResource.php'                     => ['mig-02', 'mig-05', 'mig-07', 'mig-08', 'mig-14', 'mig-16', 'mig-17'],
        'src/Resources/Industrial/IndustrialResource.php'                 => ['mig-02', 'mig-05', 'mig-07', 'mig-16', 'mig-17'],
        'src/Resources/Industrial/SensorsResource.php'                    => ['mig-02', 'mig-17'],
        'src/Resources/Integrations/GatewaysResource.php'                 => ['mig-02', 'mig-05'],
        'src/Resources/Integrations/LiveSharingLinksResource.php'         => ['mig-02', 'mig-08', 'mig-14', 'mig-16'],
        'src/Resources/Integrations/WebhooksResource.php'                 => ['mig-02', 'mig-08'],
        'src/Resources/Legacy/LegacyResource.php'                         => ['mig-02', 'mig-04', 'mig-25'],
        'src/Resources/Organization/ContactsResource.php'                 => ['mig-02'],
        'src/Resources/Organization/TagsResource.php'                     => ['mig-02', 'mig-08'],
        'src/Resources/Organization/UsersResource.php'                    => ['mig-02', 'mig-08'],
        'src/Resources/Preview/PreviewResource.php'                       => ['mig-02', 'mig-16', 'mig-17'],
        'src/Resources/Resource.php'                                      => ['mig-02', 'mig-03', 'mig-04'],
        'src/Resources/Safety/HoursOfServiceResource.php'                 => ['mig-02', 'mig-05', 'mig-07', 'mig-09', 'mig-15'],
        'src/Resources/Safety/MaintenanceResource.php'                    => ['mig-02', 'mig-06', 'mig-07', 'mig-08', 'mig-15', 'mig-16'],
        'src/Resources/Safety/SafetyEventsResource.php'                   => ['mig-02', 'mig-05', 'mig-07', 'mig-15'],
        'src/Resources/Telematics/TripsResource.php'                      => ['mig-02', 'mig-05', 'mig-07', 'mig-08', 'mig-14'],
        'src/Resources/Telematics/VehicleLocationsResource.php'           => ['mig-02', 'mig-05', 'mig-07'],
        'src/Resources/Telematics/VehicleStatsResource.php'               => ['mig-02', 'mig-05', 'mig-06', 'mig-07', 'mig-14'],
    ];

    if (isset($resourceSections[$path])) {
        return $resourceSections[$path];
    }

    if ($path === 'src/Contracts/QueryBuilderInterface.php') {
        return ['mig-03'];
    }
    if ($path === 'src/Query/Builder.php') {
        return ['mig-04', 'mig-05', 'mig-06', 'mig-07'];
    }
    if ($path === 'src/Exceptions/ValidationException.php') {
        return ['mig-04'];
    }
    if ($path === 'src/Facades/Samsara.php') {
        return ['mig-18'];
    }
    if ($path === 'config/samsara.php') {
        return ['mig-20', 'mig-21'];
    }
    if ($path === 'src/Samsara.php') {
        return ['mig-20', 'mig-21', 'mig-22', 'mig-23'];
    }
    if ($path === 'src/SamsaraServiceProvider.php') {
        return ['mig-20', 'mig-21', 'mig-22', 'mig-23', 'mig-24'];
    }
    if ($path === 'src/Enums/DutyStatus.php') {
        return ['mig-09'];
    }
    if (in_array($path, ['src/Enums/SafetyEventType.php', 'src/Enums/VehicleStatType.php'], true)) {
        return ['mig-08'];
    }
    if ($path === 'src/Http/ResponseDecoder.php') {
        return ['mig-21'];
    }
    if ($path === 'src/Webhooks/VerifyWebhookSignature.php') {
        return ['mig-24'];
    }
    if (in_array($path, [
        'src/Concerns/InteractsWithTime.php',
        'src/Data/EntityCollection.php',
        'src/Query/Pagination/CursorPaginator.php',
    ], true)) {
        return ['mig-19'];
    }
    if (in_array($path, [
        'src/Exceptions/InvalidSignatureException.php',
        'src/Webhooks/WebhookSignatureVerifier.php',
    ], true)) {
        return ['mig-19', 'mig-24'];
    }
    if (str_starts_with($path, 'src/Data/Alert/')) {
        return ['mig-07', 'mig-08', 'mig-11'];
    }
    if (str_starts_with($path, 'src/Data/Asset/')) {
        return ['mig-07', 'mig-08', 'mig-17'];
    }
    if (str_starts_with($path, 'src/Data/Driver/')) {
        return ['mig-10'];
    }
    if (str_starts_with($path, 'src/Data/Equipment/')) {
        return ['mig-07', 'mig-08'];
    }
    if (str_starts_with($path, 'src/Data/Form/')) {
        return ['mig-07', 'mig-08', 'mig-13'];
    }
    if (str_starts_with($path, 'src/Data/HoursOfService/')) {
        return ['mig-07', 'mig-09', 'mig-15'];
    }
    if (str_starts_with($path, 'src/Data/Industrial/')) {
        return ['mig-07', 'mig-17'];
    }
    if ($path === 'src/Data/LiveShare/LiveShare.php') {
        return ['mig-08', 'mig-14', 'mig-16'];
    }
    if (str_starts_with($path, 'src/Data/Maintenance/')) {
        return ['mig-07', 'mig-08', 'mig-15'];
    }
    if (str_starts_with($path, 'src/Data/Route/')) {
        return ['mig-07', 'mig-08', 'mig-14'];
    }
    if (str_starts_with($path, 'src/Data/Safety/')) {
        return ['mig-07', 'mig-15'];
    }
    if ($path === 'src/Data/Tag/Tag.php') {
        return ['mig-08'];
    }
    if ($path === 'src/Data/Trailer/Trailer.php') {
        return ['mig-08', 'mig-14', 'mig-15'];
    }
    if ($path === 'src/Data/Trip/Trip.php') {
        return ['mig-08', 'mig-14'];
    }
    if ($path === 'src/Data/User/User.php' || $path === 'src/Data/Webhook/Webhook.php') {
        return ['mig-08'];
    }
    if (str_starts_with($path, 'src/Data/Vehicle/')) {
        return ['mig-07', 'mig-08', 'mig-14'];
    }
    if (str_contains($path, '/AlertsResource.php')) {
        return ['mig-06', 'mig-11', 'mig-16'];
    }
    if (str_contains($path, '/CameraMediaResource.php')) {
        return ['mig-06', 'mig-12', 'mig-16'];
    }
    if (str_contains($path, '/FormsResource.php')) {
        return ['mig-13', 'mig-16'];
    }
    if (str_contains($path, '/DriversResource.php')) {
        return ['mig-10', 'mig-14', 'mig-16'];
    }
    if (str_contains($path, '/BetaResource.php') || str_contains($path, '/PreviewResource.php')) {
        return ['mig-16', 'mig-17'];
    }
    if (str_contains($path, '/Industrial/') || str_contains($path, '/AssetsResource.php') || str_contains($path, '/SensorsResource.php')) {
        return ['mig-14', 'mig-16', 'mig-17'];
    }
    if (str_contains($path, '/MaintenanceResource.php') || str_contains($path, '/SafetyEventsResource.php') || str_contains($path, '/HoursOfServiceResource.php')) {
        return ['mig-07', 'mig-15', 'mig-16'];
    }
    if (str_contains($path, '/TripsResource.php') || str_contains($path, '/VehicleLocationsResource.php') || str_contains($path, '/VehicleStatsResource.php')) {
        return ['mig-05', 'mig-07', 'mig-14'];
    }
    if (str_contains($path, '/FuelAndEnergyResource.php') || str_contains($path, '/IftaResource.php') || str_contains($path, '/SettingsResource.php') || str_contains($path, '/RouteEventsResource.php') || str_contains($path, '/TachographResource.php')) {
        return ['mig-16'];
    }
    if (str_contains($path, 'AssignmentsResource.php') || str_contains($path, '/WorkOrdersResource.php')) {
        return ['mig-05', 'mig-06', 'mig-15'];
    }
    if (str_contains($path, '/RoutesResource.php') || str_contains($path, '/TrailersResource.php') || str_contains($path, '/VehiclesResource.php') || str_contains($path, '/EquipmentResource.php') || str_contains($path, '/LiveSharingLinksResource.php')) {
        return ['mig-02', 'mig-07', 'mig-14'];
    }

    return ['mig-02', 'mig-05', 'mig-06'];
}

function actionFor(string $path): string
{
    if (str_starts_with($path, 'src/Data/') || str_starts_with($path, 'src/Enums/')) {
        return 'Audit imports, field reads, helper calls, serialization, fixtures, nullable cases, and endpoint-specific hydration against the linked DTO migration sections.';
    }
    if (str_starts_with($path, 'src/Resources/')) {
        return 'Audit every consumer call to this resource for capability, signature, HTTP method/path/query/body, return class, pagination, and exact HTTP-fake expectations.';
    }
    if ($path === 'src/Contracts/QueryBuilderInterface.php') {
        return 'Add repeatQueryParameters(string ...$parameters): static to every third-party implementation and verify interface conformance.';
    }
    if ($path === 'src/Query/Builder.php') {
        return 'Update pagination, query encoding, exception, and endpoint-DTO expectations for builder terminals.';
    }
    if ($path === 'src/Exceptions/ValidationException.php') {
        return 'Catch the SDK exception hierarchy and treat both HTTP 400 and 422 as ValidationException.';
    }
    if ($path === 'src/Facades/Samsara.php') {
        return 'Remove redundant manual fake facade/container swaps and verify all aliases resolve the same fake.';
    }

    return 'Apply the linked mandatory migration and its focused verification steps.';
}

function noActionReason(string $path, string $disposition): string
{
    if ($path === 'composer.json') {
        return 'Adds maintainer-only validation and memory-limit settings; it does not change the installed SDK runtime or require consumer migration.';
    }

    return match ($disposition) {
        'supporting_evidence' => str_starts_with($path, 'tests/')
            ? 'Executable regression evidence for one or more documented migrations; it does not add a separate consumer action.'
            : 'Contract, audit, validation, CI, or formatting evidence for the documented migration; it does not add a separate consumer action.',
        'additive_no_action' => 'Additive configuration, infrastructure, annotation, or DTO/helper behavior; optional adoption only unless an existing operation migration is linked elsewhere.',
        'internal_only'      => 'Repository ignore hygiene only; no shipped package or consumer behavior.',
        'documentation_only' => 'Reference or release documentation only; current behavior is incorporated or linked by the canonical upgrade guide.',
        default              => '',
    };
}

/** @return array{content_state: 'deleted'|'excluded'|'present', content_sha256: ?string} */
function contentIdentity(string $path): array
{
    if ($path === 'docs/api-sync/v2-upgrade-disposition.json') {
        return ['content_state' => 'excluded', 'content_sha256' => null];
    }

    if (! is_file(__DIR__.'/../'.$path)) {
        return ['content_state' => 'deleted', 'content_sha256' => null];
    }

    $hash = hash_file('sha256', __DIR__.'/../'.$path);
    if ($hash === false) {
        throw new RuntimeException("Unable to hash release path: {$path}.");
    }

    return ['content_state' => 'present', 'content_sha256' => $hash];
}

function isDocumentationOnlyPath(string $path): bool
{
    return str_ends_with($path, '.md')
        || str_starts_with($path, 'docs/')
        || str_starts_with($path, 'resources/boost/guidelines/')
        || str_starts_with($path, 'resources/boost/skills/');
}

$baselineOutput = [];
$baselineStatus = 0;
$ignoredAncestorOutput = [];
$ancestorStatus = 0;
exec(sprintf('git merge-base --is-ancestor %s HEAD', escapeshellarg(AUDITED_HEAD)), $ignoredAncestorOutput, $ancestorStatus);
if ($ancestorStatus !== 0) {
    fwrite(STDERR, "FAIL: audited head is not an ancestor of the current HEAD.\n");
    exit(1);
}

exec(sprintf('git diff --name-only %s..%s', escapeshellarg(BASELINE_TAG), escapeshellarg(AUDITED_HEAD)), $baselineOutput, $baselineStatus);
if ($baselineStatus !== 0) {
    fwrite(STDERR, "FAIL: unable to read the audited Git baseline.\n");
    exit(1);
}

$baseline = array_values(array_filter(array_map('trim', $baselineOutput)));
sort($baseline);

$publicationArtifacts = [
    '.gitattributes',
    'composer.json',
    'UPGRADE.md',
    'docs/api-sync/v2-upgrade-disposition.json',
    'docs/upgrade-guide-v2.md',
    'scripts/check-v2-upgrade-guide.php',
    '.ai/skills/samsara-sdk-development/SKILL.md',
    '.ai/skills/samsara-sdk-maintenance/SKILL.md',
    'CLAUDE.md',
    'resources/boost/guidelines/core.blade.php',
    'resources/boost/guidelines/query-builder.blade.php',
    'resources/boost/guidelines/resources.blade.php',
    'resources/boost/guidelines/testing.blade.php',
    'resources/boost/skills/samsara-sdk-development/SKILL.md',
    'resources/boost/skills/samsara-sdk-development/references/integration.md',
    'resources/boost/skills/samsara-sdk-development/references/querying-and-responses.md',
    'resources/boost/skills/samsara-sdk-development/references/testing-and-webhooks.md',
    'resources/boost/skills/upgrade-samsara-sdk-v2/SKILL.md',
    'tests/Unit/BoostDocumentationTest.php',
];
$implementationArtifacts = [
    'src/Http/ResponseDecoder.php',
    'src/Resources/Organization/OrganizationResource.php',
];
$finalScopeOutput = [];
$finalScopeStatus = 0;
exec(sprintf('git diff --name-only %s', escapeshellarg(BASELINE_TAG)), $finalScopeOutput, $finalScopeStatus);
$finalWorkspaceScope = array_values(array_unique(array_merge(
    array_filter(array_map('trim', $finalScopeOutput)),
    array_filter($publicationArtifacts, fn (string $path): bool => is_file(__DIR__.'/../'.$path)),
    array_filter($implementationArtifacts, fn (string $path): bool => is_file(__DIR__.'/../'.$path)),
)));
sort($finalWorkspaceScope);
if ($finalScopeStatus !== 0) {
    fwrite(STDERR, "FAIL: unable to read the final tag-to-worktree release scope.\n");
    exit(1);
}
$releaseScope = $finalWorkspaceScope;

$known = array_merge($upgradeActions, $supportingEvidence, $additiveNoAction, ['docs/.gitignore']);
$duplicates = array_diff_assoc($known, array_unique($known));
$unknownExplicit = array_values(array_diff($known, $releaseScope));
$documentationOnly = array_values(array_diff($releaseScope, $known));

if ($duplicates !== [] || $unknownExplicit !== []) {
    fwrite(STDERR, 'FAIL: classifier has duplicate or non-baseline explicit paths: '.json_encode([
        'duplicates' => array_values($duplicates),
        'unknown'    => $unknownExplicit,
    ], JSON_UNESCAPED_SLASHES)."\n");
    exit(1);
}

$records = [];
foreach ($releaseScope as $path) {
    $disposition = match (true) {
        in_array($path, $upgradeActions, true)     => 'upgrade_action',
        in_array($path, $supportingEvidence, true) => 'supporting_evidence',
        in_array($path, $additiveNoAction, true)   => 'additive_no_action',
        str_starts_with($path, 'tests/'),
        str_starts_with($path, 'src/Testing/') => 'supporting_evidence',
        $path === 'docs/.gitignore'            => 'internal_only',
        isDocumentationOnlyPath($path)         => 'documentation_only',
        default                                => throw new RuntimeException(
            "Release path requires an explicit disposition: {$path}."
        ),
    };

    $identity = contentIdentity($path);

    $records[] = [
        'path' => $path,
        ...$identity,
        'disposition'    => $disposition,
        'guide_sections' => $disposition === 'upgrade_action'
            ? guideSections($path)
            : ($disposition === 'supporting_evidence' ? ['package-maintainer-evidence'] : []),
        'consumer_action'  => $disposition === 'upgrade_action' ? actionFor($path) : null,
        'no_action_reason' => $disposition === 'upgrade_action' ? null : noActionReason($path, $disposition),
    ];
}

$counts = array_count_values(array_column($records, 'disposition'));
ksort($counts);

$ledger = [
    'schema_version' => 1,
    'baseline'       => [
        'tag'           => BASELINE_TAG,
        'audited_head'  => AUDITED_HEAD,
        'comparison'    => BASELINE_TAG.'..'.AUDITED_HEAD,
        'changed_files' => count($baseline),
    ],
    'release_scope' => [
        'comparison'                                        => BASELINE_TAG.'..final-dev',
        'changed_files'                                     => count($releaseScope),
        'content_digest_exclusions'                         => ['docs/api-sync/v2-upgrade-disposition.json'],
        'publication_artifacts_added_after_audited_head'    => $publicationArtifacts,
        'implementation_artifacts_added_after_audited_head' => $implementationArtifacts,
    ],
    'summary' => $counts,
    'records' => $records,
];

$expectedCounts = [
    'additive_no_action'  => 33,
    'documentation_only'  => 68,
    'internal_only'       => 1,
    'supporting_evidence' => 150,
    'upgrade_action'      => 98,
];

if ($counts !== $expectedCounts || count($baseline) !== 239 || count($releaseScope) !== 350) {
    fwrite(STDERR, 'FAIL: audited disposition counts drifted: '.json_encode($counts)."\n");
    exit(1);
}

if (in_array('--write', $argv, true)) {
    file_put_contents(LEDGER_PATH, json_encode($ledger, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");
    fwrite(STDOUT, 'WROTE: '.LEDGER_PATH."\n");
    exit(0);
}

if (! is_file(LEDGER_PATH) || ! is_file(GUIDE_PATH)) {
    fwrite(STDERR, "FAIL: upgrade guide or disposition ledger is missing.\n");
    exit(1);
}

$actualLedger = json_decode((string) file_get_contents(LEDGER_PATH), true, flags: JSON_THROW_ON_ERROR);
if ($actualLedger !== $ledger) {
    fwrite(STDERR, "FAIL: disposition ledger is stale; run composer check:v2-upgrade-guide -- --write and review the diff.\n");
    exit(1);
}

$guide = (string) file_get_contents(GUIDE_PATH);
$referencedSections = array_values(array_unique(array_merge(...array_column($records, 'guide_sections'))));
$sectionEvidence = [
    'mig-02'                      => ['UnsupportedOperationException', 'capability matrix'],
    'mig-03'                      => ['QueryBuilderInterface', 'supportedGenericOperations'],
    'mig-04'                      => ['SamsaraException', 'HTTP 400 and 422'],
    'mig-05'                      => ['withoutDefaultPageSize()', 'configured `per_page`'],
    'mig-06'                      => ["whereDriver('driver-1')", 'repeated query keys'],
    'mig-07'                      => ['getLocation()', 'HosDailyLog'],
    'mig-08'                      => ['Removed public helpers', 'isTriggered()', 'Dynamic DTO payload fields'],
    'mig-09'                      => ['sleeperBed', 'sleeperBerth'],
    'mig-10'                      => ['createAuthToken', 'deleteQrCode', "'driverId' => 123", 'Preview auth-token endpoint uses `id`'],
    'mig-11'                      => ['/alerts/incidents/stream', 'deleteConfigurations', 'flat `type`, `threshold`, and `tagIds`', 'requires `startTime`'],
    'mig-12'                      => ['getRetrieval', '/cameras/media/retrieval', "\$created['retrievalId']", "\$media['urlInfo']['url']"],
    'mig-13'                      => ['getPdfExport', '/form-submissions/pdf-exports', 'formTemplateId', "\$export['pdfId']", 'jobStatus'],
    'mig-14'                      => ['findByExternalId', 'Equipment external lookup'],
    'mig-15'                      => ['trailerAssignments()', 'HosClock'],
    'mig-16'                      => ['/fuel-purchase', 'fuelQuantityLiters', 'startHour', 'files[*].downloadUrl'],
    'mig-17'                      => ['createIndustrialJob', "'data' => [[", 'createDriverAuthToken(string $driverId, array $data = [])'],
    'mig-18'                      => ['same fake', 'Samsara::fake()'],
    'mig-19'                      => ['Compatibility corrections', 'non-mutating', 'nextPage()', 'InvalidSignatureException'],
    'mig-20'                      => ['SAMSARA_RETRY', 'SAMSARA_ATTEMPTS', "getConfig('attempts')"],
    'mig-21'                      => ['retry_after_max_seconds', 'Valid `{"data":[...]}` responses are unchanged'],
    'mig-22'                      => ["Laravel's shared HTTP factory", 'Standalone construction still works'],
    'mig-23'                      => ['withToken()', 'withoutToken()', 'Samsara::make()'],
    'mig-24'                      => ['samsara.webhook', 'future timestamps', 'context.expected'],
    'mig-25'                      => ['sendMessage', 'dispatchRoutes()', 'Eight legacy helpers'],
    'package-maintainer-evidence' => ['Contract Drift', 'v2 disposition ledger'],
];
foreach ($referencedSections as $section) {
    $marker = '<a id="'.$section.'"></a>';
    $start = strpos($guide, $marker);
    if ($start === false) {
        fwrite(STDERR, "FAIL: guide is missing explicit section anchor {$section}.\n");
        exit(1);
    }
    $bodyStart = $start + strlen($marker);
    $next = strpos($guide, '<a id="', $bodyStart);
    $body = substr($guide, $bodyStart, $next === false ? null : $next - $bodyStart);
    if (strlen($body) < 200) {
        fwrite(STDERR, "FAIL: guide section {$section} is too short to be actionable.\n");
        exit(1);
    }
    foreach ($sectionEvidence[$section] ?? [] as $token) {
        if (! str_contains($body, $token)) {
            fwrite(STDERR, "FAIL: guide section {$section} is missing scoped evidence: {$token}.\n");
            exit(1);
        }
    }
}

foreach ($records as $record) {
    if ($record['disposition'] !== 'upgrade_action' || $record['guide_sections'] === []) {
        if ($record['disposition'] === 'upgrade_action') {
            fwrite(STDERR, "FAIL: upgrade-action path has no guide section: {$record['path']}.\n");
            exit(1);
        }

        continue;
    }
    foreach ($record['guide_sections'] as $section) {
        if (! array_key_exists($section, $sectionEvidence)) {
            fwrite(STDERR, "FAIL: upgrade-action path {$record['path']} links to an unvalidated guide section {$section}.\n");
            exit(1);
        }
    }
}

$requiredGuideTokens = [
    'QueryBuilderInterface',
    'UnsupportedOperationException',
    'SamsaraException',
    'ValidationException',
    'withoutDefaultPageSize()',
    'repeatQueryParameters',
    "return ['all', 'create'];",
    'sleeperBed',
    'getLocation()',
    'getLocations()',
    'getLogs()',
    'HosDailyLog',
    'getSafetyEvent()',
    'DataPoint',
    'SafetyEventType',
    'VehicleStatType',
    'startDate',
    'endDate',
    'startHour',
    'endHour',
    '/alerts/incidents/stream',
    '/cameras/media',
    '/form-submissions/pdf-exports',
    '/fuel-purchase',
    '/fleet/reports/ifta/',
    '/fleet/settings/',
    '/route-events/stream',
    '/industrial/data-inputs/data-points',
    'Agent completion record',
    'Rollback procedure',
    'SAMSARA_ATTEMPTS',
];

foreach ($requiredGuideTokens as $token) {
    if (! str_contains($guide, $token)) {
        fwrite(STDERR, "FAIL: guide is missing required migration evidence: {$token}.\n");
        exit(1);
    }
}

$forbiddenGuideTokens = [
    'Samsara\\Exceptions\\ApiException',
    'self::OPERATION_ALL',
    'SDK wraps the supplied fields',
];

foreach ($forbiddenGuideTokens as $token) {
    if (str_contains($guide, $token)) {
        fwrite(STDERR, "FAIL: guide contains a known invalid migration instruction: {$token}.\n");
        exit(1);
    }
}

$actionCount = $counts['upgrade_action'] ?? 0;
$noActionCount = count($releaseScope) - $actionCount;
fwrite(STDOUT, sprintf(
    'PASS: %1$d/%1$d final release paths have one disposition (%2$d frozen implementation-baseline paths plus integrated quality and publication paths); %3$d upgrade actions link to canonical guide sections; %4$d no-action/evidence records have explicit rationale.'."\n",
    count($releaseScope),
    count($baseline),
    $actionCount,
    $noActionCount,
));
