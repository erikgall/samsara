<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * updateEngineImmobilizerState.
 *
 * Update the engine immobilizer state of a vehicle. This requires an engine immobilizer to be
 * installed on the vehicle gateway.
 *
 *  <b>Rate limit:</b> 100 requests/min (learn more about rate
 * limits <a href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use
 * this endpoint, select **Write Vehicle Immobilization** under the Vehicles category when creating or
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
class UpdateEngineImmobilizerState extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    /**
     * @param  int  $id  Vehicle ID
     */
    public function __construct(protected int $id, protected array $payload = []) {}

    public function resolveEndpoint(): string
    {
        return "/beta/fleet/vehicles/{$this->id}/immobilizer";
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
