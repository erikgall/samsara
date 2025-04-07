<?php

namespace ErikGall\Samsara\Requests\LocationAndSpeed;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getLocationAndSpeed.
 *
 * This endpoint will return asset locations and speed data that has been collected for your
 * organization based on the time parameters passed in. Results are paginated. If you include an
 * endTime, the endpoint will return data up until that point. If you don’t include an endTime, you
 * can continue to poll the API real-time with the pagination cursor that gets returned on every call.
 * The endpoint will only return data up until the endTime that has been processed by the server at the
 * time of the original request. You will need to request the same [startTime, endTime) range again to
 * receive data for assets processed after the original request time. This endpoint sorts the
 * time-series data by device.
 *
 *  <b>Rate limit:</b> 10 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
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
class GetLocationAndSpeed extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  string|null  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $endTime  An end time in RFC 3339 format. Defaults to never if not provided; if not provided then pagination will not cease, and a valid pagination cursor will always be returned. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array|null  $ids  Comma-separated list of asset IDs.
     * @param  bool|null  $includeSpeed  Optional boolean indicating whether or not to return the 'speed' object
     * @param  bool|null  $includeReverseGeo  Optional boolean indicating whether or not to return the 'address' object
     * @param  bool|null  $includeGeofenceLookup  Optional boolean indicating whether or not to return the 'geofence' object
     * @param  bool|null  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     */
    public function __construct(
        protected ?int $limit = null,
        protected ?string $startTime = null,
        protected ?string $endTime = null,
        protected ?array $ids = null,
        protected ?bool $includeSpeed = null,
        protected ?bool $includeReverseGeo = null,
        protected ?bool $includeGeofenceLookup = null,
        protected ?bool $includeExternalIds = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'limit'                 => $this->limit,
            'startTime'             => $this->startTime,
            'endTime'               => $this->endTime,
            'ids'                   => $this->ids,
            'includeSpeed'          => $this->includeSpeed,
            'includeReverseGeo'     => $this->includeReverseGeo,
            'includeGeofenceLookup' => $this->includeGeofenceLookup,
            'includeExternalIds'    => $this->includeExternalIds,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/assets/location-and-speed/stream';
    }
}
