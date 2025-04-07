<?php

namespace ErikGall\Samsara\Requests\PreviewApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getDriverEfficiencyByDrivers.
 *
 * This endpoint will return driver efficiency data that has been collected for your organization and
 * grouped by drivers based on the time parameters passed in. Results are paginated.
 *
 *  <b>Rate
 * limit:</b> 10 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Driver Efficiency** under the Closed Beta category when creating or editing
 * an API token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 * Endpoints in this section are in Preview. These APIs are not
 * functional and are instead for soliciting feedback from our API users on the intended design of this
 * API. Additionally, it is not guaranteed that we will be releasing an endpoint included in this
 * section to production. This means that developers should **NOT** rely on these APIs to build
 * business critical applications
 *
 * - Samsara may change the structure of a preview API's interface
 * without versioning or any notice to API users.
 *
 * - When an endpoint becomes generally available, it
 * will be announced in the API [changelog](https://developers.samsara.com/changelog).
 *
 *
 *  **Submit
 * Feedback**: Likes, dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetDriverEfficiencyByDrivers extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Must be in multiple of hours and at least 1 day before endTime. Timezones are supported. Note that the most recent 72 hours of data may still be processing and is subject to change and latency, so it is not recommended to request data for the most recent 72 hours. (Examples: 2019-06-11T19:00:00Z, 2015-09-12T14:00:00-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Must be in multiple of hours and no later than 3 hours before the current time. Timezones are supported. Note that the most recent 72 hours of data may still be processing and is subject to change and latency, so it is not recommended to request data for the most recent 72 hours. (Examples: 2019-06-13T19:00:00Z, 2015-09-15T14:00:00-04:00).
     * @param  array|null  $driverIds  A filter on the data based on this comma-separated list of driver IDs and externalIds. Example: `driverIds=1234,5678,payroll:4841`
     * @param  array|null  $dataFormats  A comma-separated list of data formats you want to fetch. Valid values: `score`, `raw` and `percentage`. The default data format is `score`. Example: `dataFormats=raw,score`
     * @param  string|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     */
    public function __construct(
        protected string $startTime,
        protected string $endTime,
        protected ?array $driverIds = null,
        protected ?array $dataFormats = null,
        protected ?string $tagIds = null,
        protected ?string $parentTagIds = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'startTime'    => $this->startTime,
            'endTime'      => $this->endTime,
            'driverIds'    => $this->driverIds,
            'dataFormats'  => $this->dataFormats,
            'tagIds'       => $this->tagIds,
            'parentTagIds' => $this->parentTagIds,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/preview/driver-efficiency/drivers';
    }
}
