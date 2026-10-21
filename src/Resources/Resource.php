<?php

namespace Samsara\Resources;

use Closure;
use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Http\RetryAfter;
use Samsara\Http\QueryString;
use Samsara\Http\RequestExecutor;
use Samsara\Http\ResponseDecoder;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Response;
use Samsara\Exceptions\ServerException;
use Samsara\Exceptions\SamsaraException;
use Samsara\Exceptions\NotFoundException;
use Illuminate\Http\Client\PendingRequest;
use Samsara\Exceptions\RateLimitException;
use Samsara\Exceptions\ValidationException;
use Samsara\Exceptions\AuthorizationException;
use Samsara\Exceptions\AuthenticationException;

/**
 * Base resource class for API endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @template TEntity of Entity = Entity
 */
abstract class Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '';

    /**
     * The entity class for this resource.
     *
     * @var class-string<TEntity>
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
     * Get the HTTP client.
     */
    public function client(): PendingRequest
    {
        return $this->samsara->client();
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
     * @return class-string<TEntity>
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
     * Encode an ID as exactly one URL path segment.
     */
    protected function encodePathSegment(string $id): string
    {
        return rawurlencode($id);
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
     *
     * @return TEntity|null
     */
    final protected function findByEncodedPathSegment(string $id): ?Entity
    {
        $endpoint = $this->endpoint.'/'.$id;

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
                RetryAfter::seconds($response->header('Retry-After')),
                null,
                $context + ['retry_after_header' => $response->header('Retry-After')]
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
     * @return EntityCollection<int, TEntity>
     */
    protected function mapToEntities(array $data): EntityCollection
    {
        $entities = array_map(
            fn (array $item): Entity => $this->mapToEntity($item),
            $data
        );

        return new EntityCollection($entities);
    }

    /**
     * Map response data to an entity.
     *
     * @param  array<string, mixed>  $data
     * @return TEntity
     */
    protected function mapToEntity(array $data): Entity
    {
        $entityClass = $this->entity;

        return $entityClass::make($data);
    }

    /**
     * Create a query builder for an endpoint.
     *
     * Subclasses may override this factory to customize builder creation while
     * retaining each resource method's endpoint-specific configuration.
     *
     * @return Builder<TEntity>
     */
    protected function newQuery(?string $endpoint = null): Builder
    {
        return (new Builder($this, $endpoint ?? $this->endpoint))
            ->usingEntity($this->getEntityClass());
    }

    /**
     * Get the successful-response decoder.
     */
    private function responseDecoder(): ResponseDecoder
    {
        return $this->responseDecoder ??= new ResponseDecoder;
    }
}
