<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Additional\SettingsResource;

/**
 * Unit tests for the SettingsResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SettingsResourceTest extends TestCase
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
    public function it_can_get_compliance_settings(): void
    {
        $this->http->fake([
            '*/fleet/settings/compliance' => $this->http->response([
                'data' => [
                    'canadaHosEnabled' => true,
                ],
            ], 200),
        ]);

        $resource = new SettingsResource($this->samsara);

        $result = $resource->compliance();

        $this->assertIsArray($result);
        $this->assertTrue($result['canadaHosEnabled']);
    }

    #[Test]
    public function it_can_get_driver_app_settings(): void
    {
        $this->http->fake([
            '*/fleet/settings/driver-app' => $this->http->response([
                'data' => [
                    'orgVehicleSearch' => true,
                ],
            ], 200),
        ]);

        $resource = new SettingsResource($this->samsara);

        $result = $resource->driverApp();

        $this->assertIsArray($result);
        $this->assertTrue($result['orgVehicleSearch']);
    }

    #[Test]
    public function it_can_get_safety_settings(): void
    {
        $this->http->fake([
            '*/fleet/settings/safety' => $this->http->response([
                'data' => [
                    ['eventType' => 'harshBrake'],
                ],
            ], 200),
        ]);

        $resource = new SettingsResource($this->samsara);

        $result = $resource->safety();

        $this->assertIsArray($result);
        $this->assertSame('harshBrake', $result[0]['eventType']);
    }

    #[Test]
    public function it_can_update_compliance_settings(): void
    {
        $this->http->fake([
            '*/fleet/settings/compliance' => $this->http->response([
                'data' => [
                    'canadaHosEnabled' => false,
                ],
            ], 200),
        ]);

        $resource = new SettingsResource($this->samsara);

        $result = $resource->updateCompliance(['canadaHosEnabled' => false]);

        $this->assertIsArray($result);
        $this->assertFalse($result['canadaHosEnabled']);
    }

    #[Test]
    public function it_can_update_driver_app_settings(): void
    {
        $this->http->fake([
            '*/fleet/settings/driver-app' => $this->http->response([
                'data' => [
                    'orgVehicleSearch' => false,
                ],
            ], 200),
        ]);

        $resource = new SettingsResource($this->samsara);

        $result = $resource->updateDriverApp(['orgVehicleSearch' => false]);

        $this->assertIsArray($result);
        $this->assertFalse($result['orgVehicleSearch']);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new SettingsResource($this->samsara);

        $this->assertSame('/fleet/settings', $resource->getEndpoint());
    }
}
