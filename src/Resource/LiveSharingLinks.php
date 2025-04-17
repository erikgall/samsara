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
    public function createLiveSharingLink(array $payload = []): Response
    {
        return $this->connector->send(new CreateLiveSharingLink($payload));
    }

    /**
     * @param  string  $id  Unique identifier for the Live Sharing Link.
     */
    public function deleteLiveSharingLink(string $id): Response
    {
        return $this->connector->send(new DeleteLiveSharingLink($id));
    }

    /**
     * @param  array  $ids  A filter on the data based on this comma-separated list of Live Share Link IDs
     * @param  string  $type  A filter on the data based on the Live Sharing Link type.  Valid values: `all`, `assetsLocation`, `assetsNearLocation`, `assetsOnRoute`
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 100 objects.
     */
    public function getLiveSharingLinks(
        ?array $ids = null,
        ?string $type = null,
        ?int $limit = null
    ): Response {
        return $this->connector->send(new GetLiveSharingLinks($ids, $type, $limit));
    }

    /**
     * @param  string  $id  Unique identifier for the Live Sharing Link.
     */
    public function updateLiveSharingLink(string $id, array $payload = []): Response
    {
        return $this->connector->send(new UpdateLiveSharingLink($id, $payload));
    }
}
