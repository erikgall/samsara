<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getIssues.
 *
 * Returns all issues data for the specified IDs.
 *
 * **Beta:** This endpoint is in beta and is likely to
 * change before being broadly available. Reach out to your Samsara Representative to have Forms APIs
 * enabled for your organization.
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
class GetIssues extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array  $ids  A comma-separated list containing up to 100 issue IDs to filter on. Can be either a unique Samsara ID or an [external ID](https://developers.samsara.com/docs/external-ids) for the issue.
     * @param  array|null  $include  A comma separated list of additional fields to include on requested objects. Valid values: `externalIds`
     */
    public function __construct(
        protected array $ids,
        protected ?array $include = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['ids' => $this->ids, 'include' => $this->include]);
    }

    public function resolveEndpoint(): string
    {
        return '/issues';
    }
}
