<?php

namespace Samsara\Query;

use Closure;
use Generator;
use BackedEnum;
use DateTimeInterface;
use Samsara\Data\Entity;
use Samsara\Http\QueryString;
use Samsara\Resources\Resource;
use Samsara\Http\RequestExecutor;
use Samsara\Http\ResponseDecoder;
use Samsara\Data\EntityCollection;
use Samsara\Enums\VehicleStatType;
use Illuminate\Http\Client\Response;
use Illuminate\Support\LazyCollection;
use Samsara\Concerns\InteractsWithTime;
use Samsara\Exceptions\SamsaraException;
use Illuminate\Support\Traits\Conditionable;

/**
 * Query builder for API requests.
 *
 * Provides a fluent interface for building API queries with
 * filtering, pagination, and time range options.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @template TEntity of Entity = Entity
 */
class Builder
{
    use Conditionable;
    use InteractsWithTime;

    /**
     * The cursor for pagination.
     */
    protected ?string $cursor = null;

    /**
     * The query parameter used to request the next cursor page.
     */
    protected string $cursorParameter = 'after';

    /**
     * The decorations to include.
     *
     * @var array<VehicleStatType|string>
     */
    protected array $decorations = [];

    /**
     * Whether pagination should apply the configured default page size.
     */
    protected bool $defaultPageSizeEnabled = true;

    /**
     * The optional endpoint override.
     */
    protected ?string $endpoint = null;

    /**
     * The relations to expand.
     *
     * @var array<string>
     */
    protected array $expand = [];

    /**
     * The query filters.
     *
     * @var array<string, mixed>
     */
    protected array $filters = [];

    /**
     * The result limit.
     */
    protected ?int $limit = null;

    /**
     * Form/explode=true list parameters that use repeated query keys.
     *
     * @var array<string>
     */
    protected array $repeatedQueryParameters = [];

    /**
     * The resource instance.
     */
    protected Resource $resource;

    /**
     * The response key containing the result collection.
     */
    protected string $responseCollectionKey = 'data';

    /**
     * The endpoint-specific response mapper.
     *
     * @var (Closure(array<string, mixed>): TEntity)|null
     */
    protected ?Closure $responseMapper = null;

    /**
     * The stat types to fetch.
     *
     * @var array<VehicleStatType|string>
     */
    protected array $types = [];

    /**
     * The successful-response decoder.
     */
    private ?ResponseDecoder $responseDecoder = null;

    /**
     * Create a new query builder instance.
     */
    public function __construct(Resource $resource, ?string $endpoint = null)
    {
        $this->resource = $resource;
        $this->endpoint = $endpoint;
    }

    /**
     * Set the cursor for pagination.
     */
    public function after(string $cursor): static
    {
        $this->cursor = $cursor;

        return $this;
    }

    /**
     * Set the start and end time range.
     */
    public function between(DateTimeInterface|string $start, DateTimeInterface|string $end): static
    {
        $this->startTime($start);
        $this->endTime($end);

        return $this;
    }

    /**
     * Build the query parameters array.
     *
     * @return array<string, mixed>
     */
    public function buildQuery(): array
    {
        $query = $this->filters;

        if ($this->limit !== null) {
            $query['limit'] = $this->limit;
        }

        if ($this->cursor !== null) {
            $query[$this->cursorParameter] = $this->cursor;
        }

        if (! empty($this->types)) {
            $query['types'] = $this->serializeList($this->types);
        }

        if (! empty($this->expand)) {
            $query['expand'] = $this->serializeList($this->expand);
        }

        if (! empty($this->decorations)) {
            $query['decorations'] = $this->serializeList($this->decorations);
        }

        return $query;
    }

    /**
     * Filter by created after timestamp.
     */
    public function createdAfter(DateTimeInterface|string $time): static
    {
        $this->filters['createdAfterTime'] = $this->formatTime($time);

        return $this;
    }

    /**
     * Set the end time.
     */
    public function endTime(DateTimeInterface|string $time): static
    {
        $this->filters['endTime'] = $this->formatTime($time);

        return $this;
    }

    /**
     * Set the relations to expand.
     *
     * @param  array<string>|string  $expand
     */
    public function expand(array|string $expand): static
    {
        $this->expand = is_array($expand) ? $expand : [$expand];

        return $this;
    }

    /**
     * Get the first result.
     *
     * @return TEntity|null
     */
    public function first(): ?Entity
    {
        if ($this->defaultPageSizeEnabled) {
            $this->limit(1);
        }

        return $this->get()->first();
    }

    /**
     * Execute the query and get results.
     *
     * @return EntityCollection<int, TEntity>
     */
    public function get(): EntityCollection
    {
        $response = $this->executeRequest();

        $data = $this->responseDecoder()->decodeList(
            $response,
            $this->getEndpoint(),
            $this->responseCollectionKey,
        );

        return $this->mapToEntities($data);
    }

