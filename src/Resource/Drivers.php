<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Drivers\GetDriver;
use ErikGall\Samsara\Requests\Drivers\ListDrivers;
use ErikGall\Samsara\Requests\Drivers\CreateDriver;
use ErikGall\Samsara\Requests\Drivers\UpdateDriver;

class Drivers extends Resource
{
    /**
     * Create a new Driver resource.
     *
     * @param  array  $payload  The data to create the driver.
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new CreateDriver($payload));
    }

    /**
     * Find a Driver resource by ID.
     *
     * @param  string  $id  ID of the driver. This can either be the Samsara-specified ID, or an external ID. Use `key:value` for external IDs.
     * @return Response
     */
    public function find(string $id): Response
    {
        return $this->connector->send(new GetDriver($id));
    }

    /**
     * Get a list of Driver resources.
     *
     * @param  string|null  $driverActivationStatus  Filter by activation status.
     * @param  int|null  $limit  The limit for how many objects will be in the response.
     * @param  array|null  $parentTagIds  Filter by parent tag IDs.
     * @param  array|null  $tagIds  Filter by tag IDs.
     * @param  array|null  $attributeValueIds  Filter by attribute value IDs.
     * @param  string|null  $updatedAfterTime  Filter by updated after time (RFC 3339).
     * @param  string|null  $createdAfterTime  Filter by created after time (RFC 3339).
     * @return Response
     */
    public function get(
        ?string $driverActivationStatus = null,
        ?int $limit = null,
        ?array $parentTagIds = null,
        ?array $tagIds = null,
        ?array $attributeValueIds = null,
        ?string $updatedAfterTime = null,
        ?string $createdAfterTime = null
    ): Response {
        return $this->connector->send(
            new ListDrivers(
                $driverActivationStatus,
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
     * Update a Driver resource.
     *
     * @param  string  $id  ID of the driver. This can either be the Samsara-specified ID, or an external ID. Use `key:value` for external IDs.
     * @param  array  $payload  The data to update the driver.
     * @return Response
     */
    public function update(string $id, array $payload = []): Response
    {
        return $this->connector->send(new UpdateDriver($id, $payload));
    }
}
