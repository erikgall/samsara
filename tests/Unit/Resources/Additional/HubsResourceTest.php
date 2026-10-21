<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Additional\HubsResource;
use Illuminate\Http\Client\Factory as HttpFactory;

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
        $this->http->preventStrayRequests();
        $this->samsara = new Samsara('test-token');
        $this->samsara->setHttpFactory($this->http);
    }

    #[Test]
    public function it_can_get_query_builder(): void
    {
        $resource = new HubsResource($this->samsara);

        $builder = $resource->query();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_does_not_expose_create_hub(): void
    {
        $this->assertFalse(method_exists(HubsResource::class, 'create'));
    }

    #[Test]
    public function it_does_not_expose_delete_hub(): void
    {
        $this->assertFalse(method_exists(HubsResource::class, 'delete'));
    }

    #[Test]
    public function it_does_not_expose_find_hub(): void
    {
        $this->assertFalse(method_exists(HubsResource::class, 'find'));
    }

    #[Test]
    public function it_does_not_expose_update_hub(): void
    {
        $this->assertFalse(method_exists(HubsResource::class, 'update'));
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new HubsResource($this->samsara);

        $this->assertSame('/hubs', $resource->getEndpoint());
    }

    #[Test]
    public function it_queries_hubs(): void
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

        $result = $resource->query()->get();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }
}
