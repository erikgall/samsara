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
     * Get safety activity event feed.
     *
     * @param  string|null  $startTime  Start time (RFC 3339).
     * @return Response
     */
    public function getActivityEventFeed(?string $startTime = null): Response
    {
        return $this->connector->send(new GetSafetyActivityEventFeed($startTime));
    }

    /**
     * Get safety events.
     *
     * @param  string  $startTime  Start time (RFC 3339).
     * @param  string  $endTime  End time (RFC 3339).
     * @param  array|null  $tagIds  Filter by tag IDs.
     * @param  array|null  $parentTagIds  Filter by parent tag IDs.
     * @param  array|null  $vehicleIds  Filter by vehicle IDs.
     * @return Response
     */
    public function getEvents(
        string $startTime,
        string $endTime,
        ?array $tagIds = null,
        ?array $parentTagIds = null,
        ?array $vehicleIds = null
    ): Response {
        return $this->connector->send(
            new GetSafetyEvents($startTime, $endTime, $tagIds, $parentTagIds, $vehicleIds)
        );
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
