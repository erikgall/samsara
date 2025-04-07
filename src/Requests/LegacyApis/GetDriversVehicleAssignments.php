<?php

namespace ErikGall\Samsara\Requests\LegacyApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getDriversVehicleAssignments.
 *
 * **Note: This is a legacy endpoint, consider using [this
 * endpoint](https://developers.samsara.com/reference/getdrivervehicleassignments) instead. The
 * endpoint will continue to function as documented.** Get all vehicle assignments for the requested
 * drivers in the requested time range. The only type of assignment supported right now are assignments
 * created through the driver app.
 *
 *  <b>Rate limit:</b> 25 requests/sec (learn more about rate limits
 * <a href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Assignments** under the Assignments category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetDriversVehicleAssignments extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array|null  $driverIds  A filter on the data based on this comma-separated list of driver IDs and externalIds. Example: `driverIds=1234,5678,payroll:4841`
     * @param  string|null  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00). The maximum allowed startTime-endTime range is 7 days.
     * @param  string|null  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00). The maximum allowed startTime-endTime range is 7 days.
     * @param  string|null  $tagIds  A filter on the data based on this comma-separated list of driver tag IDs. Example: `tagIds=1234,5678`
     * @param  string|null  $parentTagIds  A filter on the data based on this comma-separated list of driver parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  string|null  $driverActivationStatus  If value is `deactivated`, only drivers that are deactivated will appear in the response. This parameter will default to `active` if not provided (fetching only active drivers).  Valid values: `active`, `deactivated`
     */
    public function __construct(
        protected ?array $driverIds = null,
        protected ?string $startTime = null,
        protected ?string $endTime = null,
        protected ?string $tagIds = null,
        protected ?string $parentTagIds = null,
        protected ?string $driverActivationStatus = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'driverIds'              => $this->driverIds,
            'startTime'              => $this->startTime,
            'endTime'                => $this->endTime,
            'tagIds'                 => $this->tagIds,
            'parentTagIds'           => $this->parentTagIds,
            'driverActivationStatus' => $this->driverActivationStatus,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/drivers/vehicle-assignments';
    }
}
