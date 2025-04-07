<?php

namespace ErikGall\Samsara\Requests\HoursOfService;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getHosLogs.
 *
 * Returns HOS logs between a given `startTime` and `endTime`. The logs can be further filtered using
 * tags or by providing a list of driver IDs (including external IDs). The legacy version of this
 * endpoint can be found at
 * [samsara.com/api-legacy](https://www.samsara.com/api-legacy#operation/getFleetHosLogs).
 *
 * **Note:**
 * If data is still being uploaded from the Samsara Driver App, it may not be completely reflected in
 * the response from this endpoint. The best practice is to wait a couple of days before querying this
 * endpoint to make sure that all data from the Samsara Driver App has been uploaded.
 *
 *  <b>Rate
 * limit:</b> 5 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 *  **Submit
 * Feedback**: Likes, dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read ELD Compliance Settings (US)** under the Compliance category when
 * creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class GetHosLogs extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array|null  $driverIds  A filter on the data based on this comma-separated list of driver IDs. Example: `driverIds=1234,5678`
     * @param  string|null  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     */
    public function __construct(
        protected ?array $tagIds = null,
        protected ?array $parentTagIds = null,
        protected ?array $driverIds = null,
        protected ?string $startTime = null,
        protected ?string $endTime = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'tagIds'       => $this->tagIds,
            'parentTagIds' => $this->parentTagIds,
            'driverIds'    => $this->driverIds,
            'startTime'    => $this->startTime,
            'endTime'      => $this->endTime,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/hos/logs';
    }
}
