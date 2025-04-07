<?php

namespace ErikGall\Samsara\Requests\Vehicles;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * listVehicles.
 *
 * Returns a list of all vehicles.
 *
 *  <b>Rate limit:</b> 25 requests/sec (learn more about rate limits
 * <a href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Vehicles** under the Vehicles category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class ListVehicles extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  string|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  string|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string|null  $attributeValueIds  A filter on the data based on this comma-separated list of attribute value IDs. Only entities associated with ALL of the referenced values will be returned (i.e. the intersection of the sets of entities with each value). Example: `attributeValueIds=076efac2-83b5-47aa-ba36-18428436dcac,6707b3f0-23b9-4fe3-b7be-11be34aea544`
     * @param  string|null  $updatedAfterTime  A filter on data to have an updated at time after or equal to this specified time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $createdAfterTime  A filter on data to have a created at time after or equal to this specified time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     */
    public function __construct(
        protected ?int $limit = null,
        protected ?string $parentTagIds = null,
        protected ?string $tagIds = null,
        protected ?string $attributeValueIds = null,
        protected ?string $updatedAfterTime = null,
        protected ?string $createdAfterTime = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'limit'             => $this->limit,
            'parentTagIds'      => $this->parentTagIds,
            'tagIds'            => $this->tagIds,
            'attributeValueIds' => $this->attributeValueIds,
            'updatedAfterTime'  => $this->updatedAfterTime,
            'createdAfterTime'  => $this->createdAfterTime,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/vehicles';
    }
}
