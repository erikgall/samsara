<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Equipment\GetEquipment;
use ErikGall\Samsara\Requests\Equipment\ListEquipment;
use ErikGall\Samsara\Requests\Equipment\GetEquipmentStats;
use ErikGall\Samsara\Requests\Equipment\GetEquipmentLocations;
use ErikGall\Samsara\Requests\Equipment\GetEquipmentStatsFeed;
use ErikGall\Samsara\Requests\Equipment\GetEquipmentStatsHistory;
use ErikGall\Samsara\Requests\Equipment\GetEquipmentLocationsFeed;
use ErikGall\Samsara\Requests\Equipment\GetEquipmentLocationsHistory;

class Equipment extends Resource
{
    /**
     * @param  string  $id  Samsara ID of the Equipment.
     */
    public function getEquipment(string $id): Response
    {
        return $this->connector->send(new GetEquipment($id));
    }

    /**
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $equipmentIds  A filter on the data based on this comma-separated list of equipment IDs. Example: `equipmentIds=1234,5678`
     */
    public function getEquipmentLocations(
        ?array $parentTagIds = null,
        ?array $tagIds = null,
        ?array $equipmentIds = null
    ): Response {
        return $this->connector->send(
            new GetEquipmentLocations($parentTagIds, $tagIds, $equipmentIds)
        );
    }

