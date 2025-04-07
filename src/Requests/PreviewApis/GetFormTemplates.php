<?php

namespace ErikGall\Samsara\Requests\PreviewApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getFormTemplates.
 *
 * Returns a list of the organization's form templates.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more
 * about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 * To use this endpoint, select **Read Preview** under the  category when
 * creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 * Endpoints in this section are in Preview. These APIs are not
 * functional and are instead for soliciting feedback from our API users on the intended design of this
 * API. Additionally, it is not guaranteed that we will be releasing an endpoint included in this
 * section to production. This means that developers should **NOT** rely on these APIs to build
 * business critical applications
 *
 * - Samsara may change the structure of a preview API's interface
 * without versioning or any notice to API users.
 *
 * - When an endpoint becomes generally available, it
 * will be announced in the API [changelog](https://developers.samsara.com/changelog).
 *
 *
 *  **Submit
 * Feedback**: Likes, dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetFormTemplates extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array|null  $ids  A comma-separated list containing up to 100 template IDs to filter on.
     */
    public function __construct(
        protected ?array $ids = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['ids' => $this->ids]);
    }

    public function resolveEndpoint(): string
    {
        return '/preview/form-templates';
    }
}
