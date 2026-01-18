<?php

namespace Samsara\Query\Pagination;

use Countable;
use Traversable;
use ArrayIterator;
use IteratorAggregate;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Data\EntityCollection;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Paginator for cursor-based API responses.
 *
 * Provides iteration and pagination controls for API results
 * that use cursor-based pagination.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @implements IteratorAggregate<int, Entity>
 * @implements Arrayable<string, mixed>
 */
class CursorPaginator implements Arrayable, Countable, IteratorAggregate
{
    /**
     * The query builder instance.
     */
    protected Builder $builder;

    /**
     * The pagination cursor.
     */
    protected Cursor $cursor;

    /**
     * The items for the current page.
     *
     * @var EntityCollection<int, Entity>
     */
    protected EntityCollection $items;

    /**
     * Create a new cursor paginator instance.
     *
     * @param  EntityCollection<int, Entity>  $items
     */
    public function __construct(EntityCollection $items, Cursor $cursor, Builder $builder)
    {
        $this->items = $items;
        $this->cursor = $cursor;
        $this->builder = $builder;
    }

    /**
     * Get the number of items.
     */
    public function count(): int
    {
        return $this->items->count();
    }

    /**
     * Get the cursor.
     */
    public function cursor(): Cursor
    {
        return $this->cursor;
    }

    /**
     * Get an iterator for the items.
     *
     * @return Traversable<int, Entity>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items->all());
    }

    /**
     * Check if there are more pages.
     */
    public function hasMorePages(): bool
    {
        return $this->cursor->hasNextPage();
    }

    /**
     * Get the items.
     *
     * @return EntityCollection<int, Entity>
     */
    public function items(): EntityCollection
    {
        return $this->items;
    }

    /**
     * Fetch the next page of results.
     */
    public function nextPage(): ?self
    {
        if (! $this->hasMorePages()) {
            return null;
        }

        $endCursor = $this->cursor->getEndCursor();
        if ($endCursor === null) {
            return null;
        }

        return $this->builder
            ->after($endCursor)
            ->getWithPagination();
    }

    /**
     * Convert the paginator to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'data'       => $this->items->toArray(),
            'pagination' => $this->cursor->toArray(),
        ];
    }
}
