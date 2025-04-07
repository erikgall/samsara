<?php

namespace ErikGall\Samsara\Requests\Users;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * deleteUser.
 *
 * Delete the given user.
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests should be
 * filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback
 * form</a>. If you encountered an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Write Users** under the Setup & Administration category when creating or
 * editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class DeleteUser extends Request
{
    protected Method $method = Method::DELETE;

    /**
     * @param  string  $id  Unique identifier for the user.
     */
    public function __construct(protected string $id) {}

    public function resolveEndpoint(): string
    {
        return "/users/{$this->id}";
    }
}
