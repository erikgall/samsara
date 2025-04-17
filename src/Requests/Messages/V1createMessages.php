<?php

namespace ErikGall\Samsara\Requests\Messages;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * V1createMessages.
 *
 * <n class="warning">
 * <nh>
 * <i class="fa fa-exclamation-circle"></i>
 * This endpoint is still on our
 * legacy API.
 * </nh>
 * </n>
 *
 * Send a message to a list of driver ids.
 *
 *  **Submit Feedback**: Likes,
 * dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Write Messages** under the Driver Workflow category when creating or editing
 * an API token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class V1createMessages extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected array $payload = []) {}

    public function resolveEndpoint(): string
    {
        return '/v1/fleet/messages';
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
