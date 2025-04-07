<?php

namespace ErikGall\Samsara\Requests\HoursOfService;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getHosClocks.
 *
 * Get the current HOS status for all drivers. Note that this includes inactive as well as active
 * drivers. The legacy version of this endpoint can be found at
 * [samsara.com/api-legacy](https://www.samsara.com/api-legacy#operation/getFleetHosLogsSummary).
 *
 *
 * **Submit Feedback**: Likes, dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read ELD Compliance Settings (US)** under the Compliance category when
 * creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class GetHosClocks extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array|null  $driverIds  A filter on the data based on this comma-separated list of driver IDs. Example: `driverIds=1234,5678`
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     */
    public function __construct(
        protected ?array $tagIds = null,
        protected ?array $parentTagIds = null,
        protected ?array $driverIds = null,
        protected ?int $limit = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'tagIds'       => $this->tagIds,
            'parentTagIds' => $this->parentTagIds,
            'driverIds'    => $this->driverIds,
            'limit'        => $this->limit,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/hos/clocks';
    }
}
