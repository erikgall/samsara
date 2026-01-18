<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Additional;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Additional\HubsResource;

/**
 * Unit tests for the HubsResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class HubsResourceTest extends TestCase
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
    public function it_can_create_hub(): void
    {
        $this->http->fake([
            '*/hubs' => $this->http->response([
                'data' => [
                    'id'   => 'hub-123',
                    'name' => 'New Hub',
                ],
            ], 201),
        ]);

        $resource = new HubsResource($this->samsara);

        $result = $resource->create(['name' => 'New Hub']);

        $this->assertSame('hub-123', $result->id);
    }

    #[Test]
    public function it_can_delete_hub(): void
    {
        $this->http->fake([
            '*/hubs/hub-123' => $this->http->response([], 204),
        ]);

        $resource = new HubsResource($this->samsara);

        $result = $resource->delete('hub-123');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_find_hub(): void
    {
        $this->http->fake([
            '*/hubs/hub-123' => $this->http->response([
                'data' => [
                    'id'   => 'hub-123',
                    'name' => 'Test Hub',
                ],
            ], 200),
        ]);

        $resource = new HubsResource($this->samsara);

        $result = $resource->find('hub-123');

        $this->assertSame('hub-123', $result->id);
    }

    #[Test]
    public function it_can_get_all_hubs(): void
    {
        $this->http->fake([
            '*/hubs' => $this->http->response([
                'data' => [
                    ['id' => 'hub-1'],
                    ['id' => 'hub-2'],
                ],
            ], 200),
        ]);

        $resource = new HubsResource($this->samsara);

        $result = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_can_get_query_builder(): void
    {
        $resource = new HubsResource($this->samsara);

        $builder = $resource->query();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_update_hub(): void
    {
        $this->http->fake([
            '*/hubs/hub-123' => $this->http->response([
                'data' => [
                    'id'   => 'hub-123',
                    'name' => 'Updated Hub',
                ],
            ], 200),
        ]);

        $resource = new HubsResource($this->samsara);

        $result = $resource->update('hub-123', ['name' => 'Updated Hub']);

        $this->assertSame('hub-123', $result->id);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new HubsResource($this->samsara);

        $this->assertSame('/hubs', $resource->getEndpoint());
    }
}
