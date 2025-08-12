<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\LiveSharingLinks\GetLiveSharingLinks;
use ErikGall\Samsara\Requests\LiveSharingLinks\CreateLiveSharingLink;
use ErikGall\Samsara\Requests\LiveSharingLinks\DeleteLiveSharingLink;
use ErikGall\Samsara\Requests\LiveSharingLinks\UpdateLiveSharingLink;

class LiveSharingLinks extends Resource
{
    /**
     * Create a new Live Sharing Link resource.
     *
     * @param  array  $payload  The data to create the live sharing link.
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new CreateLiveSharingLink($payload));
    }

    /**
     * Delete a Live Sharing Link resource.
     *
     * @param  string  $id  Unique identifier for the Live Sharing Link.
     * @return Response
     */
    public function delete(string $id): Response
    {
        return $this->connector->send(new DeleteLiveSharingLink($id));
    }

    /**
     * Get a list of Live Sharing Link resources.
     *
     * @param  array|null  $ids  Filter by Live Share Link IDs.
     * @param  string|null  $type  Filter by Live Sharing Link type.
     * @param  int|null  $limit  The limit for how many objects will be in the response.
     * @return Response
     */
    public function get(
        ?array $ids = null,
        ?string $type = null,
        ?int $limit = null
    ): Response {
        return $this->connector->send(new GetLiveSharingLinks($ids, $type, $limit));
    }

    /**
     * Update a Live Sharing Link resource.
     *
     * @param  string  $id  Unique identifier for the Live Sharing Link.
     * @param  array  $payload  The data to update the live sharing link.
     * @return Response
     */
    public function update(string $id, array $payload = []): Response
    {
        return $this->connector->send(new UpdateLiveSharingLink($id, $payload));
    }
}
