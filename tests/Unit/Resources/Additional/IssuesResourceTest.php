<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Illuminate\Http\Client\Request;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Additional\IssuesResource;
use Illuminate\Http\Client\Factory as HttpFactory;

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
        $this->http->preventStrayRequests();
        $this->samsara = new Samsara('test-token');
        $this->samsara->setHttpFactory($this->http);
    }

    #[Test]
    public function it_can_get_stream_builder(): void
    {
        $resource = new IssuesResource($this->samsara);

        $builder = $resource->stream();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new IssuesResource($this->samsara);

        $this->assertSame('/issues', $resource->getEndpoint());
    }

    #[Test]
    public function it_updates_an_issue_at_the_collection_endpoint(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['data' => [
                'id'     => 'issue-123',
                'status' => 'resolved',
            ]]),
        ]);

        $issue = (new IssuesResource($this->samsara))->updateIssue([
            'id'     => 'issue-123',
            'status' => 'resolved',
        ]);

        $this->assertInstanceOf(Entity::class, $issue);
        $this->assertSame('issue-123', $issue->id);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
            && parse_url($request->url(), PHP_URL_PATH) === '/issues'
            && $request->data() === ['id' => 'issue-123', 'status' => 'resolved']);
    }
}
