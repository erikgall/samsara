<?php

namespace Samsara\Resources\Fleet;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Trailer\Trailer;
use Samsara\Resources\Concerns\FindsEntities;
use Samsara\Resources\Concerns\CreatesEntities;
use Samsara\Resources\Concerns\DeletesEntities;
use Samsara\Resources\Concerns\UpdatesEntities;

/**
 * Trailers resource for the Samsara API.
 *
 * Provides access to trailer-related endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Trailer>
 */
class TrailersResource extends Resource
{
    /** @use CreatesEntities<Trailer> */
    use CreatesEntities;

    /** @use DeletesEntities<Trailer> */
    use DeletesEntities;

    /** @use FindsEntities<Trailer> */
    use FindsEntities;

    /** @use UpdatesEntities<Trailer> */
    use UpdatesEntities;

    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/trailers';

    /**
     * The entity class for this resource.
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
        return $this->findByEncodedPathSegment(rawurlencode($key).':'.rawurlencode($value));
    }

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<Trailer>
     */
    public function query(): Builder
    {
        return $this->newQuery();
    }
}
