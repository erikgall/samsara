<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getTrailerStatsHistory.
 *
 * Returns trailer stats during the given time range for all trailers. This can be optionally filtered
 * by tags or specific trailer IDs.
 *
 *  <b>Rate limit:</b> 10 requests/sec (learn more about rate limits
 * <a href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Trailer Statistics** under the Trailers category when creating or editing an
 * API token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetTrailerStatsHistory extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $types  The stat types you want this endpoint to return information on.
     *
     * You may list **up to 3** types using comma-separated format. For example: `types=gps,reeferAmbientAirTemperatureMilliC,gpsOdometerMeters`.
     *
     * * `gps`: GPS data including lat/long, heading, speed, and a reverse geocode address.
     * * `gpsOdometerMeters`: Odometer reading provided by GPS calculations. You must provide a manual odometer reading before this value is updated. Manual odometer readings can be provided via the PATCH /fleet/trailers/{id} endpoint or through the [cloud dashboard](https://kb.samsara.com/hc/en-us/articles/115005273667-Editing-Odometer-Reading). Odometer readings wthat are manually set will update as GPS trip data is gathered.
     * * `reeferAmbientAirTemperatureMilliC`: The ambient air temperature reading of the reefer in millidegree Celsius.
     * * `reeferObdEngineSeconds`: The cumulative number of seconds the reefer has run according to onboard diagnostics. Only supported on reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone1`: The supply or discharge air temperature zone 1 in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone2`: The supply or discharge air temperature zone 2 in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone3`: The supply or discharge air temperature zone 3 in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferFuelPercent`: The fuel level of the reefer unit in percentage points (e.g. `99`, `50`, etc). Only supported on reefer solutions.
     * * `carrierReeferState`: The overall state of the reefer (`Off`, `On`). Only supported on multizone Carrier reefer solutions.
     * * `reeferStateZone1`: The state of the reefer in zone 1. For single zone reefers, this applies tot he single zone. Only supported on multizone reefer solutions.
     * * `reeferStateZone2`: The state of the reefer in zone 2. Only supported on multizone reefer solutions.
     * * `reeferStateZone3`: The state of the reefer in zone 3. Only supported on multizone reefer solutions.
     * * `reeferRunMode`: The operational mode of the reefer (`Start/Stop`, `Continuous`)
     * * `reeferAlarms`: Any alarms that are present on the reefer. Only supported on reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone1`: The return air temperature in zone 1 of the reefer in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone2`: The return air temperature in zone 2 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone3`: The return air temperature in zone 3 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone1`: The set point temperature in zone 1 of the reefer in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone2`: The set point temperature in zone 2 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone3`: The set point temperature in zone 3 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferDoorStateZone1`: The door status in zone 1 of the reefer. For single zone reefers, this applies to the single zone.
     * * `reeferDoorStateZone2`: The door status in zone 2 of the reefer. Only supported on multizone reefer solutions.
     * * `reeferDoorStateZone3`: The door status in zone 3 of the reefer. Only supported on multizone reefer solutions.
     * @param  string|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  string|null  $trailerIds  A filter on the data based on this comma-separated list of trailer IDs and externalIds. Example: `trailerIds=1234,5678,samsara.vin:1HGBH41JXMN109186`
     * @param  string|null  $decorations  Decorations add to the primary stats listed in the `types` parameter. For example, if you wish to know the trailer's location whenever the odometer updates, you may set `types=gpsOdometerMeters&decorations=gps`.
     *
     * You may list **up to 2** types using comma-separated format. If multiple stats are listed in the types parameter, the decorations will be added to each type. For example: `types=reeferStateZone1,reeferAmbientAirTemperatureMilliC,gpsOdometerMeters&decorations=gps` will list GPS decorations for each reeferStateZone1 reading, each reeferAmbientAirTemperatureMilliC reding, and gpsOdometerMeters reading.
     *
     * Note that decorations may significantly increase the response payload size.
     *
     * * `gps`: GPS data including lat/long, heading, speed, and a reverse geocode address.
     * * `gpsOdometerMeters`: Odometer reading provided by GPS calculations. You must provide a manual odometer reading before this value is updated. Manual odometer readings can be provided via the PATCH /fleet/trailers/{id} endpoint or through the [cloud dashboard](https://kb.samsara.com/hc/en-us/articles/115005273667-Editing-Odometer-Reading). Odometer readings wthat are manually set will update as GPS trip data is gathered.
     * * `reeferAmbientAirTemperatureMilliC`: The ambient air temperature reading of the reefer in millidegree Celsius.
     * * `reeferObdEngineSeconds`: The cumulative number of seconds the reefer has run according to onboard diagnostics. Only supported on reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone1`: The supply or discharge air temperature zone 1 in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone2`: The supply or discharge air temperature zone 2 in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone3`: The supply or discharge air temperature zone 3 in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferFuelPercent`: The fuel level of the reefer unit in percentage points (e.g. `99`, `50`, etc). Only supported on reefer solutions.
     * * `carrierReeferState`: The overall state of the reefer (`Off`, `On`). Only supported on multizone Carrier reefer solutions.
     * * `reeferStateZone1`: The state of the reefer in zone 1. For single zone reefers, this applies tot he single zone. Only supported on multizone reefer solutions.
     * * `reeferStateZone2`: The state of the reefer in zone 2. Only supported on multizone reefer solutions.
     * * `reeferStateZone3`: The state of the reefer in zone 3. Only supported on multizone reefer solutions.
     * * `reeferRunMode`: The operational mode of the reefer (`Start/Stop`, `Continuous`)
     * * `reeferAlarms`: Any alarms that are present on the reefer. Only supported on reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone1`: The return air temperature in zone 1 of the reefer in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone2`: The return air temperature in zone 2 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone3`: The return air temperature in zone 3 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone1`: The set point temperature in zone 1 of the reefer in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone2`: The set point temperature in zone 2 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone3`: The set point temperature in zone 3 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferDoorStateZone1`: The door status in zone 1 of the reefer. For single zone reefers, this applies to the single zone.
     * * `reeferDoorStateZone2`: The door status in zone 2 of the reefer. Only supported on multizone reefer solutions.
     * * `reeferDoorStateZone3`: The door status in zone 3 of the reefer. Only supported on multizone reefer solutions.
     */
    public function __construct(
        protected string $startTime,
        protected string $endTime,
        protected string $types,
        protected ?string $tagIds = null,
        protected ?string $parentTagIds = null,
        protected ?string $trailerIds = null,
        protected ?string $decorations = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'startTime'    => $this->startTime,
            'endTime'      => $this->endTime,
            'types'        => $this->types,
            'tagIds'       => $this->tagIds,
            'parentTagIds' => $this->parentTagIds,
            'trailerIds'   => $this->trailerIds,
            'decorations'  => $this->decorations,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/beta/fleet/trailers/stats/history';
    }
}
