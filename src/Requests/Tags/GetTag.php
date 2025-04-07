<?php

namespace ErikGall\Samsara\Requests\Tags;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getTag.
 *
 * Fetch a tag by id.
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests should be filed
 * as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback
 * form</a>. If you encountered an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read Tags** under the Setup & Administration category when creating or
 * editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class GetTag extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $id  ID of the Tag. This can either be the Samsara-provided ID or an external ID. External IDs are customer-specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `crmId:abc123`. Automatically populated external IDs are prefixed with `samsara.`. For example, `samsara.name:ELD-exempt`.
     */
    public function __construct(
        protected string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/tags/{$this->id}";
    }
}
