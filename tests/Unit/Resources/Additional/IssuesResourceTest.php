<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Additional\IssuesResource;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Exceptions\UnsupportedOperationException;

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
    public function it_rejects_the_deferred_generic_update_without_sending_a_request(): void
    {
        $this->http->fake();

        $resource = new IssuesResource($this->samsara);

        try {
            $resource->update('issue-123', ['status' => 'resolved']);
            $this->fail('Expected the generic update operation to be rejected.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame('update', $exception->getContext()['operation']);
        }

        $this->http->assertNothingSent();
    }

    #[Test]
    public function it_rejects_unfiltered_generic_all_without_sending_a_request(): void
    {
        $this->http->fake();

        $resource = new IssuesResource($this->samsara);

        try {
            $resource->all();
            $this->fail('Expected the generic all operation to be rejected.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame('all', $exception->getContext()['operation']);
        }

        $this->http->assertNothingSent();
    }
}
