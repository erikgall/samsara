<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Telematics;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Telematics\VehicleLocationsResource;

/**
 * Unit tests for the VehicleLocationsResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VehicleLocationsResourceTest extends TestCase
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
        $resource = new VehicleLocationsResource($this->samsara);

        $this->assertInstanceOf(VehicleLocationsResource::class, $resource);
    }

    #[Test]
    public function it_can_return_current_query_builder(): void
    {
        $resource = new VehicleLocationsResource($this->samsara);
        $query = $resource->current();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_feed_query_builder(): void
    {
        $resource = new VehicleLocationsResource($this->samsara);
        $query = $resource->feed();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_history_query_builder(): void
    {
        $resource = new VehicleLocationsResource($this->samsara);
        $query = $resource->history();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_query_builder(): void
    {
        $resource = new VehicleLocationsResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new VehicleLocationsResource($this->samsara);

        $this->assertSame('/fleet/vehicles/locations', $resource->getEndpoint());
    }
}
