<?php

namespace Samsara\Tests\Unit\Resources;

use ReflectionClass;
use Samsara\Samsara;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Fleet\DriversResource;
use Samsara\Resources\Fleet\TrailersResource;
use Samsara\Resources\Fleet\VehiclesResource;
use PHPUnit\Framework\Attributes\DataProvider;
use Samsara\Resources\Dispatch\RoutesResource;
use Samsara\Resources\Fleet\EquipmentResource;
use Samsara\Resources\Telematics\TripsResource;
use Samsara\Resources\Organization\TagsResource;
use Samsara\Resources\Dispatch\AddressesResource;
use Samsara\Resources\Organization\UsersResource;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Integrations\GatewaysResource;
use Samsara\Resources\Integrations\WebhooksResource;
use Samsara\Resources\Organization\ContactsResource;
use Samsara\Exceptions\UnsupportedOperationException;
use Samsara\Resources\Telematics\VehicleStatsResource;
use Samsara\Resources\Telematics\VehicleLocationsResource;
use Samsara\Resources\Integrations\LiveSharingLinksResource;

class OwnedDomainCapabilitiesTest extends TestCase
{
    /**
     * @param  class-string  $resourceClass
     * @param  array<int, string>  $expectedOperations
     */
    #[Test]
    #[DataProvider('resourceCapabilities')]
    public function it_declares_only_contract_supported_inherited_operations(
        string $resourceClass,
        array $expectedOperations
    ): void {
        $method = (new ReflectionClass($resourceClass))->getMethod('supportedGenericOperations');
        $resource = new $resourceClass(new Samsara('test-token'));

        $this->assertSame($resourceClass, $method->getDeclaringClass()->getName());
        $this->assertSame($expectedOperations, $method->invoke($resource));
    }

    /**
     * @param  class-string  $resourceClass
     */
    #[Test]
    #[DataProvider('unsupportedGenericOperations')]
    public function it_rejects_each_unsupported_inherited_operation_before_http(
        string $resourceClass,
        string $operation
    ): void {
        $http = new HttpFactory;
        $samsara = new Samsara('test-token');
        $samsara->setHttpFactory($http);
        $http->fake();
        $resource = new $resourceClass($samsara);

        try {
            match ($operation) {
                'all'    => $resource->all(),
                'find'   => $resource->find('entity-id'),
                'create' => $resource->create(['name' => 'Unsupported']),
                'update' => $resource->update('entity-id', ['name' => 'Unsupported']),
                'delete' => $resource->delete('entity-id'),
            };
            $this->fail("Expected {$resourceClass}::{$operation} to be rejected.");
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame($resourceClass, $exception->getContext()['resource']);
            $this->assertSame($operation, $exception->getContext()['operation']);
            $this->assertStringStartsWith($resource->getEndpoint(), $exception->getContext()['endpoint']);
        }

        $http->assertNothingSent();
    }

    /**
     * @return array<string, array{class-string, array<int, string>}>
     */
    public static function resourceCapabilities(): array
    {
        return [
            'addresses'         => [AddressesResource::class, ['all', 'find', 'create', 'update', 'delete']],
            'routes'            => [RoutesResource::class, ['find', 'create', 'update', 'delete']],
            'drivers'           => [DriversResource::class, ['all', 'find', 'create', 'update']],
            'equipment'         => [EquipmentResource::class, ['all', 'find']],
            'trailers'          => [TrailersResource::class, ['all', 'find', 'create', 'update', 'delete']],
            'vehicles'          => [VehiclesResource::class, ['all', 'find', 'update']],
            'gateways'          => [GatewaysResource::class, ['all', 'create', 'delete']],
            'live shares'       => [LiveSharingLinksResource::class, ['all', 'create', 'update', 'delete']],
            'webhooks'          => [WebhooksResource::class, ['all', 'find', 'create', 'update', 'delete']],
            'contacts'          => [ContactsResource::class, ['all', 'find', 'create', 'update', 'delete']],
            'tags'              => [TagsResource::class, ['all', 'find', 'create', 'update', 'delete']],
            'users'             => [UsersResource::class, ['all', 'find', 'create', 'update', 'delete']],
            'trips'             => [TripsResource::class, []],
            'vehicle locations' => [VehicleLocationsResource::class, ['all']],
            'vehicle stats'     => [VehicleStatsResource::class, []],
        ];
    }

    /**
     * @return array<string, array{class-string, string}>
     */
    public static function unsupportedGenericOperations(): array
    {
        $cases = [];

        foreach (self::resourceCapabilities() as $resourceName => [$resourceClass, $supportedOperations]) {
            foreach (['all', 'find', 'create', 'update', 'delete'] as $operation) {
                if (! in_array($operation, $supportedOperations, true)) {
                    $cases["{$resourceName} {$operation}"] = [$resourceClass, $operation];
                }
            }
        }

        return $cases;
    }
}
