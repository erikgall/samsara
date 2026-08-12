<?php

namespace Samsara\Tests\Unit\Resources\Safety;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\Maintenance\Dvir;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Safety\MaintenanceResource;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Exceptions\UnsupportedOperationException;

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
                    'type'         => 'mechanic',
                    'safetyStatus' => 'safe',
                ],
            ]),
        ]);

        $resource = new HydrationAwareMaintenanceResource($this->samsara);
        $dvir = $resource->createDvir([
            'authorId'     => 'author-123',
            'safetyStatus' => 'safe',
            'type'         => 'mechanic',
            'vehicleId'    => 'vehicle-123',
        ]);

        $this->assertInstanceOf(Dvir::class, $dvir);
        $this->assertSame('mechanic', $dvir->type);
        $this->assertTrue($resource->mappedCreateResponse);
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

    #[Test]
    public function it_rejects_the_unsupported_generic_all_operation_before_http(): void
    {
        $this->http->fake();

        $resource = new MaintenanceResource($this->samsara);

        try {
            $resource->all();
            $this->fail('Expected the generic all operation to be rejected.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame('all', $exception->getContext()['operation']);
            $this->assertSame(MaintenanceResource::class, $exception->getContext()['resource']);
        }

        $this->http->assertNothingSent();
    }
}

class HydrationAwareMaintenanceResource extends MaintenanceResource
{
    public bool $mappedCreateResponse = false;

    protected function mapToEntity(array $data): object
    {
        $this->mappedCreateResponse = true;

        return parent::mapToEntity($data);
    }
}
