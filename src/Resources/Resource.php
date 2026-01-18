<?php

namespace ErikGall\Samsara\Resources;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Data\Entity;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\PendingRequest;
use ErikGall\Samsara\Data\EntityCollection;
use ErikGall\Samsara\Exceptions\ServerException;
use ErikGall\Samsara\Contracts\ResourceInterface;
use ErikGall\Samsara\Exceptions\SamsaraException;
use ErikGall\Samsara\Exceptions\NotFoundException;
use ErikGall\Samsara\Exceptions\RateLimitException;
use ErikGall\Samsara\Exceptions\ValidationException;
use ErikGall\Samsara\Exceptions\AuthorizationException;
use ErikGall\Samsara\Exceptions\AuthenticationException;

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
     */
    public function all(): EntityCollection
    {
        $response = $this->client()->get($this->endpoint);

        $this->handleError($response);

        $data = $response->json('data', []);

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
     */
    public function create(array $data): object
    {
        $response = $this->client()->post($this->endpoint, $data);

        $this->handleError($response);

        $responseData = $response->json('data', $response->json());

        return $this->mapToEntity($responseData);
    }

    /**
     * Delete an entity.
     */
    public function delete(string $id): bool
    {
        $response = $this->client()->delete("{$this->endpoint}/{$id}");

        $this->handleError($response);

        return $response->successful();
    }

    /**
     * Find an entity by ID.
     */
    public function find(string $id): ?object
    {
        $response = $this->client()->get("{$this->endpoint}/{$id}");

        if ($response->status() === 404) {
            return null;
        }

        $this->handleError($response);

        $data = $response->json('data', $response->json());

        return $this->mapToEntity($data);
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
     * Update an entity.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(string $id, array $data): object
    {
        $response = $this->client()->patch("{$this->endpoint}/{$id}", $data);

        $this->handleError($response);

        $responseData = $response->json('data', $response->json());

        return $this->mapToEntity($responseData);
    }

    /**
     * Handle API error responses.
     *
     * @throws SamsaraException
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
            401 => throw new AuthenticationException($message, null, $context),
            403 => throw new AuthorizationException($message, null, $context),
            404 => throw new NotFoundException($message, null, $context),
            422 => throw new ValidationException($message, $body['errors'] ?? [], null, $context),
            429 => throw new RateLimitException(
                $message,
                $response->header('Retry-After') !== null ? (int) $response->header('Retry-After') : null,
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
}
