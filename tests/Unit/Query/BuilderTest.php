<?php

namespace Samsara\Tests\Unit\Query;

use Carbon\Carbon;
use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Resources\Resource;
use Samsara\Data\EntityCollection;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\PendingRequest;

/**
 * Unit tests for the Query Builder class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class BuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();
    }

    #[Test]
    public function it_can_add_custom_where_clause(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->where('customField', 'customValue');

        $this->assertSame($builder, $result);
        $this->assertSame(['customField' => 'customValue'], $builder->buildQuery());
    }

    #[Test]
    public function it_can_be_instantiated_with_resource(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_chain_multiple_filters(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $builder->whereTag('tag-1')
            ->whereVehicle('vehicle-1')
            ->limit(10)
            ->between('2024-01-01T00:00:00Z', '2024-01-31T23:59:59Z');

        $query = $builder->buildQuery();

        $this->assertSame(['tag-1'], $query['tagIds']);
        $this->assertSame(['vehicle-1'], $query['vehicleIds']);
        $this->assertSame(10, $query['limit']);
        $this->assertSame('2024-01-01T00:00:00Z', $query['startTime']);
        $this->assertSame('2024-01-31T23:59:59Z', $query['endTime']);
    }

    #[Test]
    public function it_can_expand_relations(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->expand(['driver', 'vehicle']);

        $this->assertSame($builder, $result);
        $this->assertSame(['expand' => ['driver', 'vehicle']], $builder->buildQuery());
    }

    #[Test]
    public function it_can_filter_by_attribute_value_ids(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->whereAttribute(['attr-1', 'attr-2']);

        $this->assertSame($builder, $result);
        $this->assertSame(['attributeValueIds' => ['attr-1', 'attr-2']], $builder->buildQuery());
    }

    #[Test]
    public function it_can_filter_by_created_after(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->createdAfter('2024-01-15T00:00:00Z');

        $this->assertSame($builder, $result);
        $this->assertSame(['createdAfterTime' => '2024-01-15T00:00:00Z'], $builder->buildQuery());
    }

    #[Test]
    public function it_can_filter_by_driver_ids(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->whereDriver(['driver-1', 'driver-2']);

        $this->assertSame($builder, $result);
        $this->assertSame(['driverIds' => ['driver-1', 'driver-2']], $builder->buildQuery());
    }

    #[Test]
    public function it_can_filter_by_parent_tag_ids(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->whereParentTag('parent-tag-123');

        $this->assertSame($builder, $result);
        $this->assertSame(['parentTagIds' => ['parent-tag-123']], $builder->buildQuery());
    }

    #[Test]
    public function it_can_filter_by_tag_ids_with_array(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->whereTag(['tag-1', 'tag-2']);

        $this->assertSame($builder, $result);
        $this->assertSame(['tagIds' => ['tag-1', 'tag-2']], $builder->buildQuery());
    }

    #[Test]
    public function it_can_filter_by_tag_ids_with_string(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->whereTag('tag-123');

        $this->assertSame($builder, $result);
        $this->assertSame(['tagIds' => ['tag-123']], $builder->buildQuery());
    }

    #[Test]
    public function it_can_filter_by_updated_after_with_datetime(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $date = Carbon::parse('2024-01-15T12:00:00Z');
        $result = $builder->updatedAfter($date);

        $this->assertSame($builder, $result);
        $query = $builder->buildQuery();
        $this->assertArrayHasKey('updatedAfterTime', $query);
        $this->assertStringContainsString('2024-01-15', $query['updatedAfterTime']);
    }

    #[Test]
    public function it_can_filter_by_updated_after_with_string(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->updatedAfter('2024-01-15T00:00:00Z');

        $this->assertSame($builder, $result);
        $this->assertSame(['updatedAfterTime' => '2024-01-15T00:00:00Z'], $builder->buildQuery());
    }

    #[Test]
    public function it_can_filter_by_vehicle_ids(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->whereVehicle('vehicle-123');

        $this->assertSame($builder, $result);
        $this->assertSame(['vehicleIds' => ['vehicle-123']], $builder->buildQuery());
    }

    #[Test]
    public function it_can_set_cursor_with_after(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->after('cursor-abc-123');

        $this->assertSame($builder, $result);
        $this->assertSame(['after' => 'cursor-abc-123'], $builder->buildQuery());
    }

    #[Test]
    public function it_can_set_decorations(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->withDecorations(['speed', 'address']);

        $this->assertSame($builder, $result);
        $this->assertSame(['decorations' => ['speed', 'address']], $builder->buildQuery());
    }

    #[Test]
    public function it_can_set_end_time(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->endTime('2024-01-31T23:59:59Z');

        $this->assertSame($builder, $result);
        $this->assertSame(['endTime' => '2024-01-31T23:59:59Z'], $builder->buildQuery());
    }

    #[Test]
    public function it_can_set_limit(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->limit(50);

        $this->assertSame($builder, $result);
        $this->assertSame(['limit' => 50], $builder->buildQuery());
    }

    #[Test]
    public function it_can_set_start_time(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->startTime('2024-01-01T00:00:00Z');

        $this->assertSame($builder, $result);
        $this->assertSame(['startTime' => '2024-01-01T00:00:00Z'], $builder->buildQuery());
    }

    #[Test]
    public function it_can_set_time_range_with_between(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->between('2024-01-01T00:00:00Z', '2024-01-31T23:59:59Z');

        $this->assertSame($builder, $result);
        $query = $builder->buildQuery();
        $this->assertSame('2024-01-01T00:00:00Z', $query['startTime']);
        $this->assertSame('2024-01-31T23:59:59Z', $query['endTime']);
    }

    #[Test]
    public function it_can_use_take_alias_for_limit(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->take(25);

        $this->assertSame($builder, $result);
        $this->assertSame(['limit' => 25], $builder->buildQuery());
    }

    #[Test]
    public function it_executes_first_and_returns_single_entity(): void
    {
        $fakeHttp = new \Illuminate\Http\Client\Factory;
        $fakeHttp->fake([
            '*' => $fakeHttp->response([
                'data' => [
                    ['id' => '1', 'name' => 'First'],
                    ['id' => '2', 'name' => 'Second'],
                ],
            ], 200),
        ]);

        $samsara = new Samsara('test-token');
        $samsara->setHttpFactory($fakeHttp);
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->first();

        $this->assertInstanceOf(Entity::class, $result);
        $this->assertSame('1', $result->getId());
    }

    #[Test]
    public function it_executes_get_and_returns_entity_collection(): void
    {
        $fakeHttp = new \Illuminate\Http\Client\Factory;
        $fakeHttp->fake([
            '*' => $fakeHttp->response([
                'data' => [
                    ['id' => '1', 'name' => 'First'],
                    ['id' => '2', 'name' => 'Second'],
                ],
            ], 200),
        ]);

        $samsara = new Samsara('test-token');
        $samsara->setHttpFactory($fakeHttp);
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->get();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_formats_datetime_correctly(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $date = Carbon::create(2024, 6, 15, 10, 30, 0, 'UTC');
        $builder->startTime($date);

        $query = $builder->buildQuery();
        // Timestamps are formatted in UTC with Z suffix per Samsara API requirements
        $this->assertSame('2024-06-15T10:30:00Z', $query['startTime']);
    }

    #[Test]
    public function it_formats_single_type_as_string(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->types('gps');

        $this->assertSame($builder, $result);
        $this->assertSame('gps', $builder->buildQuery()['types']);
    }

    #[Test]
    public function it_formats_types_as_comma_separated_string(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->types(['gps', 'obdOdometerMeters']);

        $this->assertSame($builder, $result);
        $this->assertSame('gps,obdOdometerMeters', $builder->buildQuery()['types']);
    }

    #[Test]
    public function it_returns_null_when_first_has_no_results(): void
    {
        $fakeHttp = new \Illuminate\Http\Client\Factory;
        $fakeHttp->fake([
            '*' => $fakeHttp->response([
                'data' => [],
            ], 200),
        ]);

        $samsara = new Samsara('test-token');
        $samsara->setHttpFactory($fakeHttp);
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->first();

        $this->assertNull($result);
    }
}

/**
 * Test resource for Builder tests.
 */
class TestQueryResource extends Resource
{
    protected string $endpoint = '/test-query-endpoint';

    protected string $entity = Entity::class;

    public function client(): PendingRequest
    {
        return $this->samsara->client();
    }
}
