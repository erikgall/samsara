<?php

namespace Samsara\Tests\Unit\Resources\Telematics;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Vehicle\VehicleStats;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Telematics\VehicleStatsResource;

/**
 * Unit tests for the VehicleStatsResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VehicleStatsResourceTest extends TestCase
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
        $resource = new VehicleStatsResource($this->samsara);

        $this->assertInstanceOf(VehicleStatsResource::class, $resource);
    }

    #[Test]
    public function it_can_create_engine_states_query_with_types(): void
    {
        $resource = new VehicleStatsResource($this->samsara);
        $query = $resource->engineStates();

        $this->assertInstanceOf(Builder::class, $query);
        $queryParams = $query->buildQuery();
        $this->assertStringContainsString('engineStates', $queryParams['types'] ?? '');
    }

    #[Test]
    public function it_can_create_fuel_percents_query_with_types(): void
    {
        $resource = new VehicleStatsResource($this->samsara);
        $query = $resource->fuelPercents();

        $this->assertInstanceOf(Builder::class, $query);
        $queryParams = $query->buildQuery();
        $this->assertStringContainsString('fuelPercents', $queryParams['types'] ?? '');
    }

    #[Test]
    public function it_can_create_gps_query_with_types(): void
    {
        $resource = new VehicleStatsResource($this->samsara);
        $query = $resource->gps();

        $this->assertInstanceOf(Builder::class, $query);
        $queryParams = $query->buildQuery();
        $this->assertStringContainsString('gps', $queryParams['types'] ?? '');
    }

    #[Test]
    public function it_can_create_odometer_query_with_types(): void
    {
        $resource = new VehicleStatsResource($this->samsara);
        $query = $resource->odometer();

        $this->assertInstanceOf(Builder::class, $query);
        $queryParams = $query->buildQuery();
        $this->assertStringContainsString('obdOdometerMeters', $queryParams['types'] ?? '');
    }

    #[Test]
    public function it_can_get_all_stats(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    ['id' => '1', 'name' => 'Truck 001', 'gps' => ['latitude' => 37.7749, 'longitude' => -122.4194]],
                    ['id' => '2', 'name' => 'Truck 002', 'fuelPercent' => ['value' => 75]],
                ],
            ]),
        ]);

        $resource = new VehicleStatsResource($this->samsara);
        $stats = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $stats);
        $this->assertCount(2, $stats);
        $this->assertInstanceOf(VehicleStats::class, $stats->first());
        $this->assertSame('Truck 001', $stats->first()->name);
    }

    #[Test]
    public function it_can_return_current_stats_query_builder(): void
    {
        $resource = new VehicleStatsResource($this->samsara);
        $query = $resource->current();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_feed_query_builder(): void
    {
        $resource = new VehicleStatsResource($this->samsara);
        $query = $resource->feed();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_history_query_builder(): void
    {
        $resource = new VehicleStatsResource($this->samsara);
        $query = $resource->history();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_query_builder(): void
    {
        $resource = new VehicleStatsResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new VehicleStatsResource($this->samsara);

        $this->assertSame('/fleet/vehicles/stats', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new VehicleStatsResource($this->samsara);

        $this->assertSame(VehicleStats::class, $resource->getEntityClass());
    }
}
