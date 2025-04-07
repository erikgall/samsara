<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Webhooks\GetWebhook;
use ErikGall\Samsara\Requests\Webhooks\ListWebhooks;
use ErikGall\Samsara\Requests\Webhooks\PatchWebhook;
use ErikGall\Samsara\Requests\Webhooks\PostWebhooks;
use ErikGall\Samsara\Requests\Webhooks\DeleteWebhook;

class Webhooks extends Resource
{
    /**
     * @param  string  $id  Unique identifier for the webhook to delete.
     */
    public function deleteWebhook(string $id): Response
    {
        return $this->connector->send(new DeleteWebhook($id));
    }

    /**
     * @param  string  $id  ID of the webhook. This is the Samsara-specified ID.
     */
    public function getWebhook(string $id): Response
    {
        return $this->connector->send(new GetWebhook($id));
    }

    /**
     * @param  string  $ids  A filter on the data based on this comma-separated list of webhook IDs. Example: `ids=49412323223,49412329928`
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     */
    public function listWebhooks(?string $ids = null, ?int $limit = null): Response
    {
        return $this->connector->send(new ListWebhooks($ids, $limit));
    }

    /**
     * @param  string  $id  Unique identifier for the webhook to update.
     */
    public function patchWebhook(string $id): Response
    {
        return $this->connector->send(new PatchWebhook($id));
    }

    public function postWebhooks(): Response
    {
        return $this->connector->send(new PostWebhooks);
    }
}
