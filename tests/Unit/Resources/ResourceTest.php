<?php

namespace Samsara\Tests\Unit\Resources;

use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Resources\Resource;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Exceptions\ServerException;
use Illuminate\Http\Client\PendingRequest;
use Samsara\Exceptions\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Samsara\Exceptions\UnsupportedOperationException;

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
    public function it_executes_direct_get_requests_with_repeated_query_keys(): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake(['*' => $http->response(['data' => ['ok' => true]])]);

        $samsara = new Samsara('test-token', ['retry' => 0]);
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
    #[DataProvider('findNotFoundAttempts')]
    public function it_keeps_find_not_found_as_null_across_retry_paths(int $attempts): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake(['*' => $http->response(['message' => 'Not found'], 404)]);

        $samsara = new Samsara('test-token', ['retry' => $attempts]);
        $samsara->setHttpFactory($http);

        $this->assertNull((new TestResource($samsara))->find('missing'));
        $http->assertSentCount($attempts);
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
        string $endpoint
    ): void {
        $body = [
            'message'   => 'Invalid parameters',
            'requestId' => 'req-123',
            'errors'    => ['name' => ['The name field is required.']],
        ];
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake(['*' => $http->response($body, $status)]);

        $samsara = new Samsara('test-token', ['retry' => $attempts]);
        $samsara->setHttpFactory($http);
        $resource = new TestResource($samsara);

        try {
            match ($operation) {
                'create' => $resource->create([]),
                'update' => $resource->update('123', []),
            };

            $this->fail('Expected a validation exception.');
        } catch (ValidationException $exception) {
            $this->assertSame('Invalid parameters', $exception->getMessage());
            $this->assertSame($status, $exception->getCode());
            $this->assertSame($body['errors'], $exception->getErrors());
            $this->assertSame($status, $exception->getContext()['status']);
            $this->assertSame($endpoint, $exception->getContext()['endpoint']);
            $this->assertSame($body, $exception->getContext()['body']);
            $http->assertSentCount($attempts);
        }
    }

    #[Test]
    #[DataProvider('crudRequests')]
    public function it_maps_post_retry_errors_for_every_inherited_crud_request(
        string $operation,
        string $method,
        string $endpoint
    ): void {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake([
            '*' => $http->response(['message' => 'Service unavailable'], 503),
        ]);

        $samsara = new Samsara('test-token', ['retry' => 2]);
        $samsara->setHttpFactory($http);
        $resource = new TestResource($samsara);

        try {
            match ($operation) {
                'all'    => $resource->all(),
                'create' => $resource->create(['name' => 'Created']),
                'delete' => $resource->delete('123'),
                'find'   => $resource->find('123'),
                'update' => $resource->update('123', ['name' => 'Updated']),
            };

            $this->fail('Expected the request to throw after exhausting retries.');
        } catch (ServerException $exception) {
            $this->assertSame($endpoint, $exception->getContext()['endpoint']);
            $http->assertSentCount(2);
            $http->assertSent(fn ($request) => $request->method() === $method
                && $request->url() === 'https://api.samsara.com'.$endpoint);
        }
    }

    #[Test]
    public function it_maps_post_retry_errors_through_the_protected_sdk_request_bridge(): void
    {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake([
            '*' => $http->response(['message' => 'Service unavailable'], 503),
        ]);

        $samsara = new Samsara('test-token', ['retry' => 2]);
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
    #[DataProvider('genericOperations')]
    public function it_preserves_supported_generic_operation_behavior(
        string $operation,
        string $method,
        string $endpoint
    ): void {
        $http = new \Illuminate\Http\Client\Factory;
        $body = $operation === 'all'
            ? ['data' => [['id' => 'entity-1']]]
            : ['data' => ['id' => 'entity-1']];
        $http->fake(['*' => $http->response($body)]);

        $samsara = new Samsara('test-token', ['retry' => 0]);
        $samsara->setHttpFactory($http);
        $result = $this->executeGenericOperation(new TestResource($samsara), $operation);

        if ($operation === 'all') {
            $this->assertInstanceOf(EntityCollection::class, $result);
            $this->assertCount(1, $result);
        } elseif ($operation === 'delete') {
            $this->assertTrue($result);
        } else {
            $this->assertInstanceOf(Entity::class, $result);
            $this->assertSame('entity-1', $result->getId());
        }

        $http->assertSentCount(1);
        $http->assertSent(fn ($request) => $request->method() === $method
            && $request->url() === 'https://api.samsara.com'.$endpoint);
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
    #[DataProvider('genericOperations')]
    public function it_stops_unsupported_generic_operations_before_sending_http(
        string $operation,
        string $method,
        string $endpoint
    ): void {
        $http = new \Illuminate\Http\Client\Factory;
        $http->fake(['*' => $http->response(['data' => []])]);

        $samsara = new Samsara('test-token', ['retry' => 0]);
        $samsara->setHttpFactory($http);
        $resource = new UnsupportedGenericResource($samsara);

        try {
            $this->executeGenericOperation($resource, $operation);
            $this->fail("Expected the {$operation} operation to be rejected locally.");
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame([
                'resource'  => UnsupportedGenericResource::class,
                'operation' => $operation,
                'endpoint'  => $endpoint,
            ], $exception->getContext());
            $http->assertSentCount(0);
        }
    }

    #[Test]
    public function it_stores_samsara_instance(): void
    {
        $samsara = new Samsara('test-token');
        $resource = new TestResource($samsara);

        $this->assertInstanceOf(Samsara::class, $resource->getSamsara());
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function crudRequests(): array
    {
        return [
            'all'    => ['all', 'GET', '/test-endpoint'],
            'create' => ['create', 'POST', '/test-endpoint'],
            'delete' => ['delete', 'DELETE', '/test-endpoint/123'],
            'find'   => ['find', 'GET', '/test-endpoint/123'],
            'update' => ['update', 'PATCH', '/test-endpoint/123'],
        ];
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
     * @return array<string, array{string, int, int, string}>
     */
    public static function validationResponsePaths(): array
    {
        return [
            'returned 400 response'    => ['create', 400, 1, '/test-endpoint'],
            'post-retry 422 exception' => ['update', 422, 2, '/test-endpoint/123'],
        ];
    }

    private function executeGenericOperation(Resource $resource, string $operation): mixed
    {
        return match ($operation) {
            'all'    => $resource->all(),
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
}

class UnsupportedGenericResource extends Resource
{
    protected string $endpoint = '/test-endpoint';

    /**
     * @return array<int, string>
     */
    protected function supportedGenericOperations(): array
    {
        return [];
    }
}
