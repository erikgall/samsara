<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Integrations;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\EntityCollection;
use ErikGall\Samsara\Data\LiveShare\LiveShare;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Integrations\LiveSharingLinksResource;

/**
 * Unit tests for the LiveSharingLinksResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class LiveSharingLinksResourceTest extends TestCase
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
    public function it_can_create_a_live_share(): void
    {
        $this->http->fake([
            '*/live-shares' => $this->http->response([
                'data' => [
                    'id'     => 'share-123',
                    'name'   => 'New Share',
                    'status' => 'active',
                ],
            ], 201),
        ]);

        $resource = new LiveSharingLinksResource($this->samsara);

        $liveShare = $resource->create([
            'name'   => 'New Share',
            'assets' => [['id' => 'vehicle-1', 'type' => 'vehicle']],
        ]);

        $this->assertInstanceOf(LiveShare::class, $liveShare);
        $this->assertSame('New Share', $liveShare->name);
    }

    #[Test]
    public function it_can_create_query_builder(): void
    {
        $resource = new LiveSharingLinksResource($this->samsara);

        $builder = $resource->query();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_delete_a_live_share(): void
    {
        $this->http->fake([
            '*/live-shares/share-123' => $this->http->response([], 204),
        ]);

        $resource = new LiveSharingLinksResource($this->samsara);

        $result = $resource->delete('share-123');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_find_a_live_share(): void
    {
        $this->http->fake([
            '*/live-shares/share-123' => $this->http->response([
                'data' => [
                    'id'   => 'share-123',
                    'name' => 'Test Share',
                ],
            ], 200),
        ]);

        $resource = new LiveSharingLinksResource($this->samsara);

        $liveShare = $resource->find('share-123');

        $this->assertInstanceOf(LiveShare::class, $liveShare);
        $this->assertSame('share-123', $liveShare->id);
    }

    #[Test]
    public function it_can_get_all_live_shares(): void
    {
        $this->http->fake([
            '*/live-shares' => $this->http->response([
                'data' => [
                    ['id' => 'share-1', 'name' => 'Share 1'],
                    ['id' => 'share-2', 'name' => 'Share 2'],
                ],
            ], 200),
        ]);

        $resource = new LiveSharingLinksResource($this->samsara);

        $result = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_can_update_a_live_share(): void
    {
        $this->http->fake([
            '*/live-shares/share-123' => $this->http->response([
                'data' => [
                    'id'   => 'share-123',
                    'name' => 'Updated Share',
                ],
            ], 200),
        ]);

        $resource = new LiveSharingLinksResource($this->samsara);

        $liveShare = $resource->update('share-123', ['name' => 'Updated Share']);

        $this->assertInstanceOf(LiveShare::class, $liveShare);
        $this->assertSame('Updated Share', $liveShare->name);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new LiveSharingLinksResource($this->samsara);

        $this->assertSame('/live-shares', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new LiveSharingLinksResource($this->samsara);

        $this->assertSame(LiveShare::class, $resource->getEntityClass());
    }
}
