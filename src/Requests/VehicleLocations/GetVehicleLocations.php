<?php

namespace ErikGall\Samsara\Requests\VehicleLocations;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getVehicleLocations.
 *
 * ***NOTE: The Vehicle Locations API is an older API that does not combine GPS data with onboard
 * diagnostics. Try our new [Vehicle Stats API](ref:getvehiclestats) instead.***
 *
 * Returns the last
 * known location of all vehicles at the given `time`. If no `time` is specified, the current time is
 * used. This can be optionally filtered by tags or specific vehicle IDs.
 *
 * Related guide: <a
 * href="/docs/vehicle-locations-1" target="_blank">Vehicle Locations</a>.
 *
 *  **Submit Feedback**:
 * Likes, dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read Vehicle Statistics** under the Vehicles category when creating or
 * editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class GetVehicleLocations extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string|null  $time  A filter on the data that returns the last known data points with timestamps less than or equal to this value. Defaults to now if not provided. Must be a string in RFC 3339 format. Millisecond precision and timezones are supported. (Example: `2020-01-27T07:06:25Z`).
     * @param  array|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array|null  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs. Example: `vehicleIds=1234,5678`
     */
    public function __construct(
        protected ?string $time = null,
        protected ?array $parentTagIds = null,
        protected ?array $tagIds = null,
        protected ?array $vehicleIds = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'time'         => $this->time,
            'parentTagIds' => $this->parentTagIds,
            'tagIds'       => $this->tagIds,
            'vehicleIds'   => $this->vehicleIds,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/vehicles/locations';
    }
}
