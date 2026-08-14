<?php

namespace Samsara\Resources;

use Closure;
use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Http\QueryString;
use Samsara\Http\RequestExecutor;
use Samsara\Http\ResponseDecoder;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Response;
use Samsara\Exceptions\ServerException;
use Samsara\Contracts\ResourceInterface;
use Samsara\Exceptions\SamsaraException;
use Samsara\Exceptions\NotFoundException;
use Illuminate\Http\Client\PendingRequest;
use Samsara\Exceptions\RateLimitException;
use Samsara\Exceptions\ValidationException;
use Samsara\Exceptions\AuthorizationException;
use Samsara\Exceptions\AuthenticationException;
use Samsara\Exceptions\UnsupportedOperationException;

/**
 * Base resource class for API endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
abstract class Resource implements ResourceInterface
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Entity>
     */
    protected string $entity = Entity::class;

    /**
     * The Samsara client instance.
     */
    protected Samsara $samsara;

    /**
     * The shared HTTP request executor.
     */
    private ?RequestExecutor $requestExecutor = null;

    /**
     * The shared successful-response decoder.
     */
    private ?ResponseDecoder $responseDecoder = null;

    /**
     * Create a new resource instance.
     */
    public function __construct(Samsara $samsara)
    {
        $this->samsara = $samsara;
    }

    /**
     * Get all entities from this resource.
     *
     * @return EntityCollection<int, Entity>
     *
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws RateLimitException
     * @throws ServerException
     * @throws SamsaraException
     * @throws UnsupportedOperationException
     */
    public function all(): EntityCollection
    {
        $this->ensureGenericOperationIsSupported('all', $this->endpoint);

        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($this->endpoint),
            $this->endpoint
        );

        $data = $this->decodeJsonList($response, $this->endpoint);

        return $this->mapToEntities($data);
    }

    /**
     * Get the HTTP client.
     */
    public function client(): PendingRequest
    {
        return $this->samsara->client();
    }

    /**
     * Create a new entity.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidationException
     * @throws RateLimitException
     * @throws ServerException
     * @throws SamsaraException
     * @throws UnsupportedOperationException
     */
    public function create(array $data): object
    {
        $this->ensureGenericOperationIsSupported('create', $this->endpoint);

        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($this->endpoint, $data),
            $this->endpoint
        );

        $responseData = $this->decodeJsonData($response, $this->endpoint);

        return $this->mapToEntity($responseData);
    }

    /**
     * Delete an entity.
     *
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ServerException
     * @throws SamsaraException
     * @throws UnsupportedOperationException
     */
    public function delete(string $id): bool
    {
        $endpoint = $this->endpoint.'/'.$this->encodePathSegment($id);
        $this->ensureGenericOperationIsSupported('delete', $endpoint);

        $response = $this->executeSdkRequest(
            fn () => $this->client()->delete($endpoint),
            $endpoint
        );

        return $response->successful();
    }

    /**
     * Find an entity by ID.
     *
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws RateLimitException
     * @throws ServerException
     * @throws SamsaraException
     * @throws UnsupportedOperationException
     */
    public function find(string $id): ?object
    {
        return $this->findByEncodedPathSegment($this->encodePathSegment($id));
    }

    /**
     * Get the configured default page size.
     */
    public function getDefaultPerPage(): int
    {
        return (int) $this->samsara->getConfig('per_page', 100);
    }

    /**
     * Get the API endpoint for this resource.
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Get the entity class for this resource.
     *
     * @return class-string<Entity>
     */
    public function getEntityClass(): string
    {
        return $this->entity;
    }

    /**
     * Throw the SDK exception represented by an unsuccessful response.
     */
    public function throwIfError(Response $response): void
    {
        $this->handleError($response);
    }

    /**
     * Update an entity.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws RateLimitException
     * @throws ServerException
     * @throws SamsaraException
     * @throws UnsupportedOperationException
     */
    public function update(string $id, array $data): object
    {
        $endpoint = $this->endpoint.'/'.$this->encodePathSegment($id);
        $this->ensureGenericOperationIsSupported('update', $endpoint);

        $response = $this->executeSdkRequest(
            fn () => $this->client()->patch($endpoint, $data),
            $endpoint
        );

        $responseData = $this->decodeJsonData($response, $endpoint);

        return $this->mapToEntity($responseData);
    }

    /**
     * Create a query builder for an endpoint.
     *
     * Subclasses may override this factory to customize builder creation while
     * retaining each resource method's endpoint-specific configuration.
     */
    protected function createBuilderWithEndpoint(string $endpoint): Builder
    {
        return new Builder($this, $endpoint);
    }

    /**
     * Decode a successful response that must contain a JSON array or object.
     *
     * @return array<mixed>
     */
    protected function decodeJsonArray(Response $response, string $endpoint): array
    {
        return $this->responseDecoder()->decodeArray($response, $endpoint);
    }

    /**
     * Decode a JSON response and unwrap its data key when present.
     *
     * @return array<mixed>
     */
    protected function decodeJsonData(
        Response $response,
        string $endpoint,
        bool $fallbackToPayload = true,
    ): array {
        return $this->responseDecoder()->decodeData($response, $endpoint, $fallbackToPayload);
    }

    /**
     * Decode a successful response collection.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function decodeJsonList(
        Response $response,
        string $endpoint,
        string $collectionKey = 'data',
    ): array {
        return $this->responseDecoder()->decodeList($response, $endpoint, $collectionKey);
    }

    /**
     * Execute a GET request with contract-aware query serialization.
     *
     * @param  array<string, mixed>  $query
     * @param  array<string>  $repeatedQueryParameters
     */
    final protected function executeSdkGet(
        string $endpoint,
        array $query = [],
        array $repeatedQueryParameters = [],
    ): Response {
        [$requestEndpoint, $requestQuery] = QueryString::prepare(
            $endpoint,
            $query,
            $repeatedQueryParameters,
        );

        return $this->executeSdkRequest(
            fn () => $requestQuery === []
                ? $this->client()->get($requestEndpoint)
                : $this->client()->get($requestEndpoint, $requestQuery),
            $endpoint,
        );
    }

    /**
     * Execute an HTTP request through the resource's error mapper.
     *
     * @param  Closure(): Response  $request
     * @param  array<int, int>  $ignoredStatuses
     */
    final protected function executeSdkRequest(
        Closure $request,
        string $endpoint,
        array $ignoredStatuses = []
    ): Response {
        return ($this->requestExecutor ??= new RequestExecutor)->execute(
            $request,
            fn (Response $response) => $this->handleError($response),
            $endpoint,
            $ignoredStatuses
        );
    }

    /**
     * Find an entity using an already encoded single path segment.
     */
    final protected function findByEncodedPathSegment(string $id): ?object
    {
        $endpoint = $this->endpoint.'/'.$id;
        $this->ensureGenericOperationIsSupported('find', $endpoint);

        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint),
            $endpoint,
            [404]
        );

        if ($response->status() === 404) {
            return null;
        }

        $data = $this->decodeJsonData($response, $endpoint);

        return $this->mapToEntity($data);
    }

    /**
     * Handle API error responses.
     *
     * @throws AuthenticationException When API returns 401
     * @throws AuthorizationException When API returns 403
     * @throws NotFoundException When API returns 404
     * @throws ValidationException When API returns 400 or 422
     * @throws RateLimitException When API returns 429
     * @throws ServerException When API returns 5xx
     * @throws SamsaraException For other error responses
     */
    protected function handleError(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        $status = $response->status();
        $body = $response->json();
        $message = $body['message'] ?? $response->body();
        $context = [
            'status'   => $status,
            'endpoint' => $this->endpoint,
            'body'     => $body,
        ];

        match ($status) {
            401      => throw new AuthenticationException($message, null, $context),
            403      => throw new AuthorizationException($message, null, $context),
            404      => throw new NotFoundException($message, null, $context),
            400, 422 => throw new ValidationException(
                $message,
                is_array($body['errors'] ?? null) ? $body['errors'] : [],
                null,
                $context,
                $status
            ),
            429 => throw new RateLimitException(
                $message,
                $this->retryAfterSeconds($response),
                null,
                $context
            ),
            default => $status >= 500
                ? throw new ServerException($message, $status, null, $context)
                : throw new SamsaraException($message, $status, null, $context),
        };
    }

    /**
     * Map response data to an entity collection.
     *
     * @param  array<int, array<string, mixed>>  $data
     * @return EntityCollection<int, Entity>
     */
    protected function mapToEntities(array $data): EntityCollection
    {
        /** @var array<int, Entity> $entities */
        $entities = array_map(
            fn (array $item) => $this->mapToEntity($item),
            $data
        );

        return new EntityCollection($entities);
    }

    /**
     * Map response data to an entity.
     *
     * @param  array<string, mixed>  $data
     */
    protected function mapToEntity(array $data): object
    {
        $entityClass = $this->entity;

        return $entityClass::make($data);
    }

    /**
     * Get the inherited generic operations supported by this resource.
     *
     * @return array<int, string>
     */
    protected function supportedGenericOperations(): array
    {
        return ['all', 'find', 'create', 'update', 'delete'];
    }

    /**
     * Encode an ID as exactly one URL path segment.
     */
    private function encodePathSegment(string $id): string
    {
        return rawurlencode($id);
    }

    /**
     * Stop unsupported inherited operations before they reach the HTTP client.
     */
    private function ensureGenericOperationIsSupported(string $operation, string $endpoint): void
    {
        if (in_array($operation, $this->supportedGenericOperations(), true)) {
            return;
        }

        $resource = static::class;

        throw new UnsupportedOperationException(
            "Generic operation [{$operation}] is not supported by resource [{$resource}].",
            0,
            null,
            [
                'resource'  => $resource,
                'operation' => $operation,
                'endpoint'  => $endpoint,
            ]
        );
    }

    /**
     * Get the successful-response decoder.
     */
    private function responseDecoder(): ResponseDecoder
    {
        return $this->responseDecoder ??= new ResponseDecoder;
    }

    /**
     * Read a delta-seconds Retry-After value for the public exception API.
     */
    private function retryAfterSeconds(Response $response): ?int
    {
        $retryAfter = $response->header('Retry-After');

        return $retryAfter !== null && ctype_digit($retryAfter)
            ? (int) $retryAfter
            : null;
    }
}
