<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Additional\IdlingResource;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Unit tests for the IdlingResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class IdlingResourceTest extends TestCase
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
    public function it_can_get_events_builder(): void
    {
        $resource = new IdlingResource($this->samsara);

        $builder = $resource->events();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new IdlingResource($this->samsara);

        $this->assertSame('/idling/events', $resource->getEndpoint());
    }
}