    /**
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $equipmentIds  A filter on the data based on this comma-separated list of equipment IDs. Example: `equipmentIds=1234,5678`
     */
    public function getEquipmentLocationsFeed(
        ?array $parentTagIds = null,
        ?array $tagIds = null,
        ?array $equipmentIds = null
    ): Response {
        return $this->connector->send(
            new GetEquipmentLocationsFeed($parentTagIds, $tagIds, $equipmentIds)
        );
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $equipmentIds  A filter on the data based on this comma-separated list of equipment IDs. Example: `equipmentIds=1234,5678`
     */
    public function getEquipmentLocationsHistory(
        string $startTime,
        string $endTime,
        ?array $parentTagIds = null,
        ?array $tagIds = null,
        ?array $equipmentIds = null
    ): Response {
        return $this->connector->send(
            new GetEquipmentLocationsHistory(
                $startTime,
                $endTime,
                $parentTagIds,
                $tagIds,
                $equipmentIds
            )
        );
    }

    /**
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $equipmentIds  A filter on the data based on this comma-separated list of equipment IDs. Example: `equipmentIds=1234,5678`
     * @param  array  $types  The types of equipment stats you want to query. Currently, you may submit up to 3 types.
     *
     * - `engineRpm`: The revolutions per minute of the engine.
     * - `fuelPercents`: The percent of fuel in the unit of equipment.
     * - `obdEngineSeconds`: The number of seconds the engine has been running since it was new. This value is provided directly from on-board diagnostics.
     * - `gatewayEngineSeconds`: An approximation of the number of seconds the engine has been running since it was new, based on the amount of time the asset gateway has been receiving power with an offset provided manually through the Samsara cloud dashboard. This is supported with the following hardware configurations:
     *   - AG24/AG26/AG46P + APWR cable ([Auxiliary engine configuration](https://kb.samsara.com/hc/en-us/articles/360043040512-Auxiliary-Inputs#UUID-d514abff-d10a-efaf-35d9-e10fa6c4888d) required)
     *   - AG52 + BPWR/BEQP cable ([Auxiliary engine configuration](https://kb.samsara.com/hc/en-us/articles/360043040512-Auxiliary-Inputs#UUID-d514abff-d10a-efaf-35d9-e10fa6c4888d) required).
     * - `gatewayJ1939EngineSeconds`: An approximation of the number of seconds the engine has been running since it was new, based on the amount of time the AG26 device is receiving power via J1939/CAT cable and an offset provided manually through the Samsara cloud dashboard.
     * - `obdEngineStates`: The state of the engine read from on-board diagnostics. Can be `Off`, `On`, or `Idle`.
     * - `gatewayEngineStates`: An approximation of engine state based on readings the AG26 receives from the aux/digio cable. Can be `Off` or `On`.
     * - `gpsOdometerMeters`: An approximation of odometer reading based on GPS calculations since the AG26 was activated, and a manual odometer offset provided in the Samsara cloud dashboard. Valid values: `Off`, `On`.
     * - `gps`: GPS data including lat/long, heading, speed, address book entry (if exists), and a reverse geocoded address.
     * - `engineTotalIdleTimeMinutes`: Total time in minutes that the engine has been idling.
     */
    public function getEquipmentStats(
        ?array $parentTagIds,
        ?array $tagIds,
        ?array $equipmentIds,
        array $types
    ): Response {
        return $this->connector->send(
            new GetEquipmentStats($parentTagIds, $tagIds, $equipmentIds, $types)
        );
    }

    /**
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $equipmentIds  A filter on the data based on this comma-separated list of equipment IDs. Example: `equipmentIds=1234,5678`
     * @param  array  $types  The types of equipment stats you want to query. Currently, you may submit up to 3 types.
     *
     * - `engineRpm`: The revolutions per minute of the engine.
     * - `fuelPercents`: The percent of fuel in the unit of equipment.
     * - `obdEngineSeconds`: The number of seconds the engine has been running since it was new. This value is provided directly from on-board diagnostics.
     * - `gatewayEngineSeconds`: An approximation of the number of seconds the engine has been running since it was new, based on the amount of time the asset gateway has been receiving power with an offset provided manually through the Samsara cloud dashboard. This is supported with the following hardware configurations:
     *   - AG24/AG26/AG46P + APWR cable ([Auxiliary engine configuration](https://kb.samsara.com/hc/en-us/articles/360043040512-Auxiliary-Inputs#UUID-d514abff-d10a-efaf-35d9-e10fa6c4888d) required)
     *   - AG52 + BPWR/BEQP cable ([Auxiliary engine configuration](https://kb.samsara.com/hc/en-us/articles/360043040512-Auxiliary-Inputs#UUID-d514abff-d10a-efaf-35d9-e10fa6c4888d) required).
     * - `gatewayJ1939EngineSeconds`: An approximation of the number of seconds the engine has been running since it was new, based on the amount of time the AG26 device is receiving power via J1939/CAT cable and an offset provided manually through the Samsara cloud dashboard.
     * - `obdEngineStates`: The state of the engine read from on-board diagnostics. Can be `Off`, `On`, or `Idle`.
     * - `gatewayEngineStates`: An approximation of engine state based on readings the AG26 receives from the aux/digio cable. Can be `Off` or `On`.
     * - `gpsOdometerMeters`: An approximation of odometer reading based on GPS calculations since the AG26 was activated, and a manual odometer offset provided in the Samsara cloud dashboard. Valid values: `Off`, `On`.
     * - `gps`: GPS data including lat/long, heading, speed, address book entry (if exists), and a reverse geocoded address.
     * - `engineTotalIdleTimeMinutes`: Total time in minutes that the engine has been idling.
     */
    public function getEquipmentStatsFeed(
        ?array $parentTagIds,
        ?array $tagIds,
        ?array $equipmentIds,
        array $types
    ): Response {
        return $this->connector->send(
            new GetEquipmentStatsFeed($parentTagIds, $tagIds, $equipmentIds, $types)
        );
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $equipmentIds  A filter on the data based on this comma-separated list of equipment IDs. Example: `equipmentIds=1234,5678`
     * @param  array  $types  The types of equipment stats you want to query. Currently, you may submit up to 3 types.
     *
     * - `engineRpm`: The revolutions per minute of the engine.
     * - `fuelPercents`: The percent of fuel in the unit of equipment.
     * - `obdEngineSeconds`: The number of seconds the engine has been running since it was new. This value is provided directly from on-board diagnostics.
     * - `gatewayEngineSeconds`: An approximation of the number of seconds the engine has been running since it was new, based on the amount of time the asset gateway has been receiving power with an offset provided manually through the Samsara cloud dashboard. This is supported with the following hardware configurations:
     *   - AG24/AG26/AG46P + APWR cable ([Auxiliary engine configuration](https://kb.samsara.com/hc/en-us/articles/360043040512-Auxiliary-Inputs#UUID-d514abff-d10a-efaf-35d9-e10fa6c4888d) required)
     *   - AG52 + BPWR/BEQP cable ([Auxiliary engine configuration](https://kb.samsara.com/hc/en-us/articles/360043040512-Auxiliary-Inputs#UUID-d514abff-d10a-efaf-35d9-e10fa6c4888d) required).
     * - `gatewayJ1939EngineSeconds`: An approximation of the number of seconds the engine has been running since it was new, based on the amount of time the AG26 device is receiving power via J1939/CAT cable and an offset provided manually through the Samsara cloud dashboard.
     * - `obdEngineStates`: The state of the engine read from on-board diagnostics. Can be `Off`, `On`, or `Idle`.
     * - `gatewayEngineStates`: An approximation of engine state based on readings the AG26 receives from the aux/digio cable. Can be `Off` or `On`.
     * - `gpsOdometerMeters`: An approximation of odometer reading based on GPS calculations since the AG26 was activated, and a manual odometer offset provided in the Samsara cloud dashboard. Valid values: `Off`, `On`.
     * - `gps`: GPS data including lat/long, heading, speed, address book entry (if exists), and a reverse geocoded address.
     * - `engineTotalIdleTimeMinutes`: Total time in minutes that the engine has been idling.
     */
    public function getEquipmentStatsHistory(
        string $startTime,
        string $endTime,
        ?array $parentTagIds,
        ?array $tagIds,
        ?array $equipmentIds,
        array $types
    ): Response {
        return $this->connector->send(
            new GetEquipmentStatsHistory(
                $startTime,
                $endTime,
                $parentTagIds,
                $tagIds,
                $equipmentIds,
                $types
            )
        );
    }

    /**
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     */
    public function listEquipment(
        ?int $limit = null,
        ?array $parentTagIds = null,
        ?array $tagIds = null
    ): Response {
        return $this->connector->send(new ListEquipment($limit, $parentTagIds, $tagIds));
    }
}
