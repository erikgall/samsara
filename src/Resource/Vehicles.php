<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Vehicles\GetVehicle;
use ErikGall\Samsara\Requests\Vehicles\ListVehicles;
use ErikGall\Samsara\Requests\Vehicles\UpdateVehicle;

class Vehicles extends Resource
{
    /**
     * Find a Vehicle resource by ID.
     *
     * @param  string  $id  ID of the vehicle. This can either be the Samsara-specified ID, or an external ID. Use `key:value` for external IDs.
     * @return Response
     */
    public function find(string $id): Response
    {
        return $this->connector->send(new GetVehicle($id));
    }

    /**
     * Get a list of Vehicle resources.
     *
     * @param  int|null  $limit  The limit for how many objects will be in the response.
     * @param  string|null  $parentTagIds  Filter by parent tag IDs.
     * @param  string|null  $tagIds  Filter by tag IDs.
     * @param  string|null  $attributeValueIds  Filter by attribute value IDs.
     * @param  string|null  $updatedAfterTime  Filter by updated after time (RFC 3339).
     * @param  string|null  $createdAfterTime  Filter by created after time (RFC 3339).
     * @return Response
     */
    public function get(
        ?int $limit = null,
        ?string $parentTagIds = null,
        ?string $tagIds = null,
        ?string $attributeValueIds = null,
        ?string $updatedAfterTime = null,
        ?string $createdAfterTime = null
    ): Response {
        return $this->connector->send(
            new ListVehicles(
                $limit,
                $parentTagIds,
                $tagIds,
                $attributeValueIds,
                $updatedAfterTime,
                $createdAfterTime
            )
        );
    }

    /**
     * Update a Vehicle resource.
     *
     * @param  string  $id  ID of the vehicle. This can either be the Samsara-specified ID, or an external ID. Use `key:value` for external IDs.
     * @param  array  $payload  The data to update the vehicle.
     * @return Response
     */
    public function update(string $id, array $payload = []): Response
    {
        return $this->connector->send(new UpdateVehicle($id, $payload));
    }
}
