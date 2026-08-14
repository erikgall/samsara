<?php

namespace Samsara\Query;

use Closure;
use DateTimeInterface;
use Samsara\Data\Entity;
use Samsara\Http\QueryString;
use Samsara\Resources\Resource;
use Samsara\Http\RequestExecutor;
use Samsara\Http\ResponseDecoder;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Response;
use Illuminate\Support\LazyCollection;
use Samsara\Concerns\InteractsWithTime;
use Samsara\Contracts\QueryBuilderInterface;

/**
 * Query builder for API requests.
 *
 * Provides a fluent interface for building API queries with
 * filtering, pagination, and time range options.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class Builder implements QueryBuilderInterface
{
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
     * @var array<string>
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
     * @var (Closure(array<string, mixed>): Entity)|null
     */
    protected ?Closure $responseMapper = null;

    /**
     * The stat types to fetch.
     *
     * @var array<string>
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
     */
    public function first(): ?object
    {
        $results = $this->limit(1)->get();

        return $results->first();
    }

    /**
     * Execute the query and get results.
     *
     * @return EntityCollection<int, \Samsara\Data\Entity>
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
     * Execute the query and get results with pagination information.
     */
    public function getWithPagination(): Pagination\CursorPaginator
    {
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

        return new Pagination\CursorPaginator($items, $cursor, $this);
    }

    /**
     * Get results as a lazy collection.
     *
     * @return LazyCollection<int, \Samsara\Data\Entity>
     */
    public function lazy(?int $chunkSize = null): LazyCollection
    {
        $template = clone $this;
        if ($chunkSize !== null) {
            $template->limit($chunkSize);
        }

        $template->applyDefaultPerPage();

        return LazyCollection::make(function () use ($template) {
            $builder = clone $template;
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
                $pagination = $response->json('pagination', []);

                foreach ($data as $item) {
                    yield $builder->mapToEntity($item);
                }

                $cursor = $pagination['endCursor'] ?? null;
                $hasNext = $pagination['hasNextPage'] ?? false;
                if (! $hasNext || $cursor === '') {
                    $cursor = null;
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
     * @param  callable(array<string, mixed>): Entity  $mapper
     */
    public function mapUsing(callable $mapper): static
    {
        $this->responseMapper = Closure::fromCallable($mapper);

        return $this;
    }

    /**
     * Paginate the query results.
     *
     * @param  int|null  $perPage  The number of items per page
     */
    public function paginate(?int $perPage = null): Pagination\CursorPaginator
    {
        if ($perPage !== null) {
            $this->limit($perPage);
        }

        return $this->getWithPagination();
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
     * @param  array<string>|string  $types
     */
    public function types(array|string $types): static
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
     * @param  class-string<Entity>  $entityClass
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
     * @param  array<string>|string  $decorations
     */
    public function withDecorations(array|string $decorations): static
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
     * @return EntityCollection<int, \Samsara\Data\Entity>
     */
    protected function mapToEntities(array $data): EntityCollection
    {
        /** @var array<int, \Samsara\Data\Entity> $entities */
        $entities = array_map(
            fn (array $item) => $this->mapToEntity($item),
            $data
        );

        return new EntityCollection($entities);
    }

    /**
     * Map data to a single entity.
     *
     * @param  array<string, mixed>  $data
     */
    protected function mapToEntity(array $data): object
    {
        if ($this->responseMapper !== null) {
            return ($this->responseMapper)($data);
        }

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
     * @param  array<string>|string  $values
     * @return array<string>|string
     */
    private function serializeList(array|string $values): array|string
    {
        return is_array($values) && $values !== []
            ? implode(',', $values)
            : $values;
    }
}
