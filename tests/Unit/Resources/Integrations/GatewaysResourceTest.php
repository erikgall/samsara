<?php

namespace Samsara\Tests\Unit\Resources\Integrations;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use Samsara\Data\Vehicle\Gateway;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Integrations\GatewaysResource;

/**
 * Unit tests for the GatewaysResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class GatewaysResourceTest extends TestCase
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
    public function it_can_create_a_gateway(): void
    {
        $this->http->fake([
            '*/gateways' => $this->http->response([
                'data' => [
                    'id'     => 'gateway-123',
                    'serial' => 'ABC123',
                    'model'  => 'VG34',
                ],
            ], 201),
        ]);

        $resource = new GatewaysResource($this->samsara);

        $gateway = $resource->create([
            'serial' => 'ABC123',
            'model'  => 'VG34',
        ]);

        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertSame('ABC123', $gateway->serial);
    }

    #[Test]
    public function it_can_delete_a_gateway(): void
    {
        $this->http->fake([
            '*/gateways/gateway-123' => $this->http->response([], 204),
        ]);

        $resource = new GatewaysResource($this->samsara);

        $result = $resource->delete('gateway-123');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_get_all_gateways(): void
    {
        $this->http->fake([
            '*/gateways' => $this->http->response([
                'data' => [
                    ['id' => 'gateway-1', 'serial' => 'ABC123'],
                    ['id' => 'gateway-2', 'serial' => 'DEF456'],
                ],
            ], 200),
        ]);

        $resource = new GatewaysResource($this->samsara);

        $result = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new GatewaysResource($this->samsara);

        $this->assertSame('/gateways', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new GatewaysResource($this->samsara);

        $this->assertSame(Gateway::class, $resource->getEntityClass());
    }
}
