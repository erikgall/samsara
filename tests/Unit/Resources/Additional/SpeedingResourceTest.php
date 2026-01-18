<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Additional\SpeedingResource;

/**
 * Unit tests for the SpeedingResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SpeedingResourceTest extends TestCase
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
    public function it_can_get_intervals_stream_builder(): void
    {
        $resource = new SpeedingResource($this->samsara);

        $builder = $resource->intervalsStream();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new SpeedingResource($this->samsara);

        $this->assertSame('/speeding-intervals/stream', $resource->getEndpoint());
    }
}
