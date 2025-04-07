<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\HoursOfService\GetHosLogs;
use ErikGall\Samsara\Requests\HoursOfService\GetHosClocks;
use ErikGall\Samsara\Requests\HoursOfService\GetHosDailyLogs;
use ErikGall\Samsara\Requests\HoursOfService\GetHosViolations;
use ErikGall\Samsara\Requests\HoursOfService\SetCurrentDutyStatus;
use ErikGall\Samsara\Requests\HoursOfService\V1getFleetHosAuthenticationLogs;

class HoursOfService extends Resource
{
    /**
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $driverIds  A filter on the data based on this comma-separated list of driver IDs. Example: `driverIds=1234,5678`
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     */
    public function getHosClocks(?array $tagIds, ?array $parentTagIds, ?array $driverIds, ?int $limit): Response
    {
        return $this->connector->send(new GetHosClocks($tagIds, $parentTagIds, $driverIds, $limit));
    }

    /**
     * @param  array  $driverIds  A filter on the data based on this comma-separated list of driver IDs and externalIds. Example: `driverIds=1234,5678,payroll:4841`
     * @param  string  $startDate  A start date in YYYY-MM-DD. This is a date only without an associated time. Example: `2019-06-13`. This is a required field
     * @param  string  $endDate  An end date in YYYY-MM-DD. This is a date only without an associated time. Must be greater than or equal to the start date. Example: `2019-07-21`. This is a required field
     * @param  string  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  string  $driverActivationStatus  If value is `deactivated`, only drivers that are deactivated will appear in the response. This parameter will default to `active` if not provided (fetching only active drivers).  Valid values: `active`, `deactivated`
     * @param  string  $expand  Expands the specified value(s) in the response object. Expansion populates additional fields in an object, if supported. Unsupported fields are ignored. To expand multiple fields, input a comma-separated list.
     *
     * Valid value: `vehicle`  Valid values: `vehicle`
     */
    public function getHosDailyLogs(
        ?array $driverIds,
        ?string $startDate,
        ?string $endDate,
        ?string $tagIds,
        ?string $parentTagIds,
        ?string $driverActivationStatus,
        ?string $expand,
    ): Response {
        return $this->connector->send(new GetHosDailyLogs($driverIds, $startDate, $endDate, $tagIds, $parentTagIds, $driverActivationStatus, $expand));
    }

    /**
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $driverIds  A filter on the data based on this comma-separated list of driver IDs. Example: `driverIds=1234,5678`
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     */
    public function getHosLogs(
        ?array $tagIds,
        ?array $parentTagIds,
        ?array $driverIds,
        ?string $startTime,
        ?string $endTime,
    ): Response {
        return $this->connector->send(new GetHosLogs($tagIds, $parentTagIds, $driverIds, $startTime, $endTime));
    }

    /**
     * @param  array  $driverIds  A filter on the data based on this comma-separated list of driver IDs and externalIds. Example: `driverIds=1234,5678,payroll:4841`
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $types  A filter on violations data based on the violation type enum. Supported types: `NONE, californiaMealbreakMissed, cycleHoursOn, cycleOffHoursAfterOnDutyHours, dailyDrivingHours, dailyOffDutyDeferralAddToDay2Consecutive, dailyOffDutyDeferralNotPartMandatory, dailyOffDutyDeferralTwoDayDrivingLimit, dailyOffDutyDeferralTwoDayOffDuty, dailyOffDutyNonResetHours, dailyOffDutyTotalHours, dailyOnDutyHours, mandatory24HoursOffDuty, restbreakMissed, shiftDrivingHours, shiftHours, shiftOnDutyHours, unsubmittedLogs`
     */
    public function getHosViolations(
        ?array $driverIds,
        ?string $startTime,
        ?string $endTime,
        ?string $tagIds,
        ?string $parentTagIds,
        ?array $types,
    ): Response {
        return $this->connector->send(new GetHosViolations($driverIds, $startTime, $endTime, $tagIds, $parentTagIds, $types));
    }

    /**
     * @param  int  $driverId  ID of the driver for whom the duty status is being set.
     */
    public function setCurrentDutyStatus(int $driverId): Response
    {
        return $this->connector->send(new SetCurrentDutyStatus($driverId));
    }

    /**
     * @param  int  $driverId  Driver ID to query.
     * @param  int  $startMs  Beginning of the time range, specified in milliseconds UNIX time.
     * @param  int  $endMs  End of the time range, specified in milliseconds UNIX time.
     */
    public function v1getFleetHosAuthenticationLogs(int $driverId, int $startMs, int $endMs): Response
    {
        return $this->connector->send(new V1getFleetHosAuthenticationLogs($driverId, $startMs, $endMs));
    }
}