    /**
     * Get the endpoint for this query.
     */
    public function getEndpoint(): string
    {
        return $this->endpoint ?? $this->resource->getEndpoint();
    }

    /**
     * Get results as a lazy collection.
     *
     * @return LazyCollection<int, TEntity>
     */
    public function lazy(?int $chunkSize = null): LazyCollection
    {
        $template = clone $this;
        if ($chunkSize !== null) {
            $template->limit($chunkSize);
        }

        $template->applyDefaultPerPage();

        return LazyCollection::make(function () use ($template): Generator {
            $builder = clone $template;
            $initialCursor = $builder->buildQuery()[$builder->cursorParameter] ?? null;
            $seen = is_string($initialCursor) ? [$initialCursor => true] : [];
            $cursor = null;

            do {
                if ($cursor !== null) {
                    $builder->after($cursor);
                }

                $response = $builder->executeRequest();
                $data = $builder->responseDecoder()->decodeList(
                    $response,
                    $builder->getEndpoint(),
                    $builder->responseCollectionKey,
                );
                $cursor = Pagination\Cursor::fromResponse($response->json('pagination', []))->next();

                if ($cursor !== null && isset($seen[$cursor])) {
                    throw new SamsaraException('Pagination cursor did not advance.', context: [
                        'endpoint' => $builder->getEndpoint(),
                        'cursor'   => $cursor,
                    ]);
                }

                if ($cursor !== null) {
                    $seen[$cursor] = true;
                }

                foreach ($data as $item) {
                    yield $builder->mapToEntity($item);
                }
            } while ($cursor !== null);
        });
    }

    /**
     * Set the result limit.
     */
    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Map endpoint response items with a custom callback.
     *
     * @template TMapped of Entity
     *
     * @param  callable(array<string, mixed>): TMapped  $mapper
     * @return static<TMapped>
     *
     * @phpstan-return $this
     *
     * @phpstan-this-out static<TMapped>
     */
    public function mapUsing(callable $mapper): static
    {
        /** @var Closure(array<string, mixed>): TEntity $responseMapper */
        $responseMapper = Closure::fromCallable($mapper);
        $this->responseMapper = $responseMapper;

        return $this;
    }

    /**
     * Paginate the query results.
     *
     * @param  int|null  $perPage  The number of items per page
     * @return Pagination\CursorPaginator<TEntity>
     */
    public function paginate(?int $perPage = null): Pagination\CursorPaginator
    {
        if ($perPage !== null) {
            $this->limit($perPage);
        }

        $this->applyDefaultPerPage();

        $response = $this->executeRequest();

        $data = $this->responseDecoder()->decodeList(
            $response,
            $this->getEndpoint(),
            $this->responseCollectionKey,
        );
        $pagination = $response->json('pagination', []);

        $items = $this->mapToEntities($data);
        $cursor = Pagination\Cursor::fromResponse($pagination);

        $initialCursor = $this->buildQuery()[$this->cursorParameter] ?? null;

        return new Pagination\CursorPaginator(
            $items,
            $cursor,
            $this,
            is_string($initialCursor) ? [$initialCursor] : [],
        );
    }

    /**
     * Declare form/explode=true list parameters that use repeated query keys.
     */
    public function repeatQueryParameters(string ...$parameters): static
    {
        $this->repeatedQueryParameters = array_values(array_unique([
            ...$this->repeatedQueryParameters,
            ...$parameters,
        ]));

        return $this;
    }

    /**
     * Set the start time.
     */
    public function startTime(DateTimeInterface|string $time): static
    {
        $this->filters['startTime'] = $this->formatTime($time);

        return $this;
    }

    /**
     * Alias for limit().
     */
    public function take(int $count): static
    {
        return $this->limit($count);
    }

    /**
     * Set the stat types to fetch.
     *
     * @param  VehicleStatType|array<VehicleStatType|string>|string  $types
     */
    public function types(array|string|VehicleStatType $types): static
    {
        $this->types = is_array($types) ? $types : [$types];

        return $this;
    }

    /**
     * Filter by updated after timestamp.
     */
    public function updatedAfter(DateTimeInterface|string $time): static
    {
        $this->filters['updatedAfterTime'] = $this->formatTime($time);

        return $this;
    }

    /**
     * Map endpoint response items into the selected entity class.
     *
     * @template TMapped of Entity
     *
     * @param  class-string<TMapped>  $entityClass
     * @return static<TMapped>
     *
     * @phpstan-this-out static<TMapped>
     */
    public function usingEntity(string $entityClass): static
    {
        return $this->mapUsing(
            fn (array $data): Entity => $entityClass::make($data)
        );
    }

