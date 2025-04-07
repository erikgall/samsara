<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\LegacyApis\GetDvirDefects;
use ErikGall\Samsara\Requests\LegacyApis\GetDvirHistory;
use ErikGall\Samsara\Requests\LegacyApis\GetDriversVehicleAssignments;
use ErikGall\Samsara\Requests\LegacyApis\GetVehiclesDriverAssignments;

class LegacyApis extends Resource
{
    /**
     * @param  array  $driverIds  A filter on the data based on this comma-separated list of driver IDs and externalIds. Example: `driverIds=1234,5678,payroll:4841`
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00). The maximum allowed startTime-endTime range is 7 days.
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00). The maximum allowed startTime-endTime range is 7 days.
     * @param  string  $tagIds  A filter on the data based on this comma-separated list of driver tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $parentTagIds  A filter on the data based on this comma-separated list of driver parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  string  $driverActivationStatus  If value is `deactivated`, only drivers that are deactivated will appear in the response. This parameter will default to `active` if not provided (fetching only active drivers).  Valid values: `active`, `deactivated`
     */
    public function getDriversVehicleAssignments(
        ?array $driverIds = null,
        ?string $startTime = null,
        ?string $endTime = null,
        ?string $tagIds = null,
        ?string $parentTagIds = null,
        ?string $driverActivationStatus = null
    ): Response {
        return $this->connector->send(
            new GetDriversVehicleAssignments(
                $driverIds,
                $startTime,
                $endTime,
                $tagIds,
                $parentTagIds,
                $driverActivationStatus
            )
        );
    }

    /**
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00). *The maximum time period you can query for is 30 days.*
     * @param  string  $endTime  An end time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00). *The maximum time period you can query for is 30 days.*
     * @param  bool  $isResolved  A filter on the data based on resolution status. Example: `isResolved=true`
     */
    public function getDvirDefects(
        ?int $limit,
        string $startTime,
        string $endTime,
        ?bool $isResolved = null
    ): Response {
        return $this->connector->send(
            new GetDvirDefects($limit, $startTime, $endTime, $isResolved)
        );
    }

    /**
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     */
    public function getDvirHistory(
        ?int $limit,
        ?array $parentTagIds,
        ?array $tagIds,
        string $startTime,
        string $endTime
    ): Response {
        return $this->connector->send(
            new GetDvirHistory($limit, $parentTagIds, $tagIds, $startTime, $endTime)
        );
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00). The maximum allowed startTime-endTime range is 7 days.
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00). The maximum allowed startTime-endTime range is 7 days.
     * @param  string  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs and externalIds. Example: `vehicleIds=1234,5678,samsara.vin:1HGBH41JXMN109186`
     * @param  string  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     */
    public function getVehiclesDriverAssignments(
        ?string $startTime = null,
        ?string $endTime = null,
        ?string $vehicleIds = null,
        ?string $tagIds = null,
        ?string $parentTagIds = null
    ): Response {
        return $this->connector->send(
            new GetVehiclesDriverAssignments(
                $startTime,
                $endTime,
                $vehicleIds,
                $tagIds,
                $parentTagIds
            )
        );
    }
}
