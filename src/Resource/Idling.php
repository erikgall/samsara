<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Idling\GetVehicleIdlingReports;

class Idling extends Resource
{
    /**
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. Note that the most recent 72 hours of data may still be processing and is subject to change and latency, so it is not recommended to request data for the most recent 72 hours. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. Note that the most recent 72 hours of data may still be processing and is subject to change and latency, so it is not recommended to request data for the most recent 72 hours. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs and externalIds. Example: `vehicleIds=1234,5678,samsara.vin:1HGBH41JXMN109186`
     * @param  string  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  bool  $isPtoActive  A filter on the data based on power take-off being active or inactive.
     * @param  int  $minIdlingDurationMinutes  A filter on the data based on a minimum idling duration.
     */
    public function getVehicleIdlingReports(
        ?int $limit,
        string $startTime,
        string $endTime,
        ?string $vehicleIds = null,
        ?string $tagIds = null,
        ?string $parentTagIds = null,
        ?bool $isPtoActive = null,
        ?int $minIdlingDurationMinutes = null
    ): Response {
        return $this->connector->send(
            new GetVehicleIdlingReports(
                $limit,
                $startTime,
                $endTime,
                $vehicleIds,
                $tagIds,
                $parentTagIds,
                $isPtoActive,
                $minIdlingDurationMinutes
            )
        );
    }
}
