<?php

namespace ErikGall\Samsara\Requests\Maintenance;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * updateDvir.
 *
 * Resolves a given DVIR by marking its `isResolved` field to `true`.
 *
 *  **Submit Feedback**: Likes,
 * dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Write DVIRs** under the Maintenance category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class UpdateDvir extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    /**
     * @param  string  $id  ID of the DVIR.
     */
    public function __construct(protected string $id, protected array $payload = []) {}

    public function resolveEndpoint(): string
    {
        return "/fleet/dvirs/{$this->id}";
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
