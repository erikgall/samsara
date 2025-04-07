<?php

namespace ErikGall\Samsara\Requests\Equipment;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * listEquipment.
 *
 * Returns a list of all equipment in an organization. Equipment objects represent powered assets
 * connected to a [Samsara AG26](https://www.samsara.com/products/models/ag26) via an APWR, CAT, or
 * J1939 cable. They are automatically created with a unique Samsara Equipment ID whenever an AG26 is
 * activated in your organization.
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support
 * team.
 *
 * To use this endpoint, select **Read Equipment** under the Equipment category when creating or
 * editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class ListEquipment extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  array|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     */
    public function __construct(
        protected ?int $limit = null,
        protected ?array $parentTagIds = null,
        protected ?array $tagIds = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'limit'        => $this->limit,
            'parentTagIds' => $this->parentTagIds,
            'tagIds'       => $this->tagIds,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/equipment';
    }
}
