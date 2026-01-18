<?php

namespace Samsara\Resources\Additional;

use Samsara\Resources\Resource;
use Samsara\Data\EntityCollection;

/**
 * TrailerAssignments resource for the Samsara API (Legacy v1).
 *
 * Provides access to legacy trailer assignment endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TrailerAssignmentsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/v1/fleet/trailers/assignments';

    /**
     * Get assignments for a specific trailer.
     *
     * @return EntityCollection<int, \Samsara\Data\Entity>
     */
    public function forTrailer(string $trailerId): EntityCollection
    {
        $response = $this->client()->get("/v1/fleet/trailers/{$trailerId}/assignments");

        $this->handleError($response);

        return $this->mapToEntities($response->json('data', []));
    }
}
