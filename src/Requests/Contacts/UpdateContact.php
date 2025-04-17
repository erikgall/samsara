<?php

namespace ErikGall\Samsara\Requests\Contacts;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * updateContact.
 *
 * Update a specific contact's information.
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature
 * requests should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69"
 * target="_blank">API feedback form</a>. If you encountered an issue or noticed inaccuracies in the
 * API documentation, please <a href="https://www.samsara.com/help" target="_blank">submit a case</a>
 * to our support team.
 *
 * To use this endpoint, select **Write Alert Contacts** under the Setup &
 * Administration category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class UpdateContact extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    /**
     * @param  string  $id  Unique identifier for the contact.
     */
    public function __construct(protected string $id, protected array $payload = []) {}

    public function resolveEndpoint(): string
    {
        return "/contacts/{$this->id}";
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
