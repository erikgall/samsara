<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Trailers\GetTrailer;
use ErikGall\Samsara\Requests\Trailers\ListTrailers;
use ErikGall\Samsara\Requests\Trailers\CreateTrailer;
use ErikGall\Samsara\Requests\Trailers\DeleteTrailer;
use ErikGall\Samsara\Requests\Trailers\UpdateTrailer;

class Trailers extends Resource
{
    /**
     * Create a new Trailer resource.
     *
     * @param  array  $payload  The data to create the trailer.
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new CreateTrailer($payload));
    }

    /**
     * Delete a Trailer resource.
     *
     * @param  string  $id  Unique identifier for the trailer to delete.
     * @return Response
     */
    public function delete(string $id): Response
    {
        return $this->connector->send(new DeleteTrailer($id));
    }

    /**
     * Find a Trailer resource by ID.
     *
     * @param  string  $id  ID of the trailer. This can either be the Samsara-specified ID, or an external ID. Use `key:value` for external IDs.
     * @return Response
     */
    public function find(string $id): Response
    {
        return $this->connector->send(new GetTrailer($id));
    }

    /**
     * Get a list of Trailer resources.
     *
     * @param  string|null  $tagIds  Filter by tag IDs.
     * @param  string|null  $parentTagIds  Filter by parent tag IDs.
     * @param  int|null  $limit  The limit for how many objects will be in the response.
     * @return Response
     */
    public function get(
        ?string $tagIds = null,
        ?string $parentTagIds = null,
        ?int $limit = null
    ): Response {
        return $this->connector->send(new ListTrailers($tagIds, $parentTagIds, $limit));
    }

    /**
     * Update a Trailer resource.
     *
     * @param  string  $id  ID of the trailer. Can be either unique Samsara ID or an external ID.
     * @param  array  $payload  The data to update the trailer.
     * @return Response
     */
    public function update(string $id, array $payload = []): Response
    {
        return $this->connector->send(new UpdateTrailer($id, $payload));
    }
}
