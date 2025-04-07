<?php

namespace ErikGall\Samsara\Requests\Routes;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getRoutesFeed.
 *
 * Subscribes to a feed of immutable, append-only updates for routes. The initial request to this feed
 * endpoint returns a cursor, which can be used on the next request to fetch updated routes that have
 * had state changes since that request.
 *
 * The legacy version of this endpoint can be found at
 * [samsara.com/api-legacy](https://www.samsara.com/api-legacy#operation/fetchAllRouteJobUpdates).
 *
 *
 * <b>Rate limit:</b> 5 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Routes** under the Driver Workflow category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetRoutesFeed extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string|null  $expand  Expands the specified value(s) in the response object. Expansion populates additional fields in an object, if supported. Unsupported fields are ignored. To expand multiple fields, input a comma-separated list.
     *
     * Valid value: `route`  Valid values: `route`
     */
    public function __construct(
        protected ?string $expand = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['expand' => $this->expand]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/routes/audit-logs/feed';
    }
}
