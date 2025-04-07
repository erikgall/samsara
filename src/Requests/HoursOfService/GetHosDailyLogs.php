<?php

namespace ErikGall\Samsara\Requests\HoursOfService;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getHosDailyLogs.
 *
 * Get summarized daily Hours of Service charts for the specified drivers.
 *
 * The time range for a log is
 * defined by the `driver`'s `eldDayStartHour`. This value is configurable per driver.
 *
 * The `startDate`
 * and `endDate` parameters indicate the date range you'd like to retrieve daily logs for. A daily log
 * will be returned if its `startTime` is on any of the days within in this date range (inclusive of
 * `startDate` and `endDate`).
 *
 * **Note:** If data is still being uploaded from the Samsara Driver App,
 * it may not be completely reflected in the response from this endpoint. The best practice is to wait
 * a couple of days before querying this endpoint to make sure that all data from the Samsara Driver
 * App has been uploaded.
 *
 * If you are using the legacy version of this endpoint and looking for its
 * documentation, you can find it
 * [here](https://www.samsara.com/api-legacy#operation/getFleetDriversHosDailyLogs).
 *
 *  <b>Rate
 * limit:</b> 5 requests/sec (learn more about rate limits <a
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
class GetHosDailyLogs extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array|null  $driverIds  A filter on the data based on this comma-separated list of driver IDs and externalIds. Example: `driverIds=1234,5678,payroll:4841`
     * @param  string|null  $startDate  A start date in YYYY-MM-DD. This is a date only without an associated time. Example: `2019-06-13`. This is a required field
     * @param  string|null  $endDate  An end date in YYYY-MM-DD. This is a date only without an associated time. Must be greater than or equal to the start date. Example: `2019-07-21`. This is a required field
     * @param  string|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  string|null  $driverActivationStatus  If value is `deactivated`, only drivers that are deactivated will appear in the response. This parameter will default to `active` if not provided (fetching only active drivers).  Valid values: `active`, `deactivated`
     * @param  string|null  $expand  Expands the specified value(s) in the response object. Expansion populates additional fields in an object, if supported. Unsupported fields are ignored. To expand multiple fields, input a comma-separated list.
     *
     * Valid value: `vehicle`  Valid values: `vehicle`
     */
    public function __construct(
        protected ?array $driverIds = null,
        protected ?string $startDate = null,
        protected ?string $endDate = null,
        protected ?string $tagIds = null,
        protected ?string $parentTagIds = null,
        protected ?string $driverActivationStatus = null,
        protected ?string $expand = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'driverIds'              => $this->driverIds,
            'startDate'              => $this->startDate,
            'endDate'                => $this->endDate,
            'tagIds'                 => $this->tagIds,
            'parentTagIds'           => $this->parentTagIds,
            'driverActivationStatus' => $this->driverActivationStatus,
            'expand'                 => $this->expand,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/hos/daily-logs';
    }
}
