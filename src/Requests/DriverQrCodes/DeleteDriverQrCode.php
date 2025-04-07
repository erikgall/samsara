<?php

namespace ErikGall\Samsara\Requests\DriverQrCodes;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * deleteDriverQrCode.
 *
 * Revoke requested driver's currently active QR code.
 *
 *  <b>Rate limit:</b> 100 requests/min (learn
 * more about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 * To use this endpoint, select **Write Drivers** under the Drivers
 * category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class DeleteDriverQrCode extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct() {}

    public function resolveEndpoint(): string
    {
        return '/drivers/qr-codes';
    }
}
