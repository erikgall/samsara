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
final class Cursor implements Arrayable
{
    /**
     * Create a new cursor instance.
     */
    public function __construct(
        public readonly ?string $endCursor,
        public readonly bool $hasNextPage,
    ) {}

    public function next(): ?string
    {
        return $this->hasNextPage && $this->endCursor !== '' ? $this->endCursor : null;
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
    public static function fromResponse(array $data): self
    {
        return new self(
            $data['endCursor'] ?? null,
            $data['hasNextPage'] ?? false
        );
    }
}
