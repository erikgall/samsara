<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use ReflectionMethod;
use Samsara\Tests\TestCase;
use Samsara\Resources\Resource;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Samsara\Resources\Additional\HubsResource;
use Samsara\Resources\Additional\IftaResource;
use Samsara\Resources\Additional\FormsResource;
use Samsara\Resources\Additional\AlertsResource;
use Samsara\Resources\Additional\IdlingResource;
use Samsara\Resources\Additional\IssuesResource;
use Samsara\Resources\Safety\MaintenanceResource;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Additional\SettingsResource;
use Samsara\Resources\Additional\SpeedingResource;
use Samsara\Resources\Safety\SafetyEventsResource;
use Samsara\Resources\Additional\TachographResource;
use Samsara\Resources\Additional\WorkOrdersResource;
use Samsara\Resources\Safety\HoursOfServiceResource;
use Samsara\Exceptions\UnsupportedOperationException;
use Samsara\Resources\Additional\CameraMediaResource;
use Samsara\Resources\Additional\RouteEventsResource;
use Samsara\Resources\Additional\FuelAndEnergyResource;
use Samsara\Resources\Additional\TrailerAssignmentsResource;
use Samsara\Resources\Additional\DriverTrailerAssignmentsResource;
use Samsara\Resources\Additional\DriverVehicleAssignmentsResource;
use Samsara\Resources\Additional\CarrierProposedAssignmentsResource;

/**
 * Exact inherited generic-operation capabilities for every owned resource.
 */
class GenericCapabilitiesTest extends TestCase
{
    #[Test]
    #[DataProvider('resourceCapabilities')]
    public function every_resource_declares_only_contract_exact_generic_operations(
        string $resourceClass,
        array $expected
    ): void {
        $method = new ReflectionMethod($resourceClass, 'supportedGenericOperations');
        $resource = new $resourceClass(new Samsara('test-token'));

        $this->assertSame($resourceClass, $method->getDeclaringClass()->getName());
        $this->assertSame($expected, $method->invoke($resource));
    }

    #[Test]
    #[DataProvider('resourceCapabilities')]
    public function unsupported_find_operations_stop_before_http(
        string $resourceClass,
        array $expected
    ): void {
        $this->assertNotContains('find', $expected);
        $http = new HttpFactory;
        $http->fake();
        $samsara = new Samsara('test-token');
        $samsara->setHttpFactory($http);

        try {
            /** @var resource $resource */
            $resource = new $resourceClass($samsara);
            $resource->find('unsupported-id');
            $this->fail('Expected the generic find operation to be rejected.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame('find', $exception->getContext()['operation']);
            $this->assertSame($resourceClass, $exception->getContext()['resource']);
        }

        $http->assertNothingSent();
    }

    /**
     * @return array<string, array{class-string<resource>, array<int, string>}>
     */
    public static function resourceCapabilities(): array
    {
        return [
            'alerts'                       => [AlertsResource::class, ['all', 'create']],
            'camera media'                 => [CameraMediaResource::class, []],
            'carrier proposed assignments' => [CarrierProposedAssignmentsResource::class, ['all', 'create', 'delete']],
            'driver trailer assignments'   => [DriverTrailerAssignmentsResource::class, ['create']],
            'driver vehicle assignments'   => [DriverVehicleAssignmentsResource::class, ['create']],
            'forms'                        => [FormsResource::class, ['create']],
            'fuel and energy'              => [FuelAndEnergyResource::class, []],
            'hubs'                         => [HubsResource::class, ['all']],
            'idling'                       => [IdlingResource::class, []],
            'ifta'                         => [IftaResource::class, []],
            'issues'                       => [IssuesResource::class, ['create']],
            'route events'                 => [RouteEventsResource::class, ['all']],
            'settings'                     => [SettingsResource::class, []],
            'speeding'                     => [SpeedingResource::class, []],
            'tachograph'                   => [TachographResource::class, []],
            'trailer assignments'          => [TrailerAssignmentsResource::class, []],
            'work orders'                  => [WorkOrdersResource::class, ['all', 'create']],
            'hours of service'             => [HoursOfServiceResource::class, ['all']],
            'maintenance'                  => [MaintenanceResource::class, []],
            'safety events'                => [SafetyEventsResource::class, []],
        ];
    }
}
