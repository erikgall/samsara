<?php

namespace Samsara\Tests\Unit\Resources\Safety;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\Safety\SafetyEvent;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Safety\SafetyEventsResource;

/**
 * Unit tests for the SafetyEventsResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SafetyEventsResourceTest extends TestCase
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
    public function it_can_return_audit_logs_query_builder(): void
    {
        $resource = new SafetyEventsResource($this->samsara);
        $query = $resource->auditLogs();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_query_builder(): void
    {
        $resource = new SafetyEventsResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new SafetyEventsResource($this->samsara);

        $this->assertSame('/fleet/safety-events', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new SafetyEventsResource($this->samsara);

        $this->assertSame(SafetyEvent::class, $resource->getEntityClass());
    }
}
