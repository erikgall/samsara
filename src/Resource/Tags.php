<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Tags\GetTag;
use ErikGall\Samsara\Requests\Tags\ListTags;
use ErikGall\Samsara\Requests\Tags\PatchTag;
use ErikGall\Samsara\Requests\Tags\CreateTag;
use ErikGall\Samsara\Requests\Tags\DeleteTag;
use ErikGall\Samsara\Requests\Tags\ReplaceTag;

class Tags extends Resource
{
    /**
     * Create a new Tag resource.
     *
     * @param  array  $payload  The data to create the tag.
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new CreateTag($payload));
    }

    /**
     * Delete a Tag resource.
     *
     * @param  string  $id  ID of the Tag. This can either be the Samsara-provided ID or an external ID. Use `key:value` for external IDs.
     * @return Response
     */
    public function delete(string $id): Response
    {
        return $this->connector->send(new DeleteTag($id));
    }

    /**
     * Find a Tag resource by ID.
     *
     * @param  string  $id  ID of the Tag. This can either be the Samsara-provided ID or an external ID. Use `key:value` for external IDs.
     * @return Response
     */
    public function find(string $id): Response
    {
        return $this->connector->send(new GetTag($id));
    }

    /**
     * Get a list of Tag resources.
     *
     * @param  int|null  $limit  The limit for how many objects will be in the response.
     * @return Response
     */
    public function get(?int $limit = null): Response
    {
        return $this->connector->send(new ListTags($limit));
    }

    /**
     * @param  string  $id  ID of the Tag. This can either be the Samsara-provided ID or an external ID. External IDs are customer-specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `crmId:abc123`. Automatically populated external IDs are prefixed with `samsara.`. For example, `samsara.name:ELD-exempt`.
     */
    public function replaceTag(string $id, array $payload = []): Response
    {
        return $this->connector->send(new ReplaceTag($id, $payload));
    }

    /**
     * Update a Tag resource.
     *
     * @param  string  $id  ID of the Tag. This can either be the Samsara-provided ID or an external ID. Use `key:value` for external IDs.
     * @param  array  $payload  The data to update the tag.
     * @return Response
     */
    public function update(string $id, array $payload = []): Response
    {
        return $this->connector->send(new PatchTag($id, $payload));
    }
}
