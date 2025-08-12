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
    /**
     * Create a new Attribute resource.
     *
     * @param  array  $payload  The data to create the attribute.
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new CreateAttribute($payload));
    }

    /**
     * Delete an Attribute resource.
     *
     * @param  string  $id  Samsara-provided UUID of the attribute.
     * @param  string  $entityType  Denotes the type of entity, driver or asset.
     * @return Response
     */
    public function delete(string $id, string $entityType): Response
    {
        return $this->connector->send(new DeleteAttribute($id, $entityType));
    }

    /**
     * Find an Attribute resource by ID and entity type.
     *
     * @param  string  $id  Samsara-provided UUID of the attribute.
     * @param  string  $entityType  Denotes the type of entity, driver or asset.
     * @return Response
     */
    public function find(string $id, string $entityType): Response
    {
        return $this->connector->send(new GetAttribute($id, $entityType));
    }

    /**
     * Get attributes by entity type.
     *
     * @param  string  $entityType  Denotes the type of entity, driver or asset.
     * @param  int|null  $limit  The limit for how many objects will be in the response.
     * @return Response
     */
    public function getByEntityType(string $entityType, ?int $limit = null): Response
    {
        return $this->connector->send(new GetAttributesByEntityType($entityType, $limit));
    }

    /**
     * Update an Attribute resource.
     *
     * @param  string  $id  Samsara-provided UUID of the attribute.
     * @param  array  $payload  The data to update the attribute.
     * @return Response
     */
    public function update(string $id, array $payload = []): Response
    {
        return $this->connector->send(new UpdateAttribute($id, $payload));
    }
}
