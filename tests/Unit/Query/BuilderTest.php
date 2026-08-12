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
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Exceptions\ServerException;
use Illuminate\Http\Client\PendingRequest;
use Samsara\Exceptions\ValidationException;
use Samsara\Query\Pagination\CursorPaginator;
use PHPUnit\Framework\Attributes\DataProvider;
use Samsara\Exceptions\AuthenticationException;

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

        $this->assertSame('tag-1', $query['tagIds']);
        $this->assertSame('vehicle-1', $query['vehicleIds']);
        $this->assertSame(10, $query['limit']);
        $this->assertSame('2024-01-01T00:00:00Z', $query['startTime']);
        $this->assertSame('2024-01-31T23:59:59Z', $query['endTime']);
    }

    #[Test]
    #[DataProvider('pageSizeDisabledMultiPageTerminals')]
    public function it_can_customize_the_cursor_request_parameter(string $terminal): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fakeSequence()
            ->push([
                'data'       => [],
                'pagination' => ['endCursor' => 'cursor-1', 'hasNextPage' => true],
            ])
            ->push([
                'data'       => [],
                'pagination' => ['endCursor' => null, 'hasNextPage' => false],
            ]);

        $samsara = new Samsara('test-token', ['retry' => 0]);
        $samsara->setHttpFactory($http);
        $builder = (new Builder(new TestQueryResource($samsara)))
            ->withoutDefaultPageSize()
            ->withCursorParameter('startingAfter');

        match ($terminal) {
            'paginator' => $builder->paginate()->nextPage(),
            'lazy'      => $builder->lazy()->all(),
        };

        $requests = $http->recorded();

        $this->assertCount(2, $requests);
        $this->assertArrayNotHasKey('after', $requests[1][0]->data());
        $this->assertSame('cursor-1', $requests[1][0]->data()['startingAfter']);
    }

    #[Test]
    #[DataProvider('pageSizeDisabledTerminals')]
    public function it_can_disable_the_implicit_default_page_size(string $terminal): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake(['*' => $http->response([
            'data'       => [],
            'pagination' => ['endCursor' => null, 'hasNextPage' => false],
        ])]);

        $samsara = new Samsara('test-token', ['retry' => 0, 'per_page' => 50]);
        $samsara->setHttpFactory($http);
        $builder = new Builder(new TestQueryResource($samsara));

        $result = $builder->withoutDefaultPageSize();

        $this->assertSame($builder, $result);

        match ($terminal) {
            'getWithPagination' => $builder->getWithPagination(),
            'paginate'          => $builder->paginate(),
            'lazy'              => $builder->lazy()->all(),
        };

        $http->assertSentCount(1);
        $http->assertSent(fn ($request) => ! array_key_exists('limit', $request->data()));
    }

    #[Test]
    public function it_can_expand_relations(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->expand('vehicle');

        $this->assertSame($builder, $result);
        $this->assertSame(['expand' => 'vehicle'], $builder->buildQuery());
    }

    #[Test]
    public function it_can_filter_by_attribute_value_ids(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->whereAttribute(['attr-1', 'attr-2']);

        $this->assertSame($builder, $result);
        $this->assertSame(['attributeValueIds' => 'attr-1,attr-2'], $builder->buildQuery());
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
        $this->assertSame(['driverIds' => 'driver-1,driver-2'], $builder->buildQuery());
    }

    #[Test]
    public function it_can_filter_by_parent_tag_ids(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->whereParentTag('parent-tag-123');

        $this->assertSame($builder, $result);
        $this->assertSame(['parentTagIds' => 'parent-tag-123'], $builder->buildQuery());
    }

    #[Test]
    public function it_can_filter_by_tag_ids_with_array(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->whereTag(['tag-1', 'tag-2']);

        $this->assertSame($builder, $result);
        $this->assertSame(['tagIds' => 'tag-1,tag-2'], $builder->buildQuery());
    }

    #[Test]
    public function it_can_filter_by_tag_ids_with_string(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $result = $builder->whereTag('tag-123');

        $this->assertSame($builder, $result);
        $this->assertSame(['tagIds' => 'tag-123'], $builder->buildQuery());
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
        $this->assertSame(['vehicleIds' => 'vehicle-123'], $builder->buildQuery());
    }

    #[Test]
    #[DataProvider('mappingTerminals')]
    public function it_can_read_a_custom_response_collection_key(string $terminal): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake(['*' => $http->response([
            'assets'     => [['id' => 'asset-1']],
            'pagination' => ['endCursor' => null, 'hasNextPage' => false],
        ])]);

        $samsara = new Samsara('test-token', ['retry' => 0]);
        $samsara->setHttpFactory($http);
        $builder = (new Builder(new TestQueryResource($samsara)))
            ->withResponseCollectionKey('assets');

        $items = match ($terminal) {
            'get'      => $builder->get()->all(),
            'paginate' => $builder->paginate()->items()->all(),
            'lazy'     => $builder->lazy()->all(),
        };

        $this->assertCount(1, $items);
        $this->assertSame('asset-1', $items[0]->getId());
    }

    #[Test]
    public function it_can_read_a_nested_response_collection_key(): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake(['*' => $http->response([
            'data' => [
                'media' => [['id' => 'media-1']],
            ],
        ])]);

        $samsara = new Samsara('test-token', ['retry' => 0]);
        $samsara->setHttpFactory($http);

        $items = (new Builder(new TestQueryResource($samsara)))
            ->withResponseCollectionKey('data.media')
            ->get();

        $this->assertCount(1, $items);
        $this->assertSame('media-1', $items->first()->getId());
    }

    #[Test]
    #[DataProvider('mappingTerminals')]
    public function it_can_select_an_entity_mapper_for_a_specific_endpoint(string $terminal): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake(['*' => $http->response([
            'data'       => [['id' => 'mapped-1']],
            'pagination' => ['endCursor' => null, 'hasNextPage' => false],
        ])]);

        $samsara = new Samsara('test-token', ['retry' => 0]);
        $samsara->setHttpFactory($http);
        $builder = (new Builder(new TestQueryResource($samsara), '/custom/mapped'))
            ->usingEntity(TestMappedEntity::class);

        $items = match ($terminal) {
            'get'      => $builder->get()->all(),
            'paginate' => $builder->paginate()->items()->all(),
            'lazy'     => $builder->lazy(1)->all(),
        };

        $this->assertInstanceOf(TestMappedEntity::class, $items[0]);
        $http->assertSent(fn ($request) => str_starts_with(
            $request->url(),
            'https://api.samsara.com/custom/mapped'
        ));
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
        $this->assertSame(['decorations' => 'speed,address'], $builder->buildQuery());
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
    public function it_can_use_a_response_mapper_callback_for_a_specific_endpoint(): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake(['*' => $http->response(['data' => [['id' => 'callback-1']]])]);

        $samsara = new Samsara('test-token', ['retry' => 0]);
        $samsara->setHttpFactory($http);
        $builder = (new Builder(new TestQueryResource($samsara), '/custom/callback'))
            ->mapUsing(fn (array $data) => TestMappedEntity::make([
                ...$data,
                'mappedBy' => 'callback',
            ]));

        $result = $builder->get()->first();

        $this->assertInstanceOf(TestMappedEntity::class, $result);
        $this->assertSame('callback-1', $result->getId());
        $this->assertSame('callback', $result->mappedBy);
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
    public function it_executes_get_with_custom_endpoint(): void
    {
        $fakeHttp = new \Illuminate\Http\Client\Factory;
        $fakeHttp->fake([
            'samsara.com/custom/endpoint*' => $fakeHttp->response([
                'data' => [
                    ['id' => '1', 'name' => 'Custom'],
                ],
            ], 200),
            '*' => $fakeHttp->response([
                'data' => [],
            ], 200),
        ]);

        $samsara = new Samsara('test-token');
        $samsara->setHttpFactory($fakeHttp);
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource, '/custom/endpoint');

        $result = $builder->get();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertSame('1', $result->first()->getId());
    }

    #[Test]
    public function it_executes_get_with_pagination_with_custom_endpoint(): void
    {
        $fakeHttp = new \Illuminate\Http\Client\Factory;
        $fakeHttp->fake([
            'samsara.com/custom/paginated*' => $fakeHttp->response([
                'data' => [
                    ['id' => '1', 'name' => 'Paginated'],
                ],
                'pagination' => [
                    'endCursor'   => 'cursor-abc',
                    'hasNextPage' => false,
                ],
            ], 200),
            '*' => $fakeHttp->response(['data' => []], 200),
        ]);

        $samsara = new Samsara('test-token');
        $samsara->setHttpFactory($fakeHttp);
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource, '/custom/paginated');

        $result = $builder->getWithPagination();

        $this->assertInstanceOf(CursorPaginator::class, $result);
        $this->assertCount(1, $result);
    }

    #[Test]
    public function it_executes_lazy_with_custom_endpoint(): void
    {
        $fakeHttp = new \Illuminate\Http\Client\Factory;
        $fakeHttp->fake([
            'samsara.com/custom/lazy*' => $fakeHttp->response([
                'data' => [
                    ['id' => '1', 'name' => 'Lazy'],
                ],
                'pagination' => [
                    'endCursor'   => null,
                    'hasNextPage' => false,
                ],
            ], 200),
            '*' => $fakeHttp->response(['data' => []], 200),
        ]);

        $samsara = new Samsara('test-token');
        $samsara->setHttpFactory($fakeHttp);
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource, '/custom/lazy');

        $result = $builder->lazy();

        $this->assertInstanceOf(LazyCollection::class, $result);
        $items = $result->all();
        $this->assertCount(1, $items);
        $this->assertSame('1', $items[0]->getId());
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
    #[DataProvider('pageSizeDisabledMultiPageTerminals')]
    public function it_keeps_the_implicit_page_size_disabled_on_subsequent_pages(string $terminal): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fakeSequence()
            ->push([
                'data'       => [],
                'pagination' => ['endCursor' => 'cursor-1', 'hasNextPage' => true],
            ])
            ->push([
                'data'       => [],
                'pagination' => ['endCursor' => null, 'hasNextPage' => false],
            ]);

        $samsara = new Samsara('test-token', ['retry' => 0, 'per_page' => 50]);
        $samsara->setHttpFactory($http);
        $builder = (new Builder(new TestQueryResource($samsara)))
            ->withoutDefaultPageSize();

        match ($terminal) {
            'paginator' => $builder->paginate()->nextPage(),
            'lazy'      => $builder->lazy()->all(),
        };

        $requests = $http->recorded();

        $this->assertCount(2, $requests);
        $this->assertSame([], $requests[0][0]->data());
        $this->assertSame(['after' => 'cursor-1'], $requests[1][0]->data());
    }

    #[Test]
    public function it_maps_query_error_context_to_the_custom_endpoint(): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake([
            '*' => $http->response(['message' => 'Invalid API token'], 401),
        ]);

        $samsara = new Samsara('test-token', ['retry' => 1]);
        $samsara->setHttpFactory($http);
        $builder = new Builder(new TestQueryResource($samsara), '/custom/query');

        try {
            $builder->get();
            $this->fail('Expected the query to throw an authentication exception.');
        } catch (AuthenticationException $exception) {
            $this->assertSame('/custom/query', $exception->getContext()['endpoint']);
        }
    }

    #[Test]
    #[DataProvider('errorMappingTerminals')]
    public function it_maps_query_error_responses_to_typed_exceptions(string $terminal): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake([
            '*' => $http->response(['message' => 'Invalid API token'], 401),
        ]);

        $samsara = new Samsara('test-token', ['retry' => 1]);
        $samsara->setHttpFactory($http);
        $builder = new Builder(new TestQueryResource($samsara));

        $this->expectException(AuthenticationException::class);

        match ($terminal) {
            'get'      => $builder->get(),
            'paginate' => $builder->paginate(),
            'lazy'     => $builder->lazy()->all(),
        };
    }

    #[Test]
    #[DataProvider('queryValidationResponsePaths')]
    public function it_maps_query_validation_responses_from_returned_and_post_retry_paths(
        int $status,
        int $attempts
    ): void {
        $body = [
            'message'   => 'Invalid query parameters',
            'requestId' => 'req-query-123',
            'errors'    => ['after' => ['The cursor is invalid.']],
        ];
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake(['*' => $http->response($body, $status)]);

        $samsara = new Samsara('test-token', ['retry' => $attempts]);
        $samsara->setHttpFactory($http);
        $builder = new Builder(new TestQueryResource($samsara), '/custom/validation-query');

        try {
            $builder->get();
            $this->fail('Expected a validation exception.');
        } catch (ValidationException $exception) {
            $this->assertSame($status, $exception->getCode());
            $this->assertSame($body['errors'], $exception->getErrors());
            $this->assertSame($body, $exception->getContext()['body']);
            $this->assertSame('/custom/validation-query', $exception->getContext()['endpoint']);
            $http->assertSentCount($attempts);
        }
    }

    #[Test]
    public function it_maps_the_final_error_after_configured_retries(): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake([
            '*' => $http->response(['message' => 'Service unavailable'], 503),
        ]);

        $samsara = new Samsara('test-token', ['retry' => 3]);
        $samsara->setHttpFactory($http);
        $builder = new Builder(new TestQueryResource($samsara), '/custom/retry-query');

        try {
            $builder->get();
            $this->fail('Expected the query to throw after exhausting retries.');
        } catch (ServerException $exception) {
            $this->assertSame('/custom/retry-query', $exception->getContext()['endpoint']);
            $http->assertSentCount(3);
        }
    }

    #[Test]
    #[DataProvider('paginatedTerminals')]
    public function it_preserves_a_custom_limit_filter_when_no_explicit_page_size_is_given(string $terminal): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake([
            '*' => $http->response([
                'data'       => [],
                'pagination' => ['endCursor' => null, 'hasNextPage' => false],
            ]),
        ]);

        $samsara = new Samsara('test-token', ['retry' => 0, 'per_page' => 50]);
        $samsara->setHttpFactory($http);
        $builder = (new Builder(new TestQueryResource($samsara)))->where('limit', 17);

        match ($terminal) {
            'paginate' => $builder->paginate(),
            'lazy'     => $builder->lazy()->all(),
        };

        $http->assertSent(fn ($request) => ($request->data()['limit'] ?? null) === 17);
    }

    #[Test]
    public function it_preserves_array_input_for_expand_as_a_comma_separated_string(): void
    {
        $samsara = new Samsara('test-token');
        $builder = new Builder(new TestQueryResource($samsara));

        $builder->expand(['route', 'vehicle']);

        $this->assertSame('route,vehicle', $builder->buildQuery()['expand']);
    }

    #[Test]
    #[DataProvider('explicitPageSizes')]
    public function it_preserves_explicit_page_sizes_when_the_default_is_disabled(
        string $selector,
        int $expectedLimit
    ): void {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake(['*' => $http->response([
            'data'       => [],
            'pagination' => ['endCursor' => null, 'hasNextPage' => false],
        ])]);

        $samsara = new Samsara('test-token', ['retry' => 0, 'per_page' => 50]);
        $samsara->setHttpFactory($http);
        $builder = new Builder(new TestQueryResource($samsara));

        match ($selector) {
            'limit' => $builder
                ->limit($expectedLimit)
                ->withoutDefaultPageSize()
                ->getWithPagination(),
            'paginate' => $builder
                ->withoutDefaultPageSize()
                ->paginate($expectedLimit),
            'lazy' => $builder
                ->withoutDefaultPageSize()
                ->lazy($expectedLimit)
                ->all(),
            'where' => $builder
                ->where('limit', $expectedLimit)
                ->withoutDefaultPageSize()
                ->paginate(),
        };

        $http->assertSentCount(1);
        $http->assertSent(fn ($request) => $request->data() === ['limit' => $expectedLimit]);
    }

    #[Test]
    public function it_restarts_lazy_pagination_without_mutating_the_original_builder(): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fakeSequence()
            ->push([
                'data'       => [['id' => 'page-1']],
                'pagination' => ['endCursor' => 'cursor-1', 'hasNextPage' => true],
            ])
            ->push([
                'data'       => [['id' => 'page-2']],
                'pagination' => ['endCursor' => null, 'hasNextPage' => false],
            ])
            ->push([
                'data'       => [['id' => 'page-1']],
                'pagination' => ['endCursor' => 'cursor-1', 'hasNextPage' => true],
            ])
            ->push([
                'data'       => [['id' => 'page-2']],
                'pagination' => ['endCursor' => null, 'hasNextPage' => false],
            ]);

        $samsara = new Samsara('test-token', ['retry' => 0]);
        $samsara->setHttpFactory($http);
        $builder = (new Builder(new TestQueryResource($samsara)))
            ->withoutDefaultPageSize();
        $lazy = $builder->lazy();

        $firstIteration = array_map(fn (Entity $item) => $item->getId(), $lazy->all());
        $secondIteration = array_map(fn (Entity $item) => $item->getId(), $lazy->all());
        $requests = $http->recorded();

        $this->assertSame(['page-1', 'page-2'], $firstIteration);
        $this->assertSame($firstIteration, $secondIteration);
        $this->assertArrayNotHasKey('after', $builder->buildQuery());
        $this->assertArrayNotHasKey('after', $requests[0][0]->data());
        $this->assertSame('cursor-1', $requests[1][0]->data()['after']);
        $this->assertArrayNotHasKey('after', $requests[2][0]->data());
        $this->assertSame('cursor-1', $requests[3][0]->data()['after']);
    }

    #[Test]
    public function it_retains_the_selected_entity_mapper_on_the_next_page(): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fakeSequence()
            ->push([
                'data'       => [['id' => 'mapped-1']],
                'pagination' => ['endCursor' => 'cursor-1', 'hasNextPage' => true],
            ])
            ->push([
                'data'       => [['id' => 'mapped-2']],
                'pagination' => ['endCursor' => null, 'hasNextPage' => false],
            ]);

        $samsara = new Samsara('test-token', ['retry' => 0]);
        $samsara->setHttpFactory($http);
        $builder = (new Builder(new TestQueryResource($samsara), '/custom/mapped-pages'))
            ->usingEntity(TestMappedEntity::class);

        $firstPage = $builder->paginate();
        $nextPage = $firstPage->nextPage();

        $this->assertInstanceOf(TestMappedEntity::class, $firstPage->items()->first());
        $this->assertNotNull($nextPage);
        $this->assertInstanceOf(TestMappedEntity::class, $nextPage->items()->first());
        $http->assertSent(fn ($request) => ($request->data()['after'] ?? null) === 'cursor-1');
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

    #[Test]
    public function it_serializes_declared_explode_true_lists_as_repeated_query_keys(): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake(['*' => $http->response(['data' => []])]);

        $samsara = new Samsara('test-token', ['retry' => 0]);
        $samsara->setHttpFactory($http);
        $builder = new Builder(new TestQueryResource($samsara));
        $builder->repeatQueryParameters('states')
            ->where('states', ['Requested', 'Approved'])
            ->where('search', 'north hub')
            ->get();

        $http->assertSent(function ($request) {
            $this->assertStringContainsString(
                'states=Requested&states=Approved',
                $request->url(),
            );
            $this->assertStringContainsString('search=north%20hub', $request->url());
            $this->assertStringNotContainsString('states%5B', $request->url());

            return true;
        });
    }

    #[Test]
    public function it_serializes_only_dedicated_list_helpers_in_the_outbound_query(): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake(['*' => $http->response(['data' => []])]);

        $samsara = new Samsara('test-token', ['retry' => 0]);
        $samsara->setHttpFactory($http);
        $builder = new Builder(new TestQueryResource($samsara));
        $builder->types(['gps', 'obdOdometerMeters'])
            ->withDecorations(['speed', 'address'])
            ->whereAttribute(['attribute-1', 'attribute-2'])
            ->whereDriver(['driver-1', 'driver-2'])
            ->whereParentTag(['parent-1', 'parent-2'])
            ->whereTag(['tag-1', 'tag-2'])
            ->whereVehicle(['vehicle-1', 'vehicle-2'])
            ->expand(['route', 'vehicle'])
            ->where('customArray', ['one', 'two'])
            ->get();

        $http->assertSent(function ($request) {
            $this->assertSame([
                'attributeValueIds' => 'attribute-1,attribute-2',
                'driverIds'         => 'driver-1,driver-2',
                'parentTagIds'      => 'parent-1,parent-2',
                'tagIds'            => 'tag-1,tag-2',
                'vehicleIds'        => 'vehicle-1,vehicle-2',
                'customArray'       => ['one', 'two'],
                'types'             => 'gps,obdOdometerMeters',
                'expand'            => 'route,vehicle',
                'decorations'       => 'speed,address',
            ], $request->data());
            $this->assertStringContainsString('types=gps%2CobdOdometerMeters', $request->url());
            $this->assertStringContainsString('decorations=speed%2Caddress', $request->url());
            $this->assertStringContainsString('customArray%5B0%5D=one', $request->url());

            return true;
        });
    }

    #[Test]
    public function it_uses_custom_endpoint_when_provided(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource, '/custom/endpoint');

        $this->assertSame('/custom/endpoint', $builder->getEndpoint());
    }

    #[Test]
    public function it_uses_resource_endpoint_by_default(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestQueryResource($samsara);
        $builder = new Builder($resource);

        $this->assertSame('/test-query-endpoint', $builder->getEndpoint());
    }

    #[Test]
    public function it_uses_the_implicit_default_page_size_unless_disabled(): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake(['*' => $http->response([
            'data'       => [],
            'pagination' => ['endCursor' => null, 'hasNextPage' => false],
        ])]);

        $samsara = new Samsara('test-token', ['retry' => 0, 'per_page' => 50]);
        $samsara->setHttpFactory($http);

        (new Builder(new TestQueryResource($samsara)))->paginate();

        $http->assertSent(fn ($request) => $request->data() === ['limit' => 50]);
    }

    #[Test]
    public function it_uses_the_resource_entity_mapper_by_default(): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake(['*' => $http->response(['data' => [['id' => 'default-1']]])]);

        $samsara = new Samsara('test-token', ['retry' => 0]);
        $samsara->setHttpFactory($http);

        $result = (new Builder(new TestQueryResource($samsara)))->get();

        $this->assertInstanceOf(Entity::class, $result->first());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function errorMappingTerminals(): array
    {
        return [
            'get'      => ['get'],
            'paginate' => ['paginate'],
            'lazy'     => ['lazy'],
        ];
    }

    /**
     * @return array<string, array{string, int}>
     */
    public static function explicitPageSizes(): array
    {
        return [
            'limit'    => ['limit', 11],
            'paginate' => ['paginate', 12],
            'lazy'     => ['lazy', 13],
            'where'    => ['where', 14],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function mappingTerminals(): array
    {
        return [
            'get'      => ['get'],
            'paginate' => ['paginate'],
            'lazy'     => ['lazy'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function pageSizeDisabledMultiPageTerminals(): array
    {
        return [
            'paginator' => ['paginator'],
            'lazy'      => ['lazy'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function pageSizeDisabledTerminals(): array
    {
        return [
            'getWithPagination' => ['getWithPagination'],
            'paginate'          => ['paginate'],
            'lazy'              => ['lazy'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function paginatedTerminals(): array
    {
        return [
            'paginate' => ['paginate'],
            'lazy'     => ['lazy'],
        ];
    }

    /**
     * @return array<string, array{int, int}>
     */
    public static function queryValidationResponsePaths(): array
    {
        return [
            'returned legacy 422 response' => [422, 1],
            'post-retry 400 exception'     => [400, 2],
        ];
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

class TestMappedEntity extends Entity {}
