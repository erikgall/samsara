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
    public function createTag(): Response
    {
        return $this->connector->send(new CreateTag);
    }

    /**
     * @param  string  $id  ID of the Tag. This can either be the Samsara-provided ID or an external ID. External IDs are customer-specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `crmId:abc123`. Automatically populated external IDs are prefixed with `samsara.`. For example, `samsara.name:ELD-exempt`.
     */
    public function deleteTag(string $id): Response
    {
        return $this->connector->send(new DeleteTag($id));
    }

    /**
     * @param  string  $id  ID of the Tag. This can either be the Samsara-provided ID or an external ID. External IDs are customer-specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `crmId:abc123`. Automatically populated external IDs are prefixed with `samsara.`. For example, `samsara.name:ELD-exempt`.
     */
    public function getTag(string $id): Response
    {
        return $this->connector->send(new GetTag($id));
    }

    /**
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     */
    public function listTags(?int $limit): Response
    {
        return $this->connector->send(new ListTags($limit));
    }

    /**
     * @param  string  $id  ID of the Tag. This can either be the Samsara-provided ID or an external ID. External IDs are customer-specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `crmId:abc123`. Automatically populated external IDs are prefixed with `samsara.`. For example, `samsara.name:ELD-exempt`.
     */
    public function patchTag(string $id): Response
    {
        return $this->connector->send(new PatchTag($id));
    }

    /**
     * @param  string  $id  ID of the Tag. This can either be the Samsara-provided ID or an external ID. External IDs are customer-specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `crmId:abc123`. Automatically populated external IDs are prefixed with `samsara.`. For example, `samsara.name:ELD-exempt`.
     */
    public function replaceTag(string $id): Response
    {
        return $this->connector->send(new ReplaceTag($id));
    }
}
