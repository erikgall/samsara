<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\LocationAndSpeed\GetLocationAndSpeed;

class LocationAndSpeed extends Resource
{
    /**
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to never if not provided; if not provided then pagination will not cease, and a valid pagination cursor will always be returned. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array  $ids  Comma-separated list of asset IDs.
     * @param  bool  $includeSpeed  Optional boolean indicating whether or not to return the 'speed' object
     * @param  bool  $includeReverseGeo  Optional boolean indicating whether or not to return the 'address' object
     * @param  bool  $includeGeofenceLookup  Optional boolean indicating whether or not to return the 'geofence' object
     * @param  bool  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     */
    public function getLocationAndSpeed(
        ?int $limit,
        ?string $startTime,
        ?string $endTime,
        ?array $ids,
        ?bool $includeSpeed,
        ?bool $includeReverseGeo,
        ?bool $includeGeofenceLookup,
        ?bool $includeExternalIds,
    ): Response {
        return $this->connector->send(new GetLocationAndSpeed($limit, $startTime, $endTime, $ids, $includeSpeed, $includeReverseGeo, $includeGeofenceLookup, $includeExternalIds));
    }
}
