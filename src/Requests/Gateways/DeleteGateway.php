<?php

namespace ErikGall\Samsara\Requests\Gateways;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * deleteGateway.
 *
 * Deactivate a gateway
 *
 *  <b>Rate limit:</b> 100 requests/min (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Write Gateways** under the Setup & Administration category when creating or
 * editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class DeleteGateway extends Request
{
    protected Method $method = Method::DELETE;

    /**
     * @param  string  $id  Gateway serial number
     */
    public function __construct(
        protected string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/gateways/{$this->id}";
    }
}
