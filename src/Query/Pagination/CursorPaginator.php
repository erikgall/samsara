<?php

namespace Samsara\Query\Pagination;

use Countable;
use Traversable;
use ArrayIterator;
use IteratorAggregate;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Data\EntityCollection;
use Samsara\Exceptions\SamsaraException;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Paginator for cursor-based API responses.
 *
 * Provides iteration and pagination controls for API results
 * that use cursor-based pagination.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @template TEntity of Entity = Entity
 *
 * @implements IteratorAggregate<int, TEntity>
 * @implements Arrayable<string, mixed>
 */
class CursorPaginator implements Arrayable, Countable, IteratorAggregate
{
    /**
     * The query builder instance.
     *
     * @var Builder<TEntity>
     */
    protected Builder $builder;

    /**
     * The pagination cursor.
     */
    protected Cursor $cursor;

    /**
     * The items for the current page.
     *
     * @var EntityCollection<int, TEntity>
     */
    protected EntityCollection $items;

    /**
     * Cursors already used to request pages in this traversal.
     *
     * @var list<string>
     */
    protected array $visitedCursors;

    /**
     * Create a new cursor paginator instance.
     *
     * @param  EntityCollection<int, TEntity>  $items
     * @param  Builder<TEntity>  $builder
     * @param  list<string>  $visitedCursors
     */
    public function __construct(EntityCollection $items, Cursor $cursor, Builder $builder, array $visitedCursors = [])
    {
        $this->items = $items;
        $this->cursor = $cursor;
        $this->builder = clone $builder;
        $this->visitedCursors = $visitedCursors;

        $this->ensureCursorAdvances();
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
     * @return Traversable<int, TEntity>
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
        return $this->cursor->hasNextPage
            && $this->cursor->endCursor !== null
            && $this->cursor->endCursor !== '';
    }

    /**
     * Get the items.
     *
     * @return EntityCollection<int, TEntity>
     */
    public function items(): EntityCollection
    {
        return $this->items;
    }

    /**
     * Fetch the next page of results.
     *
     * @return self<TEntity>|null
     */
    public function nextPage(): ?self
    {
        if (! $this->hasMorePages()) {
            return null;
        }

        $endCursor = $this->cursor->endCursor;
        if ($endCursor === null) {
            return null;
        }

        $nextPage = (clone $this->builder)->after($endCursor)->paginate();
        $nextPage->visitedCursors = [...$this->visitedCursors, $endCursor];
        $nextPage->ensureCursorAdvances();

        return $nextPage;
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

    /**
     * Reject a continuing page whose cursor has already been requested.
     */
    private function ensureCursorAdvances(): void
    {
        if (! $this->hasMorePages() || ! in_array($this->cursor->endCursor, $this->visitedCursors, true)) {
            return;
        }

        throw new SamsaraException('Pagination cursor did not advance.', context: [
            'endpoint' => $this->builder->getEndpoint(),
            'cursor'   => $this->cursor->endCursor,
        ]);
    }
}
