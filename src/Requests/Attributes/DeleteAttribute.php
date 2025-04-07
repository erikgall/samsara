<?php

namespace ErikGall\Samsara\Requests\Attributes;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * deleteAttribute.
 *
 * Delete an attribute by id, including all of its applications.
 *
 *  **Submit Feedback**: Likes,
 * dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Write Attributes** under the Setup & Administration category when creating
 * or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class DeleteAttribute extends Request
{
    protected Method $method = Method::DELETE;

    /**
     * @param  string  $id  Samsara-provided UUID of the attribute.
     * @param  string  $entityType  Denotes the type of entity, driver or asset.
     */
    public function __construct(protected string $id, protected string $entityType) {}

    public function defaultQuery(): array
    {
        return array_filter(['entityType' => $this->entityType]);
    }

    public function resolveEndpoint(): string
    {
        return "/attributes/{$this->id}";
    }
}
