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
    public function createAddress(array $payload = []): Response
    {
        return $this->connector->send(new CreateAddress($payload));
    }

    /**
     * @param  string  $id  ID of the Address. This can either be the Samsara-provided ID or an external ID. External IDs are customer-specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `crmId:abc123`
     */
    public function deleteAddress(string $id): Response
    {
        return $this->connector->send(new DeleteAddress($id));
    }

    /**
     * @param  string  $id  ID of the Address. This can either be the Samsara-provided ID or an external ID. External IDs are customer-specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `crmId:abc123`
     */
    public function getAddress(string $id): Response
    {
        return $this->connector->send(new GetAddress($id));
    }

    /**
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $createdAfterTime  A filter on data to have a created at time after or equal to this specified time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     */
    public function listAddresses(
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
     * @param  string  $id  ID of the Address. This can either be the Samsara-provided ID or an external ID. External IDs are customer-specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `crmId:abc123`
     */
    public function updateAddress(string $id, array $payload = []): Response
    {
        return $this->connector->send(new UpdateAddress($id, $payload));
    }
}
