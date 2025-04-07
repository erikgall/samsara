<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * updateShippingDocs.
 *
 * Update the shippingDocs field of an existing assignment.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn
 * more about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 * To use this endpoint, select **Write ELD Hours of Service (US)** under
 * the Compliance category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class UpdateShippingDocs extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    /**
     * @param  string  $hosDate  A start date in yyyy-mm-dd format. Required.
     * @param  string  $driverId  ID of the driver for whom the duty status is being set.
     */
    public function __construct(
        protected string $hosDate,
        protected string $driverId,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['hosDate' => $this->hosDate, 'driverID' => $this->driverId]);
    }

    public function resolveEndpoint(): string
    {
        return '/hos/daily-logs/log-meta-data';
    }
}
