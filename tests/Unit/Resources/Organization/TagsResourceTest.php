<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Organization;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Data\Tag\Tag;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Organization\TagsResource;

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
    public function it_can_return_query_builder(): void
    {
        $resource = new TagsResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
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
}
