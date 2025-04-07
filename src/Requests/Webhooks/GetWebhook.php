<?php

namespace ErikGall\Samsara\Requests\Webhooks;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getWebhook.
 *
 * Retrieve a webhook with given ID.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more about rate limits
 * <a href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Webhooks** under the Setup & Administration category when creating or
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
class GetWebhook extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $id  ID of the webhook. This is the Samsara-specified ID.
     */
    public function __construct(protected string $id) {}

    public function resolveEndpoint(): string
    {
        return "/webhooks/{$this->id}";
    }
}
