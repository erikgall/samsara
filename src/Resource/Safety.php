<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Safety\GetSafetyEvents;
use ErikGall\Samsara\Requests\Safety\V1getDriverSafetyScore;
use ErikGall\Samsara\Requests\Safety\V1getVehicleHarshEvent;
use ErikGall\Samsara\Requests\Safety\V1getVehicleSafetyScore;
use ErikGall\Samsara\Requests\Safety\GetSafetyActivityEventFeed;

class Safety extends Resource
{
    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     */
    public function getSafetyActivityEventFeed(?string $startTime): Response
    {
        return $this->connector->send(new GetSafetyActivityEventFeed($startTime));
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs. Example: `vehicleIds=1234,5678`
     */
    public function getSafetyEvents(
        string $startTime,
        string $endTime,
        ?array $tagIds,
        ?array $parentTagIds,
        ?array $vehicleIds,
    ): Response {
        return $this->connector->send(new GetSafetyEvents($startTime, $endTime, $tagIds, $parentTagIds, $vehicleIds));
    }

    /**
     * @param  int  $driverId  ID of the driver. Must contain only digits 0-9.
     * @param  int  $startMs  Timestamp in milliseconds representing the start of the period to fetch, inclusive. Used in combination with endMs. Total duration (endMs - startMs) must be greater than or equal to 1 hour.
     * @param  int  $endMs  Timestamp in milliseconds representing the end of the period to fetch, inclusive. Used in combination with startMs. Total duration (endMs - startMs) must be greater than or equal to 1 hour.
     */
    public function v1getDriverSafetyScore(int $driverId, int $startMs, int $endMs): Response
    {
        return $this->connector->send(new V1getDriverSafetyScore($driverId, $startMs, $endMs));
    }

    /**
     * @param  int  $vehicleId  ID of the vehicle. Must contain only digits 0-9.
     * @param  int  $timestamp  Timestamp in milliseconds representing the timestamp of a harsh event.
     */
    public function v1getVehicleHarshEvent(int $vehicleId, int $timestamp): Response
    {
        return $this->connector->send(new V1getVehicleHarshEvent($vehicleId, $timestamp));
    }

    /**
     * @param  int  $vehicleId  ID of the vehicle. Must contain only digits 0-9.
     * @param  int  $startMs  Timestamp in milliseconds representing the start of the period to fetch, inclusive. Used in combination with endMs. Total duration (endMs - startMs) must be greater than or equal to 1 hour.
     * @param  int  $endMs  Timestamp in milliseconds representing the end of the period to fetch, inclusive. Used in combination with startMs. Total duration (endMs - startMs) must be greater than or equal to 1 hour.
     */
    public function v1getVehicleSafetyScore(int $vehicleId, int $startMs, int $endMs): Response
    {
        return $this->connector->send(new V1getVehicleSafetyScore($vehicleId, $startMs, $endMs));
    }
}
