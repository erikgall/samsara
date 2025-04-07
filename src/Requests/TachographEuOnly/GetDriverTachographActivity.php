<?php

namespace ErikGall\Samsara\Requests\TachographEuOnly;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getDriverTachographActivity.
 *
 * Returns all known tachograph activity for all specified drivers in the time range.
 *
 *  **Submit
 * Feedback**: Likes, dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read Tachograph (EU)** under the Compliance category when creating or
 * editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class GetDriverTachographActivity extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. It can't be more than 30 days past startTime. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array|null  $driverIds  A filter on the data based on this comma-separated list of driver IDs. Example: `driverIds=1234,5678`
     * @param  array|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     */
    public function __construct(
        protected string $startTime,
        protected string $endTime,
        protected ?array $driverIds = null,
        protected ?array $parentTagIds = null,
        protected ?array $tagIds = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'startTime'    => $this->startTime,
            'endTime'      => $this->endTime,
            'driverIds'    => $this->driverIds,
            'parentTagIds' => $this->parentTagIds,
            'tagIds'       => $this->tagIds,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/drivers/tachograph-activity/history';
    }
}
