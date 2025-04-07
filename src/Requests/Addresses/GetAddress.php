<?php

namespace ErikGall\Samsara\Requests\Addresses;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getAddress.
 *
 * Returns a specific address.
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests should
 * be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support
 * team.
 *
 * To use this endpoint, select **Read Addresses** under the Addresses category when creating or
 * editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class GetAddress extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $id  ID of the Address. This can either be the Samsara-provided ID or an external ID. External IDs are customer-specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `crmId:abc123`
     */
    public function __construct(
        protected string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/addresses/{$this->id}";
    }
}
