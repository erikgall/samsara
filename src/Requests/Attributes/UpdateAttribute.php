<?php

namespace ErikGall\Samsara\Requests\Attributes;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * updateAttribute.
 *
 * Updates an attribute in the organization.
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature
 * requests should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69"
 * target="_blank">API feedback form</a>. If you encountered an issue or noticed inaccuracies in the
 * API documentation, please <a href="https://www.samsara.com/help" target="_blank">submit a case</a>
 * to our support team.
 *
 * To use this endpoint, select **Write Attributes** under the Setup &
 * Administration category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class UpdateAttribute extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    /**
     * @param  string  $id  Samsara-provided UUID of the attribute.
     */
    public function __construct(protected string $id) {}

    public function resolveEndpoint(): string
    {
        return "/attributes/{$this->id}";
    }
}
