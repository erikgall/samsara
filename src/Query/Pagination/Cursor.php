<?php

namespace Samsara\Query\Pagination;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Represents pagination cursor information from API responses.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @implements Arrayable<string, mixed>
 */
class Cursor implements Arrayable
{
    /**
     * The cursor value for the next page.
     */
    protected ?string $endCursor;

    /**
     * Whether there is a next page.
     */
    protected bool $hasNextPage;

    /**
     * Create a new cursor instance.
     */
    public function __construct(?string $endCursor, bool $hasNextPage)
    {
        $this->endCursor = $endCursor;
        $this->hasNextPage = $hasNextPage;
    }

    /**
     * Get the end cursor value.
     */
    public function getEndCursor(): ?string
    {
        return $this->endCursor;
    }

    /**
     * Check if there is a next page.
     */
    public function hasNextPage(): bool
    {
        return $this->hasNextPage;
    }

    /**
     * Convert the cursor to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'endCursor'   => $this->endCursor,
            'hasNextPage' => $this->hasNextPage,
        ];
    }

    /**
     * Create a cursor from API response data.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        /** @phpstan-ignore new.static */
        return new static(
            $data['endCursor'] ?? null,
            $data['hasNextPage'] ?? false
        );
    }
}
