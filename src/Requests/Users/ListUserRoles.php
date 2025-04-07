<?php

namespace ErikGall\Samsara\Requests\Users;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * listUserRoles.
 *
 * Returns a list of all user roles in an organization.
 *
 *  **Submit Feedback**: Likes, dislikes, and
 * API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read Users** under the Setup & Administration category when creating or
 * editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class ListUserRoles extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     */
    public function __construct(protected ?int $limit = null) {}

    public function defaultQuery(): array
    {
        return array_filter(['limit' => $this->limit]);
    }

    public function resolveEndpoint(): string
    {
        return '/user-roles';
    }
}
