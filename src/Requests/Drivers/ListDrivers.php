<?php

namespace ErikGall\Samsara\Requests\Drivers;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * listDrivers.
 *
 * Get all drivers in organization.
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support
 * team.
 *
 * To use this endpoint, select **Read Drivers** under the Drivers category when creating or
 * editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class ListDrivers extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string|null  $driverActivationStatus  If value is `deactivated`, only drivers that are deactivated will appear in the response. This parameter will default to `active` if not provided (fetching only active drivers).
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  array|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array|null  $attributeValueIds  A filter on the data based on this comma-separated list of attribute value IDs. Only entities associated with ALL of the referenced values will be returned (i.e. the intersection of the sets of entities with each value). Example: `attributeValueIds=076efac2-83b5-47aa-ba36-18428436dcac,6707b3f0-23b9-4fe3-b7be-11be34aea544`
     * @param  string|null  $updatedAfterTime  A filter on data to have an updated at time after or equal to this specified time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $createdAfterTime  A filter on data to have a created at time after or equal to this specified time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     */
    public function __construct(
        protected ?string $driverActivationStatus = null,
        protected ?int $limit = null,
        protected ?array $parentTagIds = null,
        protected ?array $tagIds = null,
        protected ?array $attributeValueIds = null,
        protected ?string $updatedAfterTime = null,
        protected ?string $createdAfterTime = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'driverActivationStatus' => $this->driverActivationStatus,
            'limit'                  => $this->limit,
            'parentTagIds'           => $this->parentTagIds,
            'tagIds'                 => $this->tagIds,
            'attributeValueIds'      => $this->attributeValueIds,
            'updatedAfterTime'       => $this->updatedAfterTime,
            'createdAfterTime'       => $this->createdAfterTime,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/drivers';
    }
}
