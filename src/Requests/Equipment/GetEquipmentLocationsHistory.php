<?php

namespace ErikGall\Samsara\Requests\Equipment;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getEquipmentLocationsHistory.
 *
 * Returns historical equipment locations during the given time range. This can be optionally filtered
 * by tags or specific equipment IDs.
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support
 * team.
 *
 * To use this endpoint, select **Read Equipment Statistics** under the Equipment category when
 * creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class GetEquipmentLocationsHistory extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array|null  $equipmentIds  A filter on the data based on this comma-separated list of equipment IDs. Example: `equipmentIds=1234,5678`
     */
    public function __construct(
        protected string $startTime,
        protected string $endTime,
        protected ?array $parentTagIds = null,
        protected ?array $tagIds = null,
        protected ?array $equipmentIds = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'startTime'    => $this->startTime,
            'endTime'      => $this->endTime,
            'parentTagIds' => $this->parentTagIds,
            'tagIds'       => $this->tagIds,
            'equipmentIds' => $this->equipmentIds,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/equipment/locations/history';
    }
}
