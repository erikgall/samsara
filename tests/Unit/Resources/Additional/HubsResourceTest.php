<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Additional\HubsResource;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Exceptions\UnsupportedOperationException;

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
        $resource = new HubsResource($this->samsara);

        $this->expectException(UnsupportedOperationException::class);

        $resource->create(['name' => 'New Hub']);
    }

    #[Test]
    public function it_can_delete_hub(): void
    {
        $resource = new HubsResource($this->samsara);

        $this->expectException(UnsupportedOperationException::class);

        $resource->delete('hub-123');
    }

    #[Test]
    public function it_can_find_hub(): void
    {
        $resource = new HubsResource($this->samsara);

        $this->expectException(UnsupportedOperationException::class);

        $resource->find('hub-123');
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
        $resource = new HubsResource($this->samsara);

        $this->expectException(UnsupportedOperationException::class);

        $resource->update('hub-123', ['name' => 'Updated Hub']);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new HubsResource($this->samsara);

        $this->assertSame('/hubs', $resource->getEndpoint());
    }
}
