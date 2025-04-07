<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\VehicleLocations\GetVehicleLocations;
use ErikGall\Samsara\Requests\VehicleLocations\GetVehicleLocationsFeed;
use ErikGall\Samsara\Requests\VehicleLocations\GetVehicleLocationsHistory;

class VehicleLocations extends Resource
{
    /**
     * @param  string  $time  A filter on the data that returns the last known data points with timestamps less than or equal to this value. Defaults to now if not provided. Must be a string in RFC 3339 format. Millisecond precision and timezones are supported. (Example: `2020-01-27T07:06:25Z`).
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs. Example: `vehicleIds=1234,5678`
     */
    public function getVehicleLocations(
        ?string $time,
        ?array $parentTagIds,
        ?array $tagIds,
        ?array $vehicleIds,
    ): Response {
        return $this->connector->send(new GetVehicleLocations($time, $parentTagIds, $tagIds, $vehicleIds));
    }

    /**
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs. Example: `vehicleIds=1234,5678`
     */
    public function getVehicleLocationsFeed(?array $parentTagIds, ?array $tagIds, ?array $vehicleIds): Response
    {
        return $this->connector->send(new GetVehicleLocationsFeed($parentTagIds, $tagIds, $vehicleIds));
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs. Example: `vehicleIds=1234,5678`
     */
    public function getVehicleLocationsHistory(
        string $startTime,
        string $endTime,
        ?array $parentTagIds,
        ?array $tagIds,
        ?array $vehicleIds,
    ): Response {
        return $this->connector->send(new GetVehicleLocationsHistory($startTime, $endTime, $parentTagIds, $tagIds, $vehicleIds));
    }
}
