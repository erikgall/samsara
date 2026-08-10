<?php

namespace Samsara\Tests\Unit\Resources\Legacy;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Legacy\LegacyResource;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Exceptions\UnsupportedOperationException;

/**
 * Unit tests for the LegacyResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class LegacyResourceTest extends TestCase
{
    protected HttpFactory $http;

    protected Samsara $samsara;

    protected function setUp(): void
    {
        parent::setUp();

        $this->http = new HttpFactory;
        $this->samsara = new Samsara('test-token');
        $this->samsara->setHttpFactory($this->http);
    }

    #[Test]
    public function it_can_get_asset_locations(): void
    {
        $this->http->fake([
            '*v1/fleet/assets/locations*' => $this->http->response([
                'assets' => [
                    ['id' => 1, 'location' => ['latitude' => 37.4, 'longitude' => -122.0]],
                ],
            ], 200),
        ]);

        $resource = new LegacyResource($this->samsara);

        $result = $resource->assetLocations(['groupId' => 123]);

        $this->assertIsArray($result);
    }

    #[Test]
    public function it_can_get_asset_reefers(): void
    {
        $this->http->fake([
            '*v1/fleet/assets/reefers*' => $this->http->response([
                'reefers' => [
                    ['id' => 1, 'setPoint' => -5],
                ],
            ], 200),
        ]);

        $resource = new LegacyResource($this->samsara);

        $result = $resource->assetReefers(['groupId' => 123]);

        $this->assertIsArray($result);
    }

    #[Test]
    public function it_can_get_dispatch_routes(): void
    {
        $this->http->fake([
            '*v1/fleet/dispatch/routes*' => $this->http->response([
                ['id' => 1, 'name' => 'Route 1'],
                ['id' => 2, 'name' => 'Route 2'],
            ], 200),
        ]);

        $resource = new LegacyResource($this->samsara);

        $result = $resource->dispatchRoutes(['groupId' => 123]);

        $this->assertIsArray($result);
    }

    #[Test]
    public function it_can_get_driver_safety_score(): void
    {
        $this->http->fake([
            '*v1/fleet/drivers/driver-123/safety/score*' => $this->http->response([
                'safetyScore'      => 85,
                'totalMilesDriven' => 1000,
            ], 200),
        ]);

        $resource = new LegacyResource($this->samsara);

        $result = $resource->driverSafetyScore('driver-123', ['startMs' => 1000, 'endMs' => 2000]);

        $this->assertIsArray($result);
        $this->assertSame(85, $result['safetyScore']);
    }

    #[Test]
    public function it_can_get_fleet_assets(): void
    {
        $this->http->fake([
            '*v1/fleet/assets*' => $this->http->response([
                'assets' => [
                    ['id' => 1, 'name' => 'Asset 1'],
                    ['id' => 2, 'name' => 'Asset 2'],
                ],
            ], 200),
        ]);

        $resource = new LegacyResource($this->samsara);

        $result = $resource->fleetAssets(['groupId' => 123]);

        $this->assertIsArray($result);
        $this->assertCount(2, $result['assets']);
    }

    #[Test]
    public function it_can_get_hos_authentication_logs(): void
    {
        $this->http->fake([
            '*v1/fleet/hos_authentication_logs*' => $this->http->response([
                'authenticationLogs' => [
                    ['action' => 'login'],
                ],
            ], 200),
        ]);

        $resource = new LegacyResource($this->samsara);

        $result = $resource->hosAuthenticationLogs(['driverId' => 123]);

        $this->assertIsArray($result);
    }

    #[Test]
    public function it_can_get_machines(): void
    {
        $this->http->fake([
            '*v1/machines/list*' => $this->http->response([
                'machines' => [
                    ['id' => 1, 'name' => 'Machine 1'],
                ],
            ], 200),
        ]);

        $resource = new LegacyResource($this->samsara);

        $result = $resource->machines(['groupId' => 123]);

        $this->assertIsArray($result);
    }

    #[Test]
    public function it_can_get_maintenance_list(): void
    {
        $this->http->fake([
            '*v1/fleet/maintenance/list*' => $this->http->response([
                'vehicles' => [
                    ['id' => 1, 'j1939' => []],
                ],
            ], 200),
        ]);

        $resource = new LegacyResource($this->samsara);

        $result = $resource->maintenanceList(['groupId' => 123]);

        $this->assertIsArray($result);
    }

    #[Test]
    public function it_can_get_messages(): void
    {
        $this->http->fake([
            '*v1/fleet/messages*' => $this->http->response([
                'messages' => [
                    ['id' => 1, 'text' => 'Hello'],
                ],
            ], 200),
        ]);

        $resource = new LegacyResource($this->samsara);

        $result = $resource->messages(['groupId' => 123]);

        $this->assertIsArray($result);
    }

    #[Test]
    public function it_can_get_trips(): void
    {
        $this->http->fake([
            '*v1/fleet/trips*' => $this->http->response([
                'trips' => [
                    ['startMs' => 1000, 'endMs' => 2000],
                ],
            ], 200),
        ]);

        $resource = new LegacyResource($this->samsara);

        $result = $resource->trips(['vehicleId' => 123, 'startMs' => 1000, 'endMs' => 2000]);

        $this->assertIsArray($result);
    }

    #[Test]
    public function it_can_get_vehicle_harsh_event(): void
    {
        $this->http->fake([
            '*v1/fleet/vehicles/vehicle-123/safety/harsh_event*' => $this->http->response([
                'harshEventType' => 'Braking',
            ], 200),
        ]);

        $resource = new LegacyResource($this->samsara);

        $result = $resource->vehicleHarshEvent('vehicle-123', ['timestamp' => 1000]);

        $this->assertIsArray($result);
        $this->assertSame('Braking', $result['harshEventType']);
    }

    #[Test]
    public function it_can_get_vehicle_safety_score(): void
    {
        $this->http->fake([
            '*v1/fleet/vehicles/vehicle-123/safety/score*' => $this->http->response([
                'safetyScore' => 90,
            ], 200),
        ]);

        $resource = new LegacyResource($this->samsara);

        $result = $resource->vehicleSafetyScore('vehicle-123', ['startMs' => 1000, 'endMs' => 2000]);

        $this->assertIsArray($result);
        $this->assertSame(90, $result['safetyScore']);
    }

    #[Test]
    public function it_can_get_vision_cameras(): void
    {
        $this->http->fake([
            '*v1/industrial/vision/cameras*' => $this->http->response([
                'cameras' => [
                    ['id' => 'cam-1'],
                ],
            ], 200),
        ]);

        $resource = new LegacyResource($this->samsara);

        $result = $resource->visionCameras(['groupId' => 123]);

        $this->assertIsArray($result);
    }

    #[Test]
    public function it_rejects_phantom_generic_operations_on_the_legacy_root(): void
    {
        $this->http->fake();
        $resource = new LegacyResource($this->samsara);

        try {
            $resource->all();
            $this->fail('Expected the inherited all operation to be rejected.');
        } catch (UnsupportedOperationException) {
            $this->http->assertNothingSent();
        }
    }

    /**
     * @param  array<int, mixed>  $arguments
     * @param  array<string, mixed>  $payload
     */
    #[Test]
    #[DataProvider('legacyRequestContracts')]
    public function it_routes_legacy_requests_through_the_exact_wire_contract(
        string $method,
        array $arguments,
        string $httpMethod,
        string $path,
        array $payload
    ): void {
        $this->http->fake([
            '*' => $this->http->response(['ok' => true]),
        ]);
        $resource = new LegacyResource($this->samsara);

        $result = $resource->{$method}(...$arguments);

        $this->assertSame(['ok' => true], $result);
        $this->http->assertSent(function ($request) use ($httpMethod, $path, $payload): bool {
            if ($request->method() !== $httpMethod || parse_url($request->url(), PHP_URL_PATH) !== $path) {
                return false;
            }

            return $httpMethod === 'GET'
                ? $this->queryFor($request->url()) === $this->stringify($payload)
                : $request->data() === $payload;
        });
    }

    /**
     * @return array<string, array{string, array<int, mixed>, string, string, array<string, mixed>}>
     */
    public static function legacyRequestContracts(): array
    {
        return [
            'asset locations'     => ['assetLocations', [['groupId' => 123]], 'POST', '/v1/fleet/assets/locations', ['groupId' => 123]],
            'asset reefers'       => ['assetReefers', [['groupId' => 123]], 'POST', '/v1/fleet/assets/reefers', ['groupId' => 123]],
            'dispatch routes'     => ['dispatchRoutes', [['groupId' => 123]], 'GET', '/v1/fleet/dispatch/routes', ['groupId' => 123]],
            'driver safety score' => [
                'driverSafetyScore',
                ['driver-123', ['startMs' => 1000, 'endMs' => 2000]],
                'GET',
                '/v1/fleet/drivers/driver-123/safety/score',
                ['startMs' => 1000, 'endMs' => 2000],
            ],
            'fleet assets'            => ['fleetAssets', [['groupId' => 123]], 'POST', '/v1/fleet/assets', ['groupId' => 123]],
            'hos authentication logs' => [
                'hosAuthenticationLogs',
                [['driverId' => 123]],
                'POST',
                '/v1/fleet/hos_authentication_logs',
                ['driverId' => 123],
            ],
            'machines'         => ['machines', [['groupId' => 123]], 'POST', '/v1/machines/list', ['groupId' => 123]],
            'maintenance list' => ['maintenanceList', [['groupId' => 123]], 'POST', '/v1/fleet/maintenance/list', ['groupId' => 123]],
            'messages'         => ['messages', [['groupId' => 123]], 'POST', '/v1/fleet/messages', ['groupId' => 123]],
            'trips'            => [
                'trips',
                [['vehicleId' => 123, 'startMs' => 1000, 'endMs' => 2000]],
                'POST',
                '/v1/fleet/trips',
                ['vehicleId' => 123, 'startMs' => 1000, 'endMs' => 2000],
            ],
            'vehicle harsh event' => [
                'vehicleHarshEvent',
                ['vehicle-123', ['timestamp' => 1000]],
                'GET',
                '/v1/fleet/vehicles/vehicle-123/safety/harsh_event',
                ['timestamp' => 1000],
            ],
            'vehicle safety score' => [
                'vehicleSafetyScore',
                ['vehicle-123', ['startMs' => 1000, 'endMs' => 2000]],
                'GET',
                '/v1/fleet/vehicles/vehicle-123/safety/score',
                ['startMs' => 1000, 'endMs' => 2000],
            ],
            'vision cameras' => ['visionCameras', [['groupId' => 123]], 'POST', '/v1/industrial/vision/cameras', ['groupId' => 123]],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function queryFor(string $url): array
    {
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        /** @var array<string, string> $query */
        return $query;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, string>
     */
    private function stringify(array $payload): array
    {
        return array_map(static fn (mixed $value): string => (string) $value, $payload);
    }
}
