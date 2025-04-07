<?php

namespace ErikGall\Samsara\Requests\LiveSharingLinks;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getLiveSharingLinks.
 *
 * Returns all non-expired Live Sharing Links.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more about
 * rate limits <a href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To
 * use this endpoint, select **Read Live Sharing Links** under the Driver Workflow category when
 * creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetLiveSharingLinks extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array|null  $ids  A filter on the data based on this comma-separated list of Live Share Link IDs
     * @param  string|null  $type  A filter on the data based on the Live Sharing Link type.  Valid values: `all`, `assetsLocation`, `assetsNearLocation`, `assetsOnRoute`
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 100 objects.
     */
    public function __construct(
        protected ?array $ids = null,
        protected ?string $type = null,
        protected ?int $limit = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['ids' => $this->ids, 'type' => $this->type, 'limit' => $this->limit]);
    }

    public function resolveEndpoint(): string
    {
        return '/live-shares';
    }
}
