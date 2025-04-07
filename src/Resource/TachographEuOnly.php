<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\TachographEuOnly\GetDriverTachographFiles;
use ErikGall\Samsara\Requests\TachographEuOnly\GetVehicleTachographFiles;
use ErikGall\Samsara\Requests\TachographEuOnly\GetDriverTachographActivity;

class TachographEuOnly extends Resource
{
    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. It can't be more than 30 days past startTime. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array  $driverIds  A filter on the data based on this comma-separated list of driver IDs. Example: `driverIds=1234,5678`
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     */
    public function getDriverTachographActivity(
        string $startTime,
        string $endTime,
        ?array $driverIds = null,
        ?array $parentTagIds = null,
        ?array $tagIds = null
    ): Response {
        return $this->connector->send(
            new GetDriverTachographActivity(
                $startTime,
                $endTime,
                $driverIds,
                $parentTagIds,
                $tagIds
            )
        );
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array  $driverIds  A filter on the data based on this comma-separated list of driver IDs. Example: `driverIds=1234,5678`
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     */
    public function getDriverTachographFiles(
        string $startTime,
        string $endTime,
        ?array $driverIds = null,
        ?array $parentTagIds = null,
        ?array $tagIds = null
    ): Response {
        return $this->connector->send(
            new GetDriverTachographFiles($startTime, $endTime, $driverIds, $parentTagIds, $tagIds)
        );
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs. Example: `vehicleIds=1234,5678`
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     */
    public function getVehicleTachographFiles(
        string $startTime,
        string $endTime,
        ?array $vehicleIds = null,
        ?array $parentTagIds = null,
        ?array $tagIds = null
    ): Response {
        return $this->connector->send(
            new GetVehicleTachographFiles($startTime, $endTime, $vehicleIds, $parentTagIds, $tagIds)
        );
    }
}
