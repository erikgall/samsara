<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getIssuesStream.
 *
 * Returns all issues data that has been created or modified for your organization based on the time
 * parameters passed in. Results are paginated and are sorted by last modified date. If you include an
 * endTime, the endpoint will return data up until that point (exclusive). If you don’t include an
 * endTime, you can continue to poll the API real-time with the pagination cursor that gets returned on
 * every call.
 *
 * **Beta:** This endpoint is in beta and is likely to change before being broadly
 * available. Reach out to your Samsara Representative to have Forms APIs enabled for your
 * organization.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Issues** under the Closed Beta category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetIssuesStream extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. Value is compared against `updatedAtTime`. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. Value is compared against `updatedAtTime`. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array|null  $status  A comma-separated list containing status values to filter issues on. Valid values: `open`, `inProgress`, `resolved`, `dismissed`
     * @param  array|null  $assetIds  A comma-separated list containing up to 50 asset IDs to filter issues on. Issues with untracked assets can also be included by passing the value: 'untracked'.
     * @param  array|null  $include  A comma separated list of additional fields to include on requested objects. Valid values: `externalIds`
     */
    public function __construct(
        protected string $startTime,
        protected ?string $endTime = null,
        protected ?array $status = null,
        protected ?array $assetIds = null,
        protected ?array $include = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'startTime' => $this->startTime,
            'endTime'   => $this->endTime,
            'status'    => $this->status,
            'assetIds'  => $this->assetIds,
            'include'   => $this->include,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/issues/stream';
    }
}
