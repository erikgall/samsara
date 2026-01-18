<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Additional;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Additional\SettingsResource;

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
            '*/settings/compliance' => $this->http->response([
                'data' => [
                    'hosEnabled' => true,
                ],
            ], 200),
        ]);

        $resource = new SettingsResource($this->samsara);

        $result = $resource->compliance();

        $this->assertIsArray($result);
        $this->assertTrue($result['hosEnabled']);
    }

    #[Test]
    public function it_can_get_driver_app_settings(): void
    {
        $this->http->fake([
            '*/settings/driver-app' => $this->http->response([
                'data' => [
                    'gpsEnabled' => true,
                ],
            ], 200),
        ]);

        $resource = new SettingsResource($this->samsara);

        $result = $resource->driverApp();

        $this->assertIsArray($result);
        $this->assertTrue($result['gpsEnabled']);
    }

    #[Test]
    public function it_can_get_safety_settings(): void
    {
        $this->http->fake([
            '*/settings/safety' => $this->http->response([
                'data' => [
                    'dashcamEnabled' => true,
                ],
            ], 200),
        ]);

        $resource = new SettingsResource($this->samsara);

        $result = $resource->safety();

        $this->assertIsArray($result);
        $this->assertTrue($result['dashcamEnabled']);
    }

    #[Test]
    public function it_can_update_compliance_settings(): void
    {
        $this->http->fake([
            '*/settings/compliance' => $this->http->response([
                'data' => [
                    'hosEnabled' => false,
                ],
            ], 200),
        ]);

        $resource = new SettingsResource($this->samsara);

        $result = $resource->updateCompliance(['hosEnabled' => false]);

        $this->assertIsArray($result);
        $this->assertFalse($result['hosEnabled']);
    }

    #[Test]
    public function it_can_update_driver_app_settings(): void
    {
        $this->http->fake([
            '*/settings/driver-app' => $this->http->response([
                'data' => [
                    'gpsEnabled' => false,
                ],
            ], 200),
        ]);

        $resource = new SettingsResource($this->samsara);

        $result = $resource->updateDriverApp(['gpsEnabled' => false]);

        $this->assertIsArray($result);
        $this->assertFalse($result['gpsEnabled']);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new SettingsResource($this->samsara);

        $this->assertSame('/settings', $resource->getEndpoint());
    }
}
