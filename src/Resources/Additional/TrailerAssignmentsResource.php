<?php

namespace Samsara\Resources\Additional;

use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Trailer\Trailer;

/**
 * TrailerAssignments resource for the Samsara API (Legacy v1).
 *
 * Provides access to legacy trailer assignment endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Entity>
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
     * @param  array<string, mixed>  $params
     */
    public function forTrailer(int $trailerId, array $params = []): Trailer
    {
        $endpoint = "/v1/fleet/trailers/{$trailerId}/assignments";
        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint, $params),
            $endpoint
        );

        return Trailer::make($this->decodeJsonArray($response, $endpoint));
    }

    /**
     * Query all trailer assignments from the wrapped v1 collection.
     *
     * @return Builder<Trailer>
     */
    public function query(): Builder
    {
        return $this->newQuery()
            ->withoutDefaultPageSize()
            ->withCursorParameter('startingAfter')
            ->withResponseCollectionKey('trailers')
            ->usingEntity(Trailer::class);
    }
}
