<?php

namespace Samsara\Resources\Fleet;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Trailer\Trailer;

/**
 * Trailers resource for the Samsara API.
 *
 * Provides access to trailer-related endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TrailersResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/trailers';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Trailer>
     */
    protected string $entity = Trailer::class;

    /**
     * Find a trailer by external ID.
     *
     * @param  string  $key  The external ID key
     * @param  string  $value  The external ID value
     */
    public function findByExternalId(string $key, string $value): ?Trailer
    {
        /** @var Trailer|null */
        return $this->find(rawurlencode($key).':'.rawurlencode($value));
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
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
}
