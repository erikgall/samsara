<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Addresses\GetAddress;
use ErikGall\Samsara\Requests\Addresses\CreateAddress;
use ErikGall\Samsara\Requests\Addresses\DeleteAddress;
use ErikGall\Samsara\Requests\Addresses\ListAddresses;
use ErikGall\Samsara\Requests\Addresses\UpdateAddress;

class Addresses extends Resource
{
    /**
     * Create a new Address resource.
     *
     * @param  array  $payload  The data to create the address.
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new CreateAddress($payload));
    }

    /**
     * Delete an Address resource.
     *
     * @param  string  $id  ID of the Address. This can either be the Samsara-provided ID or an external ID. Use `key:value` for external IDs.
     * @return Response
     */
    public function delete(string $id): Response
    {
        return $this->connector->send(new DeleteAddress($id));
    }

    /**
     * Find an Address resource by ID.
     *
     * @param  string  $id  ID of the Address. This can either be the Samsara-provided ID or an external ID. Use `key:value` for external IDs.
     * @return Response
     */
    public function find(string $id): Response
    {
        return $this->connector->send(new GetAddress($id));
    }

    /**
     * Get a list of Address resources.
     *
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  array|null  $parentTagIds  Filter by parent tag IDs.
     * @param  array|null  $tagIds  Filter by tag IDs.
     * @param  string|null  $createdAfterTime  Filter by created after time (RFC 3339).
     * @return Response
     */
    public function get(
        ?int $limit = null,
        ?array $parentTagIds = null,
        ?array $tagIds = null,
        ?string $createdAfterTime = null
    ): Response {
        return $this->connector->send(
            new ListAddresses($limit, $parentTagIds, $tagIds, $createdAfterTime)
        );
    }

    /**
     * Update an Address resource.
     *
     * @param  string  $id  ID of the Address. This can either be the Samsara-provided ID or an external ID. Use `key:value` for external IDs.
     * @param  array  $payload  The data to update the address.
     * @return Response
     */
    public function update(string $id, array $payload = []): Response
    {
        return $this->connector->send(new UpdateAddress($id, $payload));
    }
}
