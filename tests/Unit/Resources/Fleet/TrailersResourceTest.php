<?php

namespace Samsara\Tests\Unit\Resources\Fleet;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use Samsara\Data\Trailer\Trailer;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Request;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Fleet\TrailersResource;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Unit tests for the TrailersResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TrailersResourceTest extends TestCase
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
    public function it_can_be_created(): void
    {
        $resource = new TrailersResource($this->samsara);

        $this->assertInstanceOf(TrailersResource::class, $resource);
    }

    #[Test]
    public function it_can_create_trailer(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => '123',
                    'name' => 'New Trailer',
                ],
            ]),
        ]);

        $resource = new TrailersResource($this->samsara);
        $trailer = $resource->create([
            'name' => 'New Trailer',
        ]);

        $this->assertInstanceOf(Trailer::class, $trailer);
        $this->assertSame('123', $trailer->id);
        $this->assertSame('New Trailer', $trailer->name);
    }

    #[Test]
    public function it_can_delete_trailer(): void
    {
        $this->http->fake([
            '*' => $this->http->response([]),
        ]);

        $resource = new TrailersResource($this->samsara);
        $result = $resource->delete('123');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_find_trailer_by_external_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'          => '123',
                    'name'        => 'Trailer 001',
                    'externalIds' => ['fleet_code' => 'TR-123'],
                ],
            ]),
        ]);

        $resource = new TrailersResource($this->samsara);
        $trailer = $resource->findByExternalId('fleet_code', 'TR-123');

        $this->assertInstanceOf(Trailer::class, $trailer);
        $this->assertSame('123', $trailer->id);
        $this->http->assertSent(fn (Request $request) => $request->method() === 'GET'
            && $request->url() === 'https://api.samsara.com/fleet/trailers/fleet_code:TR-123'
            && $request->data() === []);
    }

    #[Test]
    public function it_can_find_trailer_by_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => ['id' => '123', 'name' => 'Trailer 001'],
            ]),
        ]);

        $resource = new TrailersResource($this->samsara);
        $trailer = $resource->find('123');

        $this->assertInstanceOf(Trailer::class, $trailer);
        $this->assertSame('123', $trailer->id);
        $this->assertSame('Trailer 001', $trailer->name);
    }

    #[Test]
    public function it_can_get_all_trailers(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    ['id' => '1', 'name' => 'Trailer 001'],
                    ['id' => '2', 'name' => 'Trailer 002'],
                ],
            ]),
        ]);

        $resource = new TrailersResource($this->samsara);
        $trailers = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $trailers);
        $this->assertCount(2, $trailers);
        $this->assertInstanceOf(Trailer::class, $trailers->first());
        $this->assertSame('Trailer 001', $trailers->first()->name);
    }

    #[Test]
    public function it_can_update_trailer(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => '123',
                    'name' => 'Updated Trailer',
                ],
            ]),
        ]);

        $resource = new TrailersResource($this->samsara);
        $trailer = $resource->update('123', ['name' => 'Updated Trailer']);

        $this->assertInstanceOf(Trailer::class, $trailer);
        $this->assertSame('Updated Trailer', $trailer->name);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new TrailersResource($this->samsara);

        $this->assertSame('/fleet/trailers', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new TrailersResource($this->samsara);

        $this->assertSame(Trailer::class, $resource->getEntityClass());
    }

    #[Test]
    public function it_returns_null_when_external_id_not_found(): void
    {
        $this->http->fake([
            '*' => $this->http->response([], 404),
        ]);

        $resource = new TrailersResource($this->samsara);
        $trailer = $resource->findByExternalId('fleet_code', 'NONEXISTENT');

        $this->assertNull($trailer);
        $this->http->assertSent(fn (Request $request) => $request->url()
            === 'https://api.samsara.com/fleet/trailers/fleet_code:NONEXISTENT');
    }

    #[Test]
    public function it_returns_null_when_trailer_not_found(): void
    {
        $this->http->fake([
            '*' => $this->http->response([], 404),
        ]);

        $resource = new TrailersResource($this->samsara);
        $trailer = $resource->find('999');

        $this->assertNull($trailer);
    }

    #[Test]
    public function it_terminal_executes_the_current_trailer_list_query(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'trailer-1', 'name' => 'Trailer A']],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $page = (new TrailersResource($this->samsara))
            ->query()
            ->whereTag(['tag-1', 'tag-2'])
            ->whereParentTag(['parent-1', 'parent-2'])
            ->after('trailer-cursor')
            ->paginate(50);

        $this->assertInstanceOf(Trailer::class, $page->items()->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/trailers'
            && $request->data() === [
                'tagIds'       => 'tag-1,tag-2',
                'parentTagIds' => 'parent-1,parent-2',
                'limit'        => 50,
                'after'        => 'trailer-cursor',
            ]
        );
    }
}
