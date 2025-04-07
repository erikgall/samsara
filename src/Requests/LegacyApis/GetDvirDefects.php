<?php

namespace ErikGall\Samsara\Requests\LegacyApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getDvirDefects.
 *
 * **Note: This is a legacy endpoint, consider using [this
 * endpoint](https://developers.samsara.com/reference/streamdefects) instead. The endpoint will
 * continue to function as documented.**
 *
 * Returns a list of DVIR defects in an organization, filtered
 * by creation time. The maximum time period you can query for is 30 days.
 *
 *  **Submit Feedback**:
 * Likes, dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read Defects** under the Maintenance category when creating or editing an
 * API token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class GetDvirDefects extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00). *The maximum time period you can query for is 30 days.*
     * @param  string  $endTime  An end time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00). *The maximum time period you can query for is 30 days.*
     * @param  bool|null  $isResolved  A filter on the data based on resolution status. Example: `isResolved=true`
     */
    public function __construct(
        protected ?int $limit,
        protected string $startTime,
        protected string $endTime,
        protected ?bool $isResolved = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'limit'      => $this->limit,
            'startTime'  => $this->startTime,
            'endTime'    => $this->endTime,
            'isResolved' => $this->isResolved,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/defects/history';
    }
}
