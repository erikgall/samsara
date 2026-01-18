<?php

namespace Samsara\Query;

use DateTimeInterface;
use Samsara\Resources\Resource;
use Samsara\Data\EntityCollection;
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
     * The decorations to include.
     *
     * @var array<string>
     */
    protected array $decorations = [];

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
     * The resource instance.
     */
    protected Resource $resource;

    /**
     * The stat types to fetch.
     *
     * @var array<string>
     */
    protected array $types = [];

    /**
     * Create a new query builder instance.
     */
    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
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
            $query['after'] = $this->cursor;
        }

        if (! empty($this->types)) {
            $query['types'] = $this->types;
        }

        if (! empty($this->expand)) {
            $query['expand'] = $this->expand;
        }

        if (! empty($this->decorations)) {
            $query['decorations'] = $this->decorations;
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
        $response = $this->resource->client()->get(
            $this->resource->getEndpoint(),
            $this->buildQuery()
        );

        $data = $response->json('data', []);

        return $this->mapToEntities($data);
    }

    /**
     * Execute the query and get results with pagination information.
     */
    public function getWithPagination(): Pagination\CursorPaginator
    {
        $response = $this->resource->client()->get(
            $this->resource->getEndpoint(),
            $this->buildQuery()
        );

        $data = $response->json('data', []);
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
        $builder = $this;
        if ($chunkSize !== null) {
            $builder = clone $this;
            $builder->limit($chunkSize);
        }

        return LazyCollection::make(function () use ($builder) {
            $cursor = null;

            do {
                if ($cursor !== null) {
                    $builder->after($cursor);
                }

                $response = $this->resource->client()->get(
                    $this->resource->getEndpoint(),
                    $builder->buildQuery()
                );

                $data = $response->json('data', []);
                $pagination = $response->json('pagination', []);

                foreach ($data as $item) {
                    yield $this->mapToEntity($item);
                }

                $cursor = $pagination['endCursor'] ?? null;
                $hasNext = $pagination['hasNextPage'] ?? false;
            } while ($hasNext && $cursor !== null);
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
        $this->filters['attributeValueIds'] = is_array($attributeValueIds)
            ? $attributeValueIds
            : [$attributeValueIds];

        return $this;
    }

    /**
     * Filter by driver IDs.
     *
     * @param  array<string>|string  $driverIds
     */
    public function whereDriver(array|string $driverIds): static
    {
        $this->filters['driverIds'] = is_array($driverIds) ? $driverIds : [$driverIds];

        return $this;
    }

    /**
     * Filter by parent tag IDs.
     *
     * @param  array<string>|string  $parentTagIds
     */
    public function whereParentTag(array|string $parentTagIds): static
    {
        $this->filters['parentTagIds'] = is_array($parentTagIds)
            ? $parentTagIds
            : [$parentTagIds];

        return $this;
    }

    /**
     * Filter by tag IDs.
     *
     * @param  array<string>|string  $tagIds
     */
    public function whereTag(array|string $tagIds): static
    {
        $this->filters['tagIds'] = is_array($tagIds) ? $tagIds : [$tagIds];

        return $this;
    }

    /**
     * Filter by vehicle IDs.
     *
     * @param  array<string>|string  $vehicleIds
     */
    public function whereVehicle(array|string $vehicleIds): static
    {
        $this->filters['vehicleIds'] = is_array($vehicleIds) ? $vehicleIds : [$vehicleIds];

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
        $entityClass = $this->resource->getEntityClass();

        return $entityClass::make($data);
    }
}
