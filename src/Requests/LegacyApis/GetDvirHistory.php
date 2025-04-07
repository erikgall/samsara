<?php

namespace ErikGall\Samsara\Requests\LegacyApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getDvirHistory.
 *
 * **Note: This is a legacy endpoint, consider using [this
 * endpoint](https://developers.samsara.com/reference/getdvirs) instead. The endpoint will continue to
 * function as documented.**
 *
 *  Returns a list of all DVIRs in an organization.
 *
 *  **Submit Feedback**:
 * Likes, dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read DVIRs** under the Maintenance category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class GetDvirHistory extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  array|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     */
    public function __construct(
        protected ?int $limit,
        protected ?array $parentTagIds,
        protected ?array $tagIds,
        protected string $startTime,
        protected string $endTime,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'limit'        => $this->limit,
            'parentTagIds' => $this->parentTagIds,
            'tagIds'       => $this->tagIds,
            'startTime'    => $this->startTime,
            'endTime'      => $this->endTime,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/dvirs/history';
    }
}
