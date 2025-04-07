<?php

namespace ErikGall\Samsara\Requests\VehicleLocations;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getVehicleLocationsFeed.
 *
 * ***NOTE: The Vehicle Locations API is an older API that does not combine GPS data with onboard
 * diagnostics. Try our new [Vehicle Stats API](ref:getvehiclestatsfeed) instead.***
 *
 * Follow a
 * continuous feed of all vehicle locations from Samsara Vehicle Gateways.
 *
 * Your first call to this
 * endpoint will provide you with the most recent location for each vehicle and a `pagination` object
 * that contains an `endCursor`.
 *
 * You can provide the `endCursor` to the `after` parameter of this
 * endpoint to get location updates since that `endCursor`.
 *
 * If `hasNextPage` is `false`, no updates
 * are readily available yet. We'd suggest waiting a minimum of 5 seconds before requesting
 * updates.
 *
 * Related guide: <a href="/docs/vehicle-locations-1" target="_blank">Vehicle Locations</a>.
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests should be filed as feedback in our
 * <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you
 * encountered an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read Vehicle Statistics** under the Vehicle category when creating or
 * editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class GetVehicleLocationsFeed extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array|null  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs. Example: `vehicleIds=1234,5678`
     */
    public function __construct(
        protected ?array $parentTagIds = null,
        protected ?array $tagIds = null,
        protected ?array $vehicleIds = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['parentTagIds' => $this->parentTagIds, 'tagIds' => $this->tagIds, 'vehicleIds' => $this->vehicleIds]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/vehicles/locations/feed';
    }
}
