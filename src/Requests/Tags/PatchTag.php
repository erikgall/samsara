<?php

namespace ErikGall\Samsara\Requests\Tags;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * patchTag.
 *
 * Update an existing tag. **Note** this implementation of patch uses [the JSON merge
 * patch](https://tools.ietf.org/html/rfc7396) proposed standard.
 *
 *  This means that any fields
 * included in the patch request will _overwrite_ fields which exist on the target resource.
 *
 *  For
 * arrays, this means any array included in the request will _replace_ the array that exists at the
 * specified path, it will not _add_ to the existing array.
 *
 *  **Submit Feedback**: Likes, dislikes,
 * and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Write Tags** under the Setup & Administration category when creating or
 * editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class PatchTag extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    /**
     * @param  string  $id  ID of the Tag. This can either be the Samsara-provided ID or an external ID. External IDs are customer-specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `crmId:abc123`. Automatically populated external IDs are prefixed with `samsara.`. For example, `samsara.name:ELD-exempt`.
     */
    public function __construct(protected string $id) {}

    public function resolveEndpoint(): string
    {
        return "/tags/{$this->id}";
    }
}
