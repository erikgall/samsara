<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getAempEquipmentList.
 *
 * Get a list of equipment following the AEMP ISO 15143-3 standard.
 *
 *  <b>Rate limit:</b> 5 requests/sec
 * (learn more about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 * To use this endpoint, select **Read AEMP** under the Equipment category
 * when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetAempEquipmentList extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $pageNumber  The number corresponding to a specific page of paginated results, defaulting to the first page if not provided. The default page size is 100 records.
     */
    public function __construct(
        protected string $pageNumber,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/beta/aemp/Fleet/{$this->pageNumber}";
    }
}
