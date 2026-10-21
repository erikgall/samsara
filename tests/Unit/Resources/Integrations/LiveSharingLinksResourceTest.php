<?php

namespace Samsara\Tests\Unit\Resources\Integrations;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Request;
use Samsara\Data\LiveShare\LiveShare;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Integrations\LiveSharingLinksResource;

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
        $this->http->preventStrayRequests();
        $this->samsara = new Samsara('test-token');
        $this->samsara->setHttpFactory($this->http);
    }

    #[Test]
    public function it_can_create_a_live_share(): void
    {
        $this->http->fake([
            '*/live-shares' => $this->http->response([
                'data' => [
                    'id'             => 'share-123',
                    'name'           => 'New Share',
                    'type'           => 'assetsLocation',
                    'liveSharingUrl' => 'https://cloud.samsara.com/live/share-123',
                ],
            ], 200),
        ]);

        $resource = new LiveSharingLinksResource($this->samsara);

        $liveShare = $resource->create([
            'name'                     => 'New Share',
            'type'                     => 'assetsLocation',
            'assetsLocationLinkConfig' => ['assetIds' => ['vehicle-1']],
        ]);

        $this->assertInstanceOf(LiveShare::class, $liveShare);
        $this->assertSame('New Share', $liveShare->name);
        $this->assertSame('https://cloud.samsara.com/live/share-123', $liveShare->liveSharingUrl);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && parse_url($request->url(), PHP_URL_PATH) === '/live-shares'
            && $request->data() === [
                'name'                     => 'New Share',
                'type'                     => 'assetsLocation',
                'assetsLocationLinkConfig' => ['assetIds' => ['vehicle-1']],
            ]
        );
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
            '*' => $this->http->response([], 204),
        ]);

        $resource = new LiveSharingLinksResource($this->samsara);

        $result = $resource->delete('share-123');

        $this->assertTrue($result);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
            && parse_url($request->url(), PHP_URL_PATH) === '/live-shares'
            && parse_url($request->url(), PHP_URL_QUERY) === 'id=share-123'
            && $request->data() === []
        );
    }

    #[Test]
    public function it_can_update_a_live_share(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'             => 'share-123',
                    'name'           => 'Updated Share',
                    'type'           => 'assetsLocation',
                    'liveSharingUrl' => 'https://cloud.samsara.com/live/share-123',
                ],
            ], 200),
        ]);

        $resource = new HydrationAwareLiveSharingLinksResource($this->samsara);

        $liveShare = $resource->update('share-123', ['name' => 'Updated Share']);

        $this->assertInstanceOf(LiveShare::class, $liveShare);
        $this->assertSame('Updated Share', $liveShare->name);
        $this->assertTrue($resource->mappedUpdateResponse);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
            && parse_url($request->url(), PHP_URL_PATH) === '/live-shares'
            && parse_url($request->url(), PHP_URL_QUERY) === 'id=share-123'
            && $request->data() === ['name' => 'Updated Share']
        );
    }

    #[Test]
    public function it_does_not_hydrate_an_updated_live_share_from_the_whole_payload(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['id' => 'wrong-level-share']),
        ]);

        $liveShare = (new LiveSharingLinksResource($this->samsara))->update('share-123', []);

        $this->assertNull($liveShare->id);
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

    #[Test]
    public function it_queries_live_shares(): void
    {
        $this->http->fake([
            '*/live-shares' => $this->http->response([
                'data' => [
                    [
                        'id'             => 'share-1',
                        'name'           => 'Share 1',
                        'type'           => 'assetsLocation',
                        'liveSharingUrl' => 'https://cloud.samsara.com/live/share-1',
                    ],
                    [
                        'id'             => 'share-2',
                        'name'           => 'Share 2',
                        'type'           => 'assetsLocation',
                        'liveSharingUrl' => 'https://cloud.samsara.com/live/share-2',
                    ],
                ],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ], 200),
        ]);

        $resource = new LiveSharingLinksResource($this->samsara);

        $result = $resource->query()->get();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(LiveShare::class, $result->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/live-shares'
            && $request->data() === []
        );
    }

    #[Test]
    public function it_terminal_executes_filtered_live_share_query(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'             => 'share-1',
                    'name'           => 'Share 1',
                    'type'           => 'assetsOnRoute',
                    'liveSharingUrl' => 'https://cloud.samsara.com/live/share-1',
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $page = (new LiveSharingLinksResource($this->samsara))
            ->query()
            ->where('ids', 'share-1,share-2')
            ->where('type', 'assetsOnRoute')
            ->after('share-cursor')
            ->paginate(50);

        $this->assertInstanceOf(LiveShare::class, $page->items()->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/live-shares'
            && $request->data() == [
                'ids'   => 'share-1,share-2',
                'type'  => 'assetsOnRoute',
                'after' => 'share-cursor',
                'limit' => 50,
            ]
        );
    }
}

class HydrationAwareLiveSharingLinksResource extends LiveSharingLinksResource
{
    public bool $mappedUpdateResponse = false;

    protected function mapToEntity(array $data): LiveShare
    {
        $this->mappedUpdateResponse = true;

        return parent::mapToEntity($data);
    }
}
