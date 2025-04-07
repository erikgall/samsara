<?php

namespace ErikGall\Samsara\Requests\HoursOfService;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getHosViolations.
 *
 * Get active Hours of Service violations for the specified drivers.
 *
 * The day object time range for a
 * violation is defined by the `driver`'s `eldDayStartHour`. This value is configurable per
 * driver.
 *
 * The `startTime` and `endTime` parameters indicate the datetime range you'd like to retrieve
 * violations for. A violation will be returned if its `violationStartTime` falls within this datetime
 * range (inclusive of `startTime` and `endTime`)
 *
 * **Note:** The following are all the violation types
 * with a short explanation about what each of them means: `californiaMealbreakMissed` (Missed
 * California Meal Break), `cycleHoursOn` (Cycle Limit), `cycleOffHoursAfterOnDutyHours` (Cycle 2
 * Limit), `dailyDrivingHours` (Daily Driving Limit), `dailyOffDutyDeferralAddToDay2Consecutive` (Daily
 * Off-Duty Deferral: Add To Day2 Consecutive), `dailyOffDutyDeferralNotPartMandatory` (Daily Off-Duty
 * Deferral: Not Part Of Mandatory), `dailyOffDutyDeferralTwoDayDrivingLimit` (Daily Off-Duty Deferral:
 * 2 Day Driving Limit), `dailyOffDutyDeferralTwoDayOffDuty` (Daily Off-Duty Deferral: 2 Day Off Duty),
 * `dailyOffDutyNonResetHours` (Daily Off-Duty Time: Non-Reset), `dailyOffDutyTotalHours` (Daily
 * Off-Duty Time), `dailyOnDutyHours` (Daily On-Duty Limit), `mandatory24HoursOffDuty` (24 Hours of Off
 * Duty required), `restbreakMissed` (Missed Rest Break), `shiftDrivingHours` (Shift Driving Limit),
 * `shiftHours` (Shift Duty Limit), `shiftOnDutyHours` (Shift On-Duty Limit), `unsubmittedLogs`
 * (Missing Driver Certification)
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read ELD Compliance Settings (US)** under the Compliance category when creating
 * or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetHosViolations extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array|null  $driverIds  A filter on the data based on this comma-separated list of driver IDs and externalIds. Example: `driverIds=1234,5678,payroll:4841`
     * @param  string|null  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array|null  $types  A filter on violations data based on the violation type enum. Supported types: `NONE, californiaMealbreakMissed, cycleHoursOn, cycleOffHoursAfterOnDutyHours, dailyDrivingHours, dailyOffDutyDeferralAddToDay2Consecutive, dailyOffDutyDeferralNotPartMandatory, dailyOffDutyDeferralTwoDayDrivingLimit, dailyOffDutyDeferralTwoDayOffDuty, dailyOffDutyNonResetHours, dailyOffDutyTotalHours, dailyOnDutyHours, mandatory24HoursOffDuty, restbreakMissed, shiftDrivingHours, shiftHours, shiftOnDutyHours, unsubmittedLogs`
     */
    public function __construct(
        protected ?array $driverIds = null,
        protected ?string $startTime = null,
        protected ?string $endTime = null,
        protected ?string $tagIds = null,
        protected ?string $parentTagIds = null,
        protected ?array $types = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'driverIds'    => $this->driverIds,
            'startTime'    => $this->startTime,
            'endTime'      => $this->endTime,
            'tagIds'       => $this->tagIds,
            'parentTagIds' => $this->parentTagIds,
            'types'        => $this->types,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/hos/violations';
    }
}
