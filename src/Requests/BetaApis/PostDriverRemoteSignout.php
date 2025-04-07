<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * postDriverRemoteSignout.
 *
 * Sign out a driver from the Samsara Driver App
 *
 * To access this endpoint, your organization must have
 * the Samsara Platform Premier license.
 *
 * Note: Sign out requests made while a logged-in driver does
 * not have internet connection will not log the driver out. A success response will still be provided
 * and the driver will be logged out once they have internet connection.
 *
 *  <b>Rate limit:</b> 100
 * requests/min (learn more about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 * To use this endpoint, select **Write Driver Remote Signout** under the
 * Closed Beta category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class PostDriverRemoteSignout extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct() {}

    public function resolveEndpoint(): string
    {
        return '/fleet/drivers/remote-sign-out';
    }
}
