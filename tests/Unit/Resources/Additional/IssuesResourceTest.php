<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Additional;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Additional\IssuesResource;

/**
 * Unit tests for the IssuesResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class IssuesResourceTest extends TestCase
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
    public function it_can_get_all_issues(): void
    {
        $this->http->fake([
            '*/issues' => $this->http->response([
                'data' => [
                    ['id' => 'issue-1', 'status' => 'open'],
                    ['id' => 'issue-2', 'status' => 'resolved'],
                ],
            ], 200),
        ]);

        $resource = new IssuesResource($this->samsara);

        $result = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_can_get_stream_builder(): void
    {
        $resource = new IssuesResource($this->samsara);

        $builder = $resource->stream();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_update_issue(): void
    {
        $this->http->fake([
            '*/issues/issue-123' => $this->http->response([
                'data' => [
                    'id'     => 'issue-123',
                    'status' => 'resolved',
                ],
            ], 200),
        ]);

        $resource = new IssuesResource($this->samsara);

        $result = $resource->update('issue-123', ['status' => 'resolved']);

        $this->assertInstanceOf(Entity::class, $result);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new IssuesResource($this->samsara);

        $this->assertSame('/issues', $resource->getEndpoint());
    }
}
