<?php

namespace ErikGall\Samsara\Requests\Maintenance;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getDefectTypes.
 *
 * Get DVIR defect types.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Defect Types** under the Maintenance category when creating or editing an
 * API token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetDefectTypes extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  array|null  $ids  A filter on the data based on this comma-separated list of defect type IDs.
     */
    public function __construct(
        protected ?int $limit = null,
        protected ?array $ids = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['limit' => $this->limit, 'ids' => $this->ids]);
    }

    public function resolveEndpoint(): string
    {
        return '/defect-types';
    }
}
