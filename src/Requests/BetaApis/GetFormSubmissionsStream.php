<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getFormSubmissionsStream.
 *
 * Returns all form submissions data that has been created or modified for your organization based on
 * the time parameters passed in. Results are paginated and are sorted by last modified date. If you
 * include an endTime, the endpoint will return data up until that point (exclusive). If you don’t
 * include an endTime, you can continue to poll the API real-time with the pagination cursor that gets
 * returned on every call.
 *
 * **Beta:** This endpoint is in beta and is likely to change before being
 * broadly available. Reach out to your Samsara Representative to have Forms APIs enabled for your
 * organization.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Form Submissions** under the Closed Beta category when creating or editing
 * an API token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetFormSubmissionsStream extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. Value is compared against `updatedAtTime`. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. Value is compared against `updatedAtTime`. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array|null  $formTemplateIds  A comma-separated list containing up to 50 template IDs to filter data to.
     * @param  array|null  $userIds  A comma-separated list containing up to 50 user IDs to filter data to.
     * @param  array|null  $driverIds  A comma-separated list containing up to 50 user IDs to filter data to.
     * @param  array|null  $include  A comma-separated list of strings indicating whether to return additional information. Valid values: `externalIds`, `fieldLabels`
     * @param  array|null  $assignedToRouteStopIds  A comma-separated list containing up to 50 route stop IDs to filter data to.
     */
    public function __construct(
        protected string $startTime,
        protected ?string $endTime = null,
        protected ?array $formTemplateIds = null,
        protected ?array $userIds = null,
        protected ?array $driverIds = null,
        protected ?array $include = null,
        protected ?array $assignedToRouteStopIds = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'startTime'              => $this->startTime,
            'endTime'                => $this->endTime,
            'formTemplateIds'        => $this->formTemplateIds,
            'userIds'                => $this->userIds,
            'driverIds'              => $this->driverIds,
            'include'                => $this->include,
            'assignedToRouteStopIds' => $this->assignedToRouteStopIds,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/form-submissions/stream';
    }
}
