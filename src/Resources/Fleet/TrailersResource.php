<?php

namespace ErikGall\Samsara\Resources\Fleet;

use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Resources\Resource;
use ErikGall\Samsara\Data\Trailer\Trailer;

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
        $result = $this->query()
            ->where("externalIds[{$key}]", $value)
            ->first();

        if ($result === null) {
            return null;
        }

        /** @var Trailer */
        return $result;
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }
}
