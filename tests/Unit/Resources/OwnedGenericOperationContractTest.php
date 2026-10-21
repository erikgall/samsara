<?php

namespace Samsara\Tests\Unit\Resources;

use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Data\Tag\Tag;
use Samsara\Data\User\User;
use Samsara\Tests\TestCase;
use Samsara\Data\Route\Route;
use Samsara\Data\Driver\Driver;
use Samsara\Data\Address\Address;
use Samsara\Data\Contact\Contact;
use Samsara\Data\Trailer\Trailer;
use Samsara\Data\Vehicle\Gateway;
use Samsara\Data\Vehicle\Vehicle;
use Samsara\Data\Webhook\Webhook;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Request;
use Samsara\Data\Equipment\Equipment;
use Samsara\Data\LiveShare\LiveShare;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Fleet\DriversResource;
use Samsara\Resources\Fleet\TrailersResource;
use Samsara\Resources\Fleet\VehiclesResource;
use PHPUnit\Framework\Attributes\DataProvider;
use Samsara\Resources\Dispatch\RoutesResource;
use Samsara\Resources\Fleet\EquipmentResource;
use Samsara\Data\Vehicle\VehicleLocationResult;
use Samsara\Resources\Organization\TagsResource;
use Samsara\Resources\Dispatch\AddressesResource;
use Samsara\Resources\Organization\UsersResource;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Integrations\GatewaysResource;
use Samsara\Resources\Integrations\WebhooksResource;
use Samsara\Resources\Organization\ContactsResource;
use Samsara\Resources\Telematics\VehicleLocationsResource;
use Samsara\Resources\Integrations\LiveSharingLinksResource;

class OwnedGenericOperationContractTest extends TestCase
{
    /**
     * @param  class-string  $resourceClass
     * @param  class-string<Entity>  $entityClass
     */
    #[Test]
    #[DataProvider('genericOperations')]
    public function it_sends_supported_operations_to_the_exact_contract_endpoint(
        string $resourceClass,
        string $entityClass,
        string $endpoint,
        string $operation
    ): void {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $samsara = new Samsara('test-token');
        $samsara->setHttpFactory($http);

        $http->fake([
            '*' => $http->response($this->responseFor($resourceClass, $operation), $operation === 'delete' ? 204 : 200),
        ]);

        $resource = new $resourceClass($samsara);
        $result = match ($operation) {
            'query'  => $resource->query()->get(),
            'find'   => $resource->find('entity-id'),
            'create' => $resource->create(['name' => 'Contract entity']),
            'update' => $resource->update('entity-id', ['name' => 'Updated entity']),
            'delete' => $resource->delete('entity-id'),
        };

        if ($operation === 'query') {
            $this->assertInstanceOf(EntityCollection::class, $result);
            $this->assertInstanceOf($entityClass, $result->first());
        }

        if ($operation === 'delete') {
            $this->assertTrue($result);
        }

        if (! in_array($operation, ['query', 'delete'], true)) {
            $this->assertInstanceOf($entityClass, $result);
        }

        $http->assertSent(function (Request $request) use ($endpoint, $operation, $resourceClass): bool {
            [$expectedMethod, $expectedPath, $expectedQuery, $expectedData] = $this->expectedRequest(
                $resourceClass,
                $endpoint,
                $operation
            );

            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $actualQuery);

            return $request->method() === $expectedMethod
                && parse_url($request->url(), PHP_URL_PATH) === $expectedPath
                && $actualQuery === $expectedQuery
                && $request->data() === $expectedData;
        });
    }

    /**
     * @return array<string, array{class-string, class-string<Entity>, string, string}>
     */
    public static function genericOperations(): array
    {
        $cases = [];

        foreach (self::resources() as $resourceName => [$resourceClass, $entityClass, $endpoint, $operations]) {
            foreach ($operations as $operation) {
                $cases["{$resourceName} {$operation}"] = [$resourceClass, $entityClass, $endpoint, $operation];
            }
        }

        return $cases;
    }

    /**
     * @return array<string, array{class-string, class-string<Entity>, string, array<int, string>}>
     */
    public static function resources(): array
    {
        return [
            'addresses'         => [AddressesResource::class, Address::class, '/addresses', ['query', 'find', 'create', 'update', 'delete']],
            'routes'            => [RoutesResource::class, Route::class, '/fleet/routes', ['find', 'create', 'update', 'delete']],
            'drivers'           => [DriversResource::class, Driver::class, '/fleet/drivers', ['query', 'find', 'create', 'update']],
            'equipment'         => [EquipmentResource::class, Equipment::class, '/fleet/equipment', ['query', 'find']],
            'trailers'          => [TrailersResource::class, Trailer::class, '/fleet/trailers', ['query', 'find', 'create', 'update', 'delete']],
            'vehicles'          => [VehiclesResource::class, Vehicle::class, '/fleet/vehicles', ['query', 'find', 'update']],
            'gateways'          => [GatewaysResource::class, Gateway::class, '/gateways', ['query', 'create', 'delete']],
            'live shares'       => [LiveSharingLinksResource::class, LiveShare::class, '/live-shares', ['query', 'create', 'update', 'delete']],
            'webhooks'          => [WebhooksResource::class, Webhook::class, '/webhooks', ['query', 'find', 'create', 'update', 'delete']],
            'contacts'          => [ContactsResource::class, Contact::class, '/contacts', ['query', 'find', 'create', 'update', 'delete']],
            'tags'              => [TagsResource::class, Tag::class, '/tags', ['query', 'find', 'create', 'update', 'delete']],
            'users'             => [UsersResource::class, User::class, '/users', ['query', 'find', 'create', 'update', 'delete']],
            'vehicle locations' => [VehicleLocationsResource::class, VehicleLocationResult::class, '/fleet/vehicles/locations', ['query']],
        ];
    }

    /**
     * @param  class-string  $resourceClass
     * @return array{string, string, array<string, string>, array<string, mixed>}
     */
    private function expectedRequest(string $resourceClass, string $endpoint, string $operation): array
    {
        if ($resourceClass === LiveSharingLinksResource::class && in_array($operation, ['update', 'delete'], true)) {
            return [
                $operation === 'update' ? 'PATCH' : 'DELETE',
                $endpoint,
                ['id' => 'entity-id'],
                $operation === 'update' ? ['name' => 'Updated entity'] : [],
            ];
        }

        return match ($operation) {
            'query'  => ['GET', $endpoint, [], []],
            'find'   => ['GET', "{$endpoint}/entity-id", [], []],
            'create' => ['POST', $endpoint, [], ['name' => 'Contract entity']],
            'update' => ['PATCH', "{$endpoint}/entity-id", [], ['name' => 'Updated entity']],
            'delete' => ['DELETE', "{$endpoint}/entity-id", [], []],
        };
    }

    /**
     * @param  class-string  $resourceClass
     * @return array<string, mixed>
     */
    private function responseFor(string $resourceClass, string $operation): array
    {
        if ($operation === 'delete') {
            return [];
        }

        if ($resourceClass === VehicleLocationsResource::class) {
            return [
                'data' => [[
                    'id'       => 'entity-id',
                    'name'     => 'Contract vehicle',
                    'location' => [
                        'latitude'  => 35.8456,
                        'longitude' => -86.3903,
                        'time'      => '2026-08-10T15:00:00Z',
                    ],
                ]],
            ];
        }

        $data = $operation === 'query'
            ? [['id' => 'entity-id']]
            : ['id' => 'entity-id'];

        if ($resourceClass === WebhooksResource::class && $operation !== 'query') {
            return $data;
        }

        return ['data' => $data];
    }
}
