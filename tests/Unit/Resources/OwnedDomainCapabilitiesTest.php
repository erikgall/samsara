<?php

namespace Samsara\Tests\Unit\Resources;

use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Contracts\ResourceInterface;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @author Erik Galloway <erik@erikgall.com>
 */
class OwnedDomainCapabilitiesTest extends TestCase
{
    /**
     * @param  class-string  $resourceClass
     * @param  list<string>  $operations
     */
    #[Test]
    #[DataProvider('resourceCapabilities')]
    public function it_declares_only_supported_generic_operations(string $resourceClass, array $operations): void
    {
        foreach (['all', 'find', 'create', 'update', 'delete'] as $operation) {
            $this->assertSame(in_array($operation, $operations, true), method_exists($resourceClass, $operation), $resourceClass.'::'.$operation);
        }
    }

    #[Test]
    public function it_removes_the_partial_resource_interface(): void
    {
        $this->assertFalse(interface_exists(ResourceInterface::class));
    }

    /**
     * @return array<string, array{class-string, list<string>}>
     */
    public static function resourceCapabilities(): array
    {
        return [
            'AlertsResource'                     => [\Samsara\Resources\Additional\AlertsResource::class, ['create']],
            'CameraMediaResource'                => [\Samsara\Resources\Additional\CameraMediaResource::class, []],
            'CarrierProposedAssignmentsResource' => [\Samsara\Resources\Additional\CarrierProposedAssignmentsResource::class, ['create', 'delete']],
            'DriverTrailerAssignmentsResource'   => [\Samsara\Resources\Additional\DriverTrailerAssignmentsResource::class, ['create']],
            'DriverVehicleAssignmentsResource'   => [\Samsara\Resources\Additional\DriverVehicleAssignmentsResource::class, ['create']],
            'FormsResource'                      => [\Samsara\Resources\Additional\FormsResource::class, ['create']],
            'FuelAndEnergyResource'              => [\Samsara\Resources\Additional\FuelAndEnergyResource::class, []],
            'HubsResource'                       => [\Samsara\Resources\Additional\HubsResource::class, []],
            'IdlingResource'                     => [\Samsara\Resources\Additional\IdlingResource::class, []],
            'IftaResource'                       => [\Samsara\Resources\Additional\IftaResource::class, []],
            'IssuesResource'                     => [\Samsara\Resources\Additional\IssuesResource::class, ['create']],
            'RouteEventsResource'                => [\Samsara\Resources\Additional\RouteEventsResource::class, []],
            'SettingsResource'                   => [\Samsara\Resources\Additional\SettingsResource::class, []],
            'SpeedingResource'                   => [\Samsara\Resources\Additional\SpeedingResource::class, []],
            'TachographResource'                 => [\Samsara\Resources\Additional\TachographResource::class, []],
            'TrailerAssignmentsResource'         => [\Samsara\Resources\Additional\TrailerAssignmentsResource::class, []],
            'WorkOrdersResource'                 => [\Samsara\Resources\Additional\WorkOrdersResource::class, ['create']],
            'BetaResource'                       => [\Samsara\Resources\Beta\BetaResource::class, []],
            'AddressesResource'                  => [\Samsara\Resources\Dispatch\AddressesResource::class, ['find', 'create', 'update', 'delete']],
            'RoutesResource'                     => [\Samsara\Resources\Dispatch\RoutesResource::class, ['find', 'create', 'update', 'delete']],
            'DriversResource'                    => [\Samsara\Resources\Fleet\DriversResource::class, ['find', 'create', 'update']],
            'EquipmentResource'                  => [\Samsara\Resources\Fleet\EquipmentResource::class, ['find']],
            'TrailersResource'                   => [\Samsara\Resources\Fleet\TrailersResource::class, ['find', 'create', 'update', 'delete']],
            'VehiclesResource'                   => [\Samsara\Resources\Fleet\VehiclesResource::class, ['find', 'update']],
            'AssetsResource'                     => [\Samsara\Resources\Industrial\AssetsResource::class, ['create', 'update', 'delete']],
            'IndustrialResource'                 => [\Samsara\Resources\Industrial\IndustrialResource::class, ['create', 'update', 'delete']],
            'SensorsResource'                    => [\Samsara\Resources\Industrial\SensorsResource::class, []],
            'GatewaysResource'                   => [\Samsara\Resources\Integrations\GatewaysResource::class, ['create', 'delete']],
            'LiveSharingLinksResource'           => [\Samsara\Resources\Integrations\LiveSharingLinksResource::class, ['create', 'update', 'delete']],
            'WebhooksResource'                   => [\Samsara\Resources\Integrations\WebhooksResource::class, ['find', 'create', 'update', 'delete']],
            'LegacyResource'                     => [\Samsara\Resources\Legacy\LegacyResource::class, []],
            'ContactsResource'                   => [\Samsara\Resources\Organization\ContactsResource::class, ['find', 'create', 'update', 'delete']],
            'OrganizationResource'               => [\Samsara\Resources\Organization\OrganizationResource::class, []],
            'TagsResource'                       => [\Samsara\Resources\Organization\TagsResource::class, ['find', 'create', 'update', 'delete']],
            'UsersResource'                      => [\Samsara\Resources\Organization\UsersResource::class, ['find', 'create', 'update', 'delete']],
            'PreviewResource'                    => [\Samsara\Resources\Preview\PreviewResource::class, []],
            'HoursOfServiceResource'             => [\Samsara\Resources\Safety\HoursOfServiceResource::class, []],
            'MaintenanceResource'                => [\Samsara\Resources\Safety\MaintenanceResource::class, []],
            'SafetyEventsResource'               => [\Samsara\Resources\Safety\SafetyEventsResource::class, []],
            'TripsResource'                      => [\Samsara\Resources\Telematics\TripsResource::class, []],
            'VehicleLocationsResource'           => [\Samsara\Resources\Telematics\VehicleLocationsResource::class, []],
            'VehicleStatsResource'               => [\Samsara\Resources\Telematics\VehicleStatsResource::class, []],
        ];
    }
}
