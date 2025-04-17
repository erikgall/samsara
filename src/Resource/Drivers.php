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
    public function createDriver(array $payload = []): Response
    {
        return $this->connector->send(new CreateDriver($payload));
    }

    /**
     * @param  string  $id  ID of the driver. This can either be the Samsara-specified ID, or an external ID. External IDs are customer specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `payrollId:ABFS18600`
     */
    public function getDriver(string $id): Response
    {
        return $this->connector->send(new GetDriver($id));
    }

    /**
     * @param  string  $driverActivationStatus  If value is `deactivated`, only drivers that are deactivated will appear in the response. This parameter will default to `active` if not provided (fetching only active drivers).
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $attributeValueIds  A filter on the data based on this comma-separated list of attribute value IDs. Only entities associated with ALL of the referenced values will be returned (i.e. the intersection of the sets of entities with each value). Example: `attributeValueIds=076efac2-83b5-47aa-ba36-18428436dcac,6707b3f0-23b9-4fe3-b7be-11be34aea544`
     * @param  string  $updatedAfterTime  A filter on data to have an updated at time after or equal to this specified time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $createdAfterTime  A filter on data to have a created at time after or equal to this specified time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     */
    public function listDrivers(
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
     * @param  string  $id  ID of the driver. This can either be the Samsara-specified ID, or an external ID. External IDs are customer specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `payrollId:ABFS18600`
     * @param  array  $payload
     */
    public function updateDriver(string $id, array $payload = []): Response
    {
        return $this->connector->send(new UpdateDriver($id, $payload));
    }
}
