<?php

namespace Samsara\Tests\Unit\Resources\Organization;

use Samsara\Samsara;
use Samsara\Data\Tag\Tag;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Request;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Organization\TagsResource;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Unit tests for the TagsResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TagsResourceTest extends TestCase
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
        $resource = new TagsResource($this->samsara);

        $this->assertInstanceOf(TagsResource::class, $resource);
    }

    #[Test]
    public function it_can_create_tag(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => 'tag-1',
                    'name' => 'Fleet A',
                ],
            ]),
        ]);

        $resource = new TagsResource($this->samsara);
        $tag = $resource->create([
            'name' => 'Fleet A',
        ]);

        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertSame('Fleet A', $tag->name);
    }

    #[Test]
    public function it_can_delete_tag(): void
    {
        $this->http->fake([
            '*' => $this->http->response([], 204),
        ]);

        $resource = new TagsResource($this->samsara);
        $result = $resource->delete('tag-1');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_find_tag_by_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => 'tag-1',
                    'name' => 'Fleet B',
                ],
            ]),
        ]);

        $resource = new TagsResource($this->samsara);
        $tag = $resource->find('tag-1');

        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertSame('Fleet B', $tag->name);
    }

    #[Test]
    public function it_can_get_all_tags(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    [
                        'id'   => 'tag-1',
                        'name' => 'Fleet A',
                    ],
                    [
                        'id'   => 'tag-2',
                        'name' => 'Fleet B',
                    ],
                ],
            ]),
        ]);

        $resource = new TagsResource($this->samsara);
        $tags = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $tags);
        $this->assertCount(2, $tags);
        $this->assertInstanceOf(Tag::class, $tags->first());
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new TagsResource($this->samsara);

        $this->assertSame('/tags', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new TagsResource($this->samsara);

        $this->assertSame(Tag::class, $resource->getEntityClass());
    }

    #[Test]
    public function it_terminal_executes_the_current_tag_list_query(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'tag-1', 'name' => 'Fleet A']],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $page = (new TagsResource($this->samsara))
            ->query()
            ->after('tag-cursor')
            ->paginate(50);

        $this->assertInstanceOf(Tag::class, $page->items()->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/tags'
            && $request->data() === ['limit' => 50, 'after' => 'tag-cursor']
        );
    }
}