    /**
     * Add a custom filter.
     */
    public function where(string $key, mixed $value): static
    {
        $this->filters[$key] = $value;

        return $this;
    }

    /**
     * Filter by attribute value IDs.
     *
     * @param  array<string>|string  $attributeValueIds
     */
    public function whereAttribute(array|string $attributeValueIds): static
    {
        $this->filters['attributeValueIds'] = $this->serializeList($attributeValueIds);

        return $this;
    }

    /**
     * Filter by driver IDs.
     *
     * @param  array<string>|string  $driverIds
     */
    public function whereDriver(array|string $driverIds): static
    {
        $this->filters['driverIds'] = $this->serializeList($driverIds);

        return $this;
    }

    /**
     * Filter by parent tag IDs.
     *
     * @param  array<string>|string  $parentTagIds
     */
    public function whereParentTag(array|string $parentTagIds): static
    {
        $this->filters['parentTagIds'] = $this->serializeList($parentTagIds);

        return $this;
    }

    /**
     * Filter by tag IDs.
     *
     * @param  array<string>|string  $tagIds
     */
    public function whereTag(array|string $tagIds): static
    {
        $this->filters['tagIds'] = $this->serializeList($tagIds);

        return $this;
    }

    /**
     * Filter by vehicle IDs.
     *
     * @param  array<string>|string  $vehicleIds
     */
    public function whereVehicle(array|string $vehicleIds): static
    {
        $this->filters['vehicleIds'] = $this->serializeList($vehicleIds);

        return $this;
    }

    /**
     * Set the query parameter used to request the next cursor page.
     */
    public function withCursorParameter(string $parameter): static
    {
        $this->cursorParameter = $parameter;

        return $this;
    }

    /**
     * Set the decorations to include.
     *
     * @param  VehicleStatType|array<VehicleStatType|string>|string  $decorations
     */
    public function withDecorations(array|string|VehicleStatType $decorations): static
    {
        $this->decorations = is_array($decorations) ? $decorations : [$decorations];

        return $this;
    }

    /**
     * Disable automatic configured page-size injection for this query.
     */
    public function withoutDefaultPageSize(): static
    {
        $this->defaultPageSizeEnabled = false;

        return $this;
    }

    /**
     * Set the response key containing the result collection.
     */
    public function withResponseCollectionKey(string $key): static
    {
        $this->responseCollectionKey = $key;

        return $this;
    }

    /**
     * Map data to entity collection.
     *
     * @param  array<int, array<string, mixed>>  $data
     * @return EntityCollection<int, TEntity>
     */
    protected function mapToEntities(array $data): EntityCollection
    {
        return new EntityCollection(array_map(
            fn (array $item): Entity => $this->mapToEntity($item),
            $data
        ));
    }

    /**
     * Map data to a single entity.
     *
     * @param  array<string, mixed>  $data
     * @return TEntity
     */
    protected function mapToEntity(array $data): Entity
    {
        if ($this->responseMapper !== null) {
            return ($this->responseMapper)($data);
        }

        // Resource::newQuery() always sets a mapper; direct builders use the resource's entity class.
        /** @var class-string<TEntity> $entityClass */
        $entityClass = $this->resource->getEntityClass();

        return $entityClass::make($data);
    }

    /**
     * Apply the configured page size when no limit was explicitly selected.
     */
    private function applyDefaultPerPage(): void
    {
        if (
            $this->defaultPageSizeEnabled
            && $this->limit === null
            && ! array_key_exists('limit', $this->filters)
        ) {
            $this->limit($this->resource->getDefaultPerPage());
        }
    }

    /**
     * Execute a query through the configured client and map HTTP errors.
     */
    private function executeRequest(): Response
    {
        $endpoint = $this->getEndpoint();
        [$requestEndpoint, $query] = QueryString::prepare(
            $endpoint,
            $this->buildQuery(),
            $this->repeatedQueryParameters,
        );

        return (new RequestExecutor)->execute(
            fn () => $query === []
                ? $this->resource->client()->get($requestEndpoint)
                : $this->resource->client()->get($requestEndpoint, $query),
            fn (Response $response) => $this->resource->throwIfError($response),
            $endpoint,
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
     * Serialize a dedicated list parameter for form/explode=false query encoding.
     *
     * @param  array<BackedEnum|string>|string  $values
     * @return array<string>|string
     */
    private function serializeList(array|string $values): array|string
    {
        if (! is_array($values) || $values === []) {
            return $values;
        }

        return implode(',', array_map(
            fn (string|BackedEnum $value): string|int => $value instanceof BackedEnum ? $value->value : $value,
            $values,
        ));
    }
}
