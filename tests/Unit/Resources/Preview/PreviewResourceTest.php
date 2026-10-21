<?php

namespace Samsara\Tests\Unit\Resources\Preview;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Preview\PreviewResource;
use Illuminate\Http\Client\Factory as HttpFactory;

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
        $this->http->preventStrayRequests();
        $this->samsara = new Samsara('test-token');
        $this->samsara->setHttpFactory($this->http);
    }

    #[Test]
    public function it_can_create_driver_auth_token(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'token'          => 'abc123token',
                    'expirationTime' => 1786370400,
                ],
            ], 200),
        ]);

        $resource = new PreviewResource($this->samsara);

        $result = $resource->createDriverAuthToken([
            'code' => '123456789012',
            'id'   => 123,
        ]);

        $this->assertIsArray($result);
        $this->assertSame('abc123token', $result['token']);
        $this->assertSame(1786370400, $result['expirationTime']);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'POST'
            && parse_url($request->url(), PHP_URL_PATH) === '/preview/fleet/drivers/create-auth-token'
            && $request->data() === ['code' => '123456789012', 'id' => 123]);
    }

    #[Test]
    public function it_can_lock_vehicle(): void
    {
        $this->http->fake([
            '*' => $this->http->response(null, 200),
        ]);

        $resource = new PreviewResource($this->samsara);

        $result = $resource->lockVehicle('vehicle-123');

        $this->assertTrue($result);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'PUT'
            && parse_url($request->url(), PHP_URL_PATH) === '/preview/fleet/vehicles/vehicle-123/lock'
            && $request->data() === []);
    }

    #[Test]
    public function it_can_unlock_vehicle(): void
    {
        $this->http->fake([
            '*' => $this->http->response(null, 204),
        ]);

        $resource = new PreviewResource($this->samsara);

        $result = $resource->unlockVehicle('vehicle-123');

        $this->assertTrue($result);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'DELETE'
            && parse_url($request->url(), PHP_URL_PATH) === '/preview/fleet/vehicles/vehicle-123/lock'
            && $request->data() === []);
    }
}
