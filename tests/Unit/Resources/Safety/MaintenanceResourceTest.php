<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Safety;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\EntityCollection;
use ErikGall\Samsara\Data\Maintenance\Dvir;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Safety\MaintenanceResource;

/**
 * Unit tests for the MaintenanceResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class MaintenanceResourceTest extends TestCase
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
        $resource = new MaintenanceResource($this->samsara);

        $this->assertInstanceOf(MaintenanceResource::class, $resource);
    }

    #[Test]
    public function it_can_create_dvir(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'           => 'dvir-1',
                    'type'         => 'preTrip',
                    'safetyStatus' => 'safe',
                ],
            ]),
        ]);

        $resource = new MaintenanceResource($this->samsara);
        $dvir = $resource->createDvir([
            'type'      => 'preTrip',
            'vehicleId' => 'vehicle-123',
        ]);

        $this->assertInstanceOf(Dvir::class, $dvir);
        $this->assertSame('preTrip', $dvir->type);
    }

    #[Test]
    public function it_can_get_all_dvirs(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    [
                        'id'           => 'dvir-1',
                        'type'         => 'preTrip',
                        'safetyStatus' => 'safe',
                    ],
                    [
                        'id'           => 'dvir-2',
                        'type'         => 'postTrip',
                        'safetyStatus' => 'unsafe',
                    ],
                ],
            ]),
        ]);

        $resource = new MaintenanceResource($this->samsara);
        $dvirs = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $dvirs);
        $this->assertCount(2, $dvirs);
        $this->assertInstanceOf(Dvir::class, $dvirs->first());
    }

    #[Test]
    public function it_can_return_defects_query_builder(): void
    {
        $resource = new MaintenanceResource($this->samsara);
        $query = $resource->defects();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_dvirs_query_builder(): void
    {
        $resource = new MaintenanceResource($this->samsara);
        $query = $resource->dvirs();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_query_builder(): void
    {
        $resource = new MaintenanceResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new MaintenanceResource($this->samsara);

        $this->assertSame('/dvirs/stream', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new MaintenanceResource($this->samsara);

        $this->assertSame(Dvir::class, $resource->getEntityClass());
    }
}
