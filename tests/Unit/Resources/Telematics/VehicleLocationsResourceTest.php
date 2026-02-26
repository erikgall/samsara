<?php

namespace Samsara\Tests\Unit\Resources\Telematics;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Telematics\VehicleLocationsResource;

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
    public function it_feed_hits_correct_endpoint(): void
    {
        $this->http->fake([
            'samsara.com/fleet/vehicles/locations/feed*' => $this->http->response([
                'data' => [
                    ['id' => '2', 'latitude' => 40.7128, 'longitude' => -74.0060],
                ],
            ]),
            '*' => $this->http->response(['data' => []], 200),
        ]);

        $resource = new VehicleLocationsResource($this->samsara);
        $locations = $resource->feed()->get();

        $this->assertInstanceOf(EntityCollection::class, $locations);
        $this->assertCount(1, $locations);
        $this->assertSame('2', $locations->first()->getId());
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new VehicleLocationsResource($this->samsara);

        $this->assertSame('/fleet/vehicles/locations', $resource->getEndpoint());
    }

    #[Test]
    public function it_history_hits_correct_endpoint(): void
    {
        $this->http->fake([
            'samsara.com/fleet/vehicles/locations/history*' => $this->http->response([
                'data' => [
                    ['id' => '1', 'latitude' => 37.7749, 'longitude' => -122.4194],
                ],
            ]),
            '*' => $this->http->response(['data' => []], 200),
        ]);

        $resource = new VehicleLocationsResource($this->samsara);
        $locations = $resource->history()->get();

        $this->assertInstanceOf(EntityCollection::class, $locations);
        $this->assertCount(1, $locations);
        $this->assertSame('1', $locations->first()->getId());
    }
}
