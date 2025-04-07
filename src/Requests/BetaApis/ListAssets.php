<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * listAssets.
 *
 * List all assets. Up to 300 assets will be returned per page.
 *
 *  <b>Rate limit:</b> 5 requests/sec
 * (learn more about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 * To use this endpoint, select **Read Assets** under the Assets category
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
class ListAssets extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string|null  $type  The operational context in which the asset interacts with the Samsara system. Examples: Vehicle (eg: truck, bus...), Trailer (eg: dry van, reefer, flatbed...), Powered Equipment (eg: dozer, crane...), Unpowered Equipment (eg: container, dumpster...), or Uncategorized.  Valid values: `uncategorized`, `trailer`, `equipment`, `unpowered`, `vehicle`
     * @param  string|null  $updatedAfterTime  A filter on data to have an updated at time after or equal to this specified time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  bool|null  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     * @param  bool|null  $includeTags  Optional boolean indicating whether to return tags on supported entities
     * @param  string|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array|null  $ids  A filter on the data based on this comma-separated list of asset IDs and External IDs.
     * @param  string|null  $attributeValueIds  A filter on the data based on this comma-separated list of attribute value IDs. Only entities associated with ALL of the referenced values will be returned (i.e. the intersection of the sets of entities with each value). Example: `attributeValueIds=076efac2-83b5-47aa-ba36-18428436dcac,6707b3f0-23b9-4fe3-b7be-11be34aea544`
     */
    public function __construct(
        protected ?string $type = null,
        protected ?string $updatedAfterTime = null,
        protected ?bool $includeExternalIds = null,
        protected ?bool $includeTags = null,
        protected ?string $tagIds = null,
        protected ?string $parentTagIds = null,
        protected ?array $ids = null,
        protected ?string $attributeValueIds = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'type'               => $this->type,
            'updatedAfterTime'   => $this->updatedAfterTime,
            'includeExternalIds' => $this->includeExternalIds,
            'includeTags'        => $this->includeTags,
            'tagIds'             => $this->tagIds,
            'parentTagIds'       => $this->parentTagIds,
            'ids'                => $this->ids,
            'attributeValueIds'  => $this->attributeValueIds,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/assets';
    }
}
