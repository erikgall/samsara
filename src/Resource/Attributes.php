<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Attributes\GetAttribute;
use ErikGall\Samsara\Requests\Attributes\CreateAttribute;
use ErikGall\Samsara\Requests\Attributes\DeleteAttribute;
use ErikGall\Samsara\Requests\Attributes\UpdateAttribute;
use ErikGall\Samsara\Requests\Attributes\GetAttributesByEntityType;

class Attributes extends Resource
{
    public function createAttribute(array $payload = []): Response
    {
        return $this->connector->send(new CreateAttribute($payload));
    }

    /**
     * @param  string  $id  Samsara-provided UUID of the attribute.
     * @param  string  $entityType  Denotes the type of entity, driver or asset.
     */
    public function deleteAttribute(string $id, string $entityType): Response
    {
        return $this->connector->send(new DeleteAttribute($id, $entityType));
    }

    /**
     * @param  string  $id  Samsara-provided UUID of the attribute.
     * @param  string  $entityType  Denotes the type of entity, driver or asset.
     */
    public function getAttribute(string $id, string $entityType): Response
    {
        return $this->connector->send(new GetAttribute($id, $entityType));
    }

    /**
     * @param  string  $entityType  Denotes the type of entity, driver or asset.
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     */
    public function getAttributesByEntityType(string $entityType, ?int $limit = null): Response
    {
        return $this->connector->send(new GetAttributesByEntityType($entityType, $limit));
    }

    /**
     * @param  string  $id  Samsara-provided UUID of the attribute.
     */
    public function updateAttribute(string $id, array $payload = []): Response
    {
        return $this->connector->send(new UpdateAttribute($id, $payload));
    }
}
