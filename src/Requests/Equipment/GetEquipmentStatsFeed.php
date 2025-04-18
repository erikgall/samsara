<?php

namespace ErikGall\Samsara\Requests\Equipment;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getEquipmentStatsFeed.
 *
 * Follow a continuous feed of all equipment stats from Samsara AG26s.
 *
 * Your first call to this
 * endpoint will provide you with the most recent stats for each unit of equipment and a `pagination`
 * object that contains an `endCursor`.
 *
 * You can provide the `endCursor` to subsequent calls via the
 * `after` parameter. The response will contain any equipment stats updates since that `endCursor`.
 *
 * If
 * `hasNextPage` is `false`, no updates are readily available yet. Each stat type has a different
 * refresh rate, but in general we'd suggest waiting a minimum of 5 seconds before requesting updates.
 *
 *
 *  <b>Rate limit:</b> 150 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 *  **Submit
 * Feedback**: Likes, dislikes, and API feature requests should be filed as feedback in our <a
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
class GetEquipmentStatsFeed extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array|null  $equipmentIds  A filter on the data based on this comma-separated list of equipment IDs. Example: `equipmentIds=1234,5678`
     * @param  array  $types  The types of equipment stats you want to query. Currently, you may submit up to 3 types.
     *
     * - `engineRpm`: The revolutions per minute of the engine.
     * - `fuelPercents`: The percent of fuel in the unit of equipment.
     * - `obdEngineSeconds`: The number of seconds the engine has been running since it was new. This value is provided directly from on-board diagnostics.
     * - `gatewayEngineSeconds`: An approximation of the number of seconds the engine has been running since it was new, based on the amount of time the asset gateway has been receiving power with an offset provided manually through the Samsara cloud dashboard. This is supported with the following hardware configurations:
     *   - AG24/AG26/AG46P + APWR cable ([Auxiliary engine configuration](https://kb.samsara.com/hc/en-us/articles/360043040512-Auxiliary-Inputs#UUID-d514abff-d10a-efaf-35d9-e10fa6c4888d) required)
     *   - AG52 + BPWR/BEQP cable ([Auxiliary engine configuration](https://kb.samsara.com/hc/en-us/articles/360043040512-Auxiliary-Inputs#UUID-d514abff-d10a-efaf-35d9-e10fa6c4888d) required).
     * - `gatewayJ1939EngineSeconds`: An approximation of the number of seconds the engine has been running since it was new, based on the amount of time the AG26 device is receiving power via J1939/CAT cable and an offset provided manually through the Samsara cloud dashboard.
     * - `obdEngineStates`: The state of the engine read from on-board diagnostics. Can be `Off`, `On`, or `Idle`.
     * - `gatewayEngineStates`: An approximation of engine state based on readings the AG26 receives from the aux/digio cable. Can be `Off` or `On`.
     * - `gpsOdometerMeters`: An approximation of odometer reading based on GPS calculations since the AG26 was activated, and a manual odometer offset provided in the Samsara cloud dashboard. Valid values: `Off`, `On`.
     * - `gps`: GPS data including lat/long, heading, speed, address book entry (if exists), and a reverse geocoded address.
     * - `engineTotalIdleTimeMinutes`: Total time in minutes that the engine has been idling.
     */
    public function __construct(
        protected ?array $parentTagIds,
        protected ?array $tagIds,
        protected ?array $equipmentIds,
        protected array $types
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'parentTagIds' => $this->parentTagIds,
            'tagIds'       => $this->tagIds,
            'equipmentIds' => $this->equipmentIds,
            'types'        => $this->types,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/equipment/stats/feed';
    }
}
