<?php

namespace ErikGall\Samsara\Requests\Equipment;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getEquipmentLocationsFeed.
 *
 * Follow a continuous feed of all equipment locations from Samsara AG26s.
 *
 * Your first call to this
 * endpoint will provide you with the most recent location for each unit of equipment and a
 * `pagination` object that contains an `endCursor`.
 *
 * You can provide the `endCursor` to subsequent
 * calls via the `after` parameter. The response will contain any equipment location updates since that
 * `endCursor`.
 *
 * If `hasNextPage` is `false`, no updates are readily available yet. We'd suggest
 * waiting a minimum of 5 seconds before requesting updates.
 *
 *  **Submit Feedback**: Likes, dislikes,
 * and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read Equipment Statistics** under the Equipment category when creating or
 * editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class GetEquipmentLocationsFeed extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array|null  $equipmentIds  A filter on the data based on this comma-separated list of equipment IDs. Example: `equipmentIds=1234,5678`
     */
    public function __construct(
        protected ?array $parentTagIds = null,
        protected ?array $tagIds = null,
        protected ?array $equipmentIds = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'parentTagIds' => $this->parentTagIds,
            'tagIds'       => $this->tagIds,
            'equipmentIds' => $this->equipmentIds,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/equipment/locations/feed';
    }
}
