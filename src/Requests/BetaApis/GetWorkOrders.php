<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getWorkOrders.
 *
 * Gets work orders.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Work Orders** under the Closed Beta category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetWorkOrders extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array|null  $ids  Filter by the IDs. Up to 100 ids. Returns all if no ids are provided.
     */
    public function __construct(protected ?array $ids = null) {}

    public function defaultQuery(): array
    {
        return array_filter(['ids' => $this->ids]);
    }

    public function resolveEndpoint(): string
    {
        return '/maintenance/work-orders';
    }
}
