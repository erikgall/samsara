<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Legacy;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Legacy\LegacyResource;

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
}
