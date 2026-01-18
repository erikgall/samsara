<?php

namespace ErikGall\Samsara\Tests\Unit\Resources;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Resources\Resource;
use Illuminate\Http\Client\PendingRequest;
use ErikGall\Samsara\Data\EntityCollection;

/**
 * Unit tests for the Resource base class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class ResourceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();
    }

    #[Test]
    public function it_maps_data_to_entities_collection(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestResource($samsara);

        $data = [
            ['id' => '1', 'name' => 'First'],
            ['id' => '2', 'name' => 'Second'],
        ];

        $collection = $resource->testMapToEntities($data);

        $this->assertInstanceOf(EntityCollection::class, $collection);
        $this->assertCount(2, $collection);
    }

    #[Test]
    public function it_maps_data_to_entity(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestResource($samsara);

        $entity = $resource->testMapToEntity(['id' => '123', 'name' => 'Test']);

        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertSame('123', $entity->getId());
        $this->assertSame('Test', $entity->name);
    }

    #[Test]
    public function it_returns_endpoint(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestResource($samsara);

        $this->assertSame('/test-endpoint', $resource->getEndpoint());
    }

    #[Test]
    public function it_returns_pending_request_from_client(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestResource($samsara);

        $this->assertInstanceOf(PendingRequest::class, $resource->client());
    }

    #[Test]
    public function it_stores_samsara_instance(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestResource($samsara);

        $this->assertInstanceOf(Samsara::class, $resource->getSamsara());
    }
}

/**
 * Test resource implementation.
 */
class TestResource extends Resource
{
    protected string $endpoint = '/test-endpoint';

    protected string $entity = Entity::class;

    public function getSamsara(): Samsara
    {
        return $this->samsara;
    }

    public function testMapToEntities(array $data): EntityCollection
    {
        return $this->mapToEntities($data);
    }

    public function testMapToEntity(array $data): object
    {
        return $this->mapToEntity($data);
    }
}
