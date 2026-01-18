<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Preview;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Preview\PreviewResource;

/**
 * Unit tests for the PreviewResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class PreviewResourceTest extends TestCase
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
    public function it_can_create_driver_auth_token(): void
    {
        $this->http->fake([
            '*/fleet/drivers/driver-123/auth-token' => $this->http->response([
                'data' => [
                    'token' => 'abc123token',
                ],
            ], 201),
        ]);

        $resource = new PreviewResource($this->samsara);

        $result = $resource->createDriverAuthToken('driver-123', ['expiresIn' => 3600]);

        $this->assertIsArray($result);
        $this->assertSame('abc123token', $result['token']);
    }

    #[Test]
    public function it_can_lock_vehicle(): void
    {
        $this->http->fake([
            '*/fleet/vehicles/vehicle-123/lock' => $this->http->response([
                'data' => [
                    'id'     => 'vehicle-123',
                    'locked' => true,
                ],
            ], 200),
        ]);

        $resource = new PreviewResource($this->samsara);

        $result = $resource->lockVehicle('vehicle-123');

        $this->assertIsArray($result);
        $this->assertTrue($result['locked']);
    }

    #[Test]
    public function it_can_unlock_vehicle(): void
    {
        $this->http->fake([
            '*/fleet/vehicles/vehicle-123/unlock' => $this->http->response([
                'data' => [
                    'id'     => 'vehicle-123',
                    'locked' => false,
                ],
            ], 200),
        ]);

        $resource = new PreviewResource($this->samsara);

        $result = $resource->unlockVehicle('vehicle-123');

        $this->assertIsArray($result);
        $this->assertFalse($result['locked']);
    }
}
