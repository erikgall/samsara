<?php

namespace ErikGall\Samsara\Requests\LiveSharingLinks;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * createLiveSharingLink.
 *
 * Create Live Sharing Link.
 *
 *  <b>Rate limit:</b> 100 requests/min (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Write Live Sharing Links** under the Driver Workflow category when creating or
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
class CreateLiveSharingLink extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected array $payload = []) {}

    public function resolveEndpoint(): string
    {
        return '/live-shares';
    }

    /**
     * Default body.
     *
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->payload;
    }
}
