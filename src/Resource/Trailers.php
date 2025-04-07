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
    public function createTrailer(): Response
    {
        return $this->connector->send(new CreateTrailer);
    }

    /**
     * @param  string  $id  Unique identifier for the trailer to delete.
     */
    public function deleteTrailer(string $id): Response
    {
        return $this->connector->send(new DeleteTrailer($id));
    }

    /**
     * @param  string  $id  ID of the trailer. This can either be the Samsara-specified ID, or an external ID. External IDs are customer specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: "key:value". For example, "maintenanceId:250020".
     */
    public function getTrailer(string $id): Response
    {
        return $this->connector->send(new GetTrailer($id));
    }

    /**
     * @param  string  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     */
    public function listTrailers(
        ?string $tagIds = null,
        ?string $parentTagIds = null,
        ?int $limit = null
    ): Response {
        return $this->connector->send(new ListTrailers($tagIds, $parentTagIds, $limit));
    }

    /**
     * @param  string  $id  ID of the trailer. Can be either unique Samsara ID or an [external ID](https://developers.samsara.com/docs/external-ids) for the trailer.
     */
    public function updateTrailer(string $id): Response
    {
        return $this->connector->send(new UpdateTrailer($id));
    }
}
