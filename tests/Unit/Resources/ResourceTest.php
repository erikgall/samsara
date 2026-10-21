<?php

namespace Samsara\Tests\Unit\Resources;

use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Illuminate\Support\Sleep;
use Illuminate\Support\Carbon;
use Samsara\Data\Driver\Driver;
use Samsara\Resources\Resource;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Exceptions\ServerException;
use Samsara\Exceptions\SamsaraException;
use Samsara\Exceptions\NotFoundException;
use Illuminate\Http\Client\PendingRequest;
use Samsara\Exceptions\RateLimitException;
use Samsara\Exceptions\ValidationException;
use Samsara\Resources\Concerns\FindsEntities;
use PHPUnit\Framework\Attributes\DataProvider;
use Samsara\Resources\Concerns\CreatesEntities;
use Samsara\Resources\Concerns\DeletesEntities;
use Samsara\Resources\Concerns\UpdatesEntities;
use Illuminate\Http\Client\Factory as HttpFactory;

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
        Http::preventStrayRequests();
        Http::fake();
    }

    #[Test]
    #[DataProvider('findNotFoundAttempts')]
    public function it_does_not_retry_find_not_found_responses(int $attempts): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['message' => 'Not found'], 404)]);

        $samsara = new Samsara('test-token', ['attempts' => $attempts]);
        $samsara->setHttpFactory($http);

        $this->assertNull((new TestResource($samsara))->find('missing'));
        $http->assertSentCount(1);
    }

    #[Test]
    #[DataProvider('encodedPathOperations')]
    public function it_encodes_arbitrary_ids_as_one_path_segment(string $operation, string $method): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => ['id' => 'external/id with spaces?#']])]);

        $samsara = new Samsara('test-token', ['attempts' => 1]);
        $samsara->setHttpFactory($http);
        $resource = new TestResource($samsara);

        match ($operation) {
            'delete' => $resource->delete('external/id with spaces?#'),
            'find'   => $resource->find('external/id with spaces?#'),
            'update' => $resource->update('external/id with spaces?#', ['name' => 'Updated']),
        };

        $http->assertSent(fn ($request) => $request->method() === $method
            && $request->url() === 'https://api.samsara.com/test-endpoint/external%2Fid%20with%20spaces%3F%23');
    }

    #[Test]
    public function it_executes_direct_get_requests_with_repeated_query_keys(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => ['ok' => true]])]);

        $samsara = new Samsara('test-token', ['attempts' => 1]);
        $samsara->setHttpFactory($http);
        $response = (new TestExecutorResource($samsara))->getCustomEndpointWithRepeatedQuery();

        $this->assertTrue($response->json('data.ok'));
        $http->assertSent(fn ($request) => $request->url()
            === 'https://api.samsara.com/test-endpoint/custom?states=Requested&states=Approved&search=north%20hub');
    }

    #[Test]
    public function it_exposes_one_protected_final_sdk_request_bridge(): void
    {
        $method = new \ReflectionMethod(Resource::class, 'executeSdkRequest');

        $this->assertTrue($method->isProtected());
        $this->assertTrue($method->isFinal());
    }

    #[Test]
    #[DataProvider('entityClassOperations')]
    public function it_honors_the_resource_entity_class_override_for_single_entities(string $operation): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => [
            'id'                     => 'driver-1',
            'driverActivationStatus' => 'active',
        ]])]);
        $resource = new TestEntityClassResource(new Samsara('test-token', ['attempts' => 1], $http));

        $driver = match ($operation) {
            'find'   => $resource->find('driver-1'),
            'create' => $resource->create(['name' => 'Driver']),
        };

        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertSame('driver-1', $driver->id);
        $this->assertTrue($driver->isActive());
        $http->assertSentCount(1);
    }

    #[Test]
    public function it_honors_the_resource_entity_class_override_when_querying(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => [[
            'id'                     => 'driver-1',
            'driverActivationStatus' => 'active',
        ]]])]);
        $resource = new TestEntityClassResource(new Samsara('test-token', ['attempts' => 1], $http));

        $driver = $resource->query()->first();

        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertTrue($driver->isActive());
        $http->assertSentCount(1);
    }

    #[Test]
    public function it_keeps_direct_builder_construction_inside_the_base_resource_factory(): void
    {
        $resources = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(dirname(__DIR__, 3).'/src/Resources')
        );

        foreach ($resources as $resource) {
            if (! $resource->isFile() || $resource->getExtension() !== 'php' || $resource->getFilename() === 'Resource.php') {
                continue;
            }

            $contents = file_get_contents($resource->getPathname());

            $this->assertIsString($contents);
            $this->assertDoesNotMatchRegularExpression(
                '/new\s+Builder\s*\(/',
                $contents,
                "Direct Builder construction remains in {$resource->getPathname()}."
            );
        }
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
    #[DataProvider('validationResponsePaths')]
    public function it_maps_documented_and_legacy_validation_responses(
        string $operation,
        int $status,
        int $attempts,
        int $expectedRequests,
        string $endpoint
    ): void {
        $body = [
            'message'   => 'Invalid parameters',
            'requestId' => 'req-123',
            'errors'    => ['name' => ['The name field is required.']],
        ];
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response($body, $status)]);

        $samsara = new Samsara('test-token', ['attempts' => $attempts]);
        $samsara->setHttpFactory($http);
        $resource = new TestResource($samsara);

        try {
            match ($operation) {
                'create' => $resource->create([]),
                'update' => $resource->update('123', []),
            };

            $this->fail('Expected a validation exception.');
        } catch (ValidationException $exception) {
            $this->assertSame($status, $exception->getCode());
            $this->assertSame($body['errors'], $exception->getErrors());
            $this->assertSame($status, $exception->getContext()['status']);
            $this->assertSame($endpoint, $exception->getContext()['endpoint']);
            $this->assertSame($body, $exception->getContext()['body']);
            $http->assertSentCount($expectedRequests);
        }
    }

    /**
     * @param  class-string<SamsaraException>  $exceptionClass
     */
    #[Test]
    #[DataProvider('httpErrorStatuses')]
    public function it_maps_http_error_statuses_at_the_request_boundary(int $status, string $exceptionClass): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $body = ['message' => 'Rejected', 'requestId' => 'request-1'];
        $http->fake(['*' => $http->response($body, $status)]);
        $resource = new TestExecutorResource(new Samsara('test-token', ['attempts' => 1], $http));

        try {
            $resource->getCustomEndpoint();
            $this->fail('Expected an HTTP error.');
        } catch (SamsaraException $exception) {
            $this->assertSame($exceptionClass, $exception::class);
            $this->assertSame($status, $exception->getCode());
            $this->assertSame([
                'status'   => $status,
                'endpoint' => '/test-endpoint/custom',
                'body'     => $body,
            ], $exception->getContext());
        }

        $http->assertSentCount(1);
    }

    #[Test]
    #[DataProvider('crudRequests')]
    public function it_maps_post_retry_errors_for_every_inherited_crud_request(
        string $operation,
        string $method,
        string $endpoint,
        bool $retryWrites,
        int $expectedRequests
    ): void {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake([
            '*' => $http->response(['message' => 'Service unavailable'], 503),
        ]);

        $samsara = new Samsara('test-token', ['attempts' => 2, 'retry_writes' => $retryWrites]);
        $samsara->setHttpFactory($http);
        $resource = new TestResource($samsara);

        try {
            match ($operation) {
                'all'    => $resource->query()->get(),
                'create' => $resource->create(['name' => 'Created']),
                'delete' => $resource->delete('123'),
                'find'   => $resource->find('123'),
                'update' => $resource->update('123', ['name' => 'Updated']),
            };

            $this->fail('Expected the request to throw after exhausting retries.');
        } catch (ServerException $exception) {
            $this->assertSame($endpoint, $exception->getContext()['endpoint']);
            $http->assertSentCount($expectedRequests);
            $http->assertSent(fn ($request) => $request->method() === $method
                && $request->url() === 'https://api.samsara.com'.$endpoint);
        }
    }

    #[Test]
    public function it_maps_post_retry_errors_through_the_protected_sdk_request_bridge(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake([
            '*' => $http->response(['message' => 'Service unavailable'], 503),
        ]);

        $samsara = new Samsara('test-token', ['attempts' => 2]);
        $samsara->setHttpFactory($http);
        $resource = new TestExecutorResource($samsara);

        try {
            $resource->getCustomEndpoint();
            $this->fail('Expected the protected executor to map the final response.');
        } catch (ServerException $exception) {
            $this->assertSame('/test-endpoint/custom', $exception->getContext()['endpoint']);
            $http->assertSentCount(2);
        }
    }

    #[Test]
    #[DataProvider('rateLimitHeaders')]
    public function it_maps_retry_after_onto_rate_limit_exceptions(
        ?string $header,
        ?int $expected,
        ?string $expectedHeader,
    ): void {
        Carbon::setTestNow(Carbon::parse('2026-10-21 12:00:00 UTC'));
        Sleep::fake();
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $body = ['message' => 'Rate limited'];
        $http->fake(['*' => $http->response($body, 429, $header === null ? [] : ['Retry-After' => $header])]);
        $resource = new TestResource(new Samsara('test-token', ['attempts' => 1], $http));

        try {
            $resource->create([]);
            $this->fail('Expected a rate-limit exception.');
        } catch (RateLimitException $exception) {
            $this->assertSame($expected, $exception->getRetryAfter());
            $this->assertSame(429, $exception->getCode());
            $this->assertSame([
                'status'             => 429,
                'endpoint'           => '/test-endpoint',
                'body'               => $body,
                'retry_after_header' => $expectedHeader,
            ], $exception->getContext());
        } finally {
            Carbon::setTestNow();
        }

        $http->assertSentCount(1);
        Sleep::assertNeverSlept();
    }

    #[Test]
    #[DataProvider('unexpectedSuccessfulBodies')]
    public function it_maps_unexpected_successful_bodies_to_an_sdk_exception(
        string $operation,
        string $body,
        int $status,
        string $endpoint
    ): void {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response($body, $status)]);

        $samsara = new Samsara('test-token', ['attempts' => 1]);
        $samsara->setHttpFactory($http);
        $resource = new TestResource($samsara);

        try {
            match ($operation) {
                'create' => $resource->create([]),
                'find'   => $resource->find('entity-1'),
                'raw'    => (new TestExecutorResource($samsara))->getDecodedCustomEndpoint(),
            };

            $this->fail('Expected a Samsara SDK exception.');
        } catch (SamsaraException $exception) {
            $this->assertSame($status, $exception->getCode());
            $this->assertSame($endpoint, $exception->getContext()['endpoint']);
            $this->assertSame($body, $exception->getContext()['body']);
            $http->assertSentCount(1);
        }
    }

    #[Test]
    #[DataProvider('genericOperations')]
    public function it_preserves_supported_generic_operation_behavior(
        string $operation,
        string $method,
        string $endpoint
    ): void {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $body = $operation === 'all'
            ? ['data' => [['id' => 'entity-1']]]
            : ['data' => ['id' => 'entity-1']];
        $http->fake(['*' => $http->response($body)]);

        $samsara = new Samsara('test-token', ['attempts' => 1]);
        $samsara->setHttpFactory($http);
        $result = $this->executeGenericOperation(new TestResource($samsara), $operation);

        if ($operation === 'all') {
            $this->assertInstanceOf(EntityCollection::class, $result);
            $this->assertCount(1, $result);
        }

        if ($operation === 'delete') {
            $this->assertTrue($result);
        }

        if (! in_array($operation, ['all', 'delete'], true)) {
            $this->assertInstanceOf(Entity::class, $result);
            $this->assertSame('entity-1', $result->getId());
        }

        $http->assertSentCount(1);
        $http->assertSent(fn ($request) => $request->method() === $method
            && $request->url() === 'https://api.samsara.com'.$endpoint);
    }

    #[Test]
    #[DataProvider('invalidCollectionBodies')]
    public function it_rejects_present_non_list_collection_data(string $body): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake([
            '*' => $http->response($body, 200, ['Content-Type' => 'application/json']),
        ]);

        $samsara = new Samsara('test-token', ['attempts' => 1]);
        $samsara->setHttpFactory($http);

        try {
            (new TestResource($samsara))->query()->get();
            $this->fail('Expected invalid collection data to be rejected.');
        } catch (SamsaraException $exception) {
            $this->assertSame(SamsaraException::class, $exception::class);
            $this->assertSame(200, $exception->getCode());
            $this->assertSame([
                'status'   => 200,
                'endpoint' => '/test-endpoint',
                'body'     => $body,
            ], $exception->getContext());
        }
    }

    #[Test]
    public function it_returns_an_empty_collection_when_data_is_absent(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['pagination' => ['hasNextPage' => false]])]);

        $samsara = new Samsara('test-token', ['attempts' => 1]);
        $samsara->setHttpFactory($http);

        $this->assertCount(0, (new TestResource($samsara))->query()->get());
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
    public function it_routes_default_and_custom_endpoint_builders_through_the_resource_factory(): void
    {
        $resource = new TestBuilderFactoryResource(new Samsara('test-token'));

        $query = $resource->query();
        $customQuery = $resource->customQuery();

        $this->assertInstanceOf(TestBuilder::class, $query);
        $this->assertSame('/test-endpoint', $query->getEndpoint());
        $this->assertInstanceOf(TestBuilder::class, $customQuery);
        $this->assertSame('/test-endpoint/custom', $customQuery->getEndpoint());
        $this->assertSame(['/test-endpoint', '/test-endpoint/custom'], $resource->createdEndpoints);
    }

    #[Test]
    public function it_stores_samsara_instance(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestResource($samsara);

        $this->assertInstanceOf(Samsara::class, $resource->getSamsara());
    }

    #[Test]
    public function it_treats_a_percent_triplet_as_literal_id_content(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => ['id' => 'abc%2Fdef']])]);

        $samsara = new Samsara('test-token', ['attempts' => 1]);
        $samsara->setHttpFactory($http);

        (new TestResource($samsara))->find('abc%2Fdef');

        $http->assertSent(fn ($request) => parse_url($request->url(), PHP_URL_PATH)
            === '/test-endpoint/abc%252Fdef');
    }

    /**
     * @return array<string, array{string, string, string, bool, int}>
     */
    public static function crudRequests(): array
    {
        return [
            'list'            => ['all', 'GET', '/test-endpoint', false, 2],
            'find'            => ['find', 'GET', '/test-endpoint/123', false, 2],
            'create default'  => ['create', 'POST', '/test-endpoint', false, 1],
            'create opted in' => ['create', 'POST', '/test-endpoint', true, 2],
            'update default'  => ['update', 'PATCH', '/test-endpoint/123', false, 1],
            'update opted in' => ['update', 'PATCH', '/test-endpoint/123', true, 2],
            'delete default'  => ['delete', 'DELETE', '/test-endpoint/123', false, 1],
            'delete opted in' => ['delete', 'DELETE', '/test-endpoint/123', true, 2],
        ];
    }

    /** @return array<string, array{string, string}> */
    public static function encodedPathOperations(): array
    {
        return [
            'find'   => ['find', 'GET'],
            'update' => ['update', 'PATCH'],
            'delete' => ['delete', 'DELETE'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function entityClassOperations(): array
    {
        return ['find' => ['find'], 'create' => ['create']];
    }

    /**
     * @return array<string, array{int}>
     */
    public static function findNotFoundAttempts(): array
    {
        return [
            'returned response'    => [1],
            'post-retry exception' => [2],
        ];
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function genericOperations(): array
    {
        return [
            'all'    => ['all', 'GET', '/test-endpoint'],
            'find'   => ['find', 'GET', '/test-endpoint/123'],
            'create' => ['create', 'POST', '/test-endpoint'],
            'update' => ['update', 'PATCH', '/test-endpoint/123'],
            'delete' => ['delete', 'DELETE', '/test-endpoint/123'],
        ];
    }

    /**
     * @return array<string, array{int, class-string<SamsaraException>}>
     */
    public static function httpErrorStatuses(): array
    {
        return [
            'not found'    => [404, NotFoundException::class],
            'server error' => [500, ServerException::class],
            'other error'  => [499, SamsaraException::class],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidCollectionBodies(): array
    {
        return [
            'null data'   => ['{"data":null}'],
            'object data' => ['{"data":{"id":"entity-1"}}'],
            'scalar data' => ['{"data":"entity-1"}'],
        ];
    }

    /**
     * @return array<string, array{?string, ?int, ?string}>
     */
    public static function rateLimitHeaders(): array
    {
        return [
            'future date'  => ['Wed, 21 Oct 2026 13:00:00 GMT', 3600, 'Wed, 21 Oct 2026 13:00:00 GMT'],
            'expired date' => ['Wed, 21 Oct 2026 11:00:00 GMT', 0, 'Wed, 21 Oct 2026 11:00:00 GMT'],
            'numeric'      => ['2', 2, '2'],
            'zero'         => ['0', 0, '0'],
            'invalid'      => ['invalid', null, 'invalid'],
            'missing'      => [null, null, null],
            'empty'        => ['', null, null],
        ];
    }

    /**
     * @return array<string, array{string, string, int, string}>
     */
    public static function unexpectedSuccessfulBodies(): array
    {
        return [
            'empty create response' => ['create', '', 201, '/test-endpoint'],
            'html find response'    => ['find', '<html>not json</html>', 200, '/test-endpoint/entity-1'],
            'empty raw response'    => ['raw', '', 200, '/test-endpoint/custom'],
            'null data envelope'    => ['create', '{"data":null}', 201, '/test-endpoint'],
        ];
    }

    /**
     * @return array<string, array{string, int, int, int, string}>
     */
    public static function validationResponsePaths(): array
    {
        return [
            'returned 400 response' => ['create', 400, 1, 1, '/test-endpoint'],
            'returned 422 response' => ['update', 422, 2, 1, '/test-endpoint/123'],
        ];
    }

    private function executeGenericOperation(Resource $resource, string $operation): mixed
    {
        return match ($operation) {
            'all'    => $resource->query()->get(),
            'find'   => $resource->find('123'),
            'create' => $resource->create(['name' => 'Created']),
            'update' => $resource->update('123', ['name' => 'Updated']),
            'delete' => $resource->delete('123'),
        };
    }
}

/**
 * Test resource implementation.
 */
class TestResource extends Resource
{
    use CreatesEntities;
    use DeletesEntities;
    use FindsEntities;
    use UpdatesEntities;

    protected string $endpoint = '/test-endpoint';

    protected string $entity = Entity::class;

    public function getSamsara(): Samsara
    {
        return $this->samsara;
    }

    public function query(): Builder
    {
        return $this->newQuery();
    }

    public function testMapToEntities(array $data): EntityCollection
    {
        return $this->mapToEntities($data);
    }

    public function testMapToEntity(array $data): Entity
    {
        return $this->mapToEntity($data);
    }
}

class TestExecutorResource extends Resource
{
    protected string $endpoint = '/test-endpoint';

    public function getCustomEndpoint(): Response
    {
        $endpoint = "{$this->endpoint}/custom";

        return $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint),
            $endpoint
        );
    }

    public function getCustomEndpointWithRepeatedQuery(): Response
    {
        return $this->executeSdkGet(
            "{$this->endpoint}/custom",
            [
                'states' => ['Requested', 'Approved'],
                'search' => 'north hub',
            ],
            ['states'],
        );
    }

    /**
     * @return array<mixed>
     */
    public function getDecodedCustomEndpoint(): array
    {
        $endpoint = "{$this->endpoint}/custom";
        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint),
            $endpoint
        );

        return $this->decodeJsonArray($response, $endpoint);
    }
}

class TestBuilderFactoryResource extends Resource
{
    /** @var array<int, string> */
    public array $createdEndpoints = [];

    protected string $endpoint = '/test-endpoint';

    public function customQuery(): Builder
    {
        return $this->newQuery("{$this->endpoint}/custom");
    }

    public function query(): Builder
    {
        return $this->newQuery();
    }

    protected function newQuery(?string $endpoint = null): Builder
    {
        $endpoint ??= $this->endpoint;
        $this->createdEndpoints[] = $endpoint;

        return new TestBuilder($this, $endpoint);
    }
}

class TestBuilder extends Builder {}

/**
 * @author Erik Galloway <erik@erikgall.com>
 */
class TestEntityClassResource extends TestResource
{
    /**
     * @return class-string<Driver>
     */
    public function getEntityClass(): string
    {
        return Driver::class;
    }
}
