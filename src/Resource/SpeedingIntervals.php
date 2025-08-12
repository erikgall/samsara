<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\SpeedingIntervals\GetSpeedingIntervals;

class SpeedingIntervals extends Resource
{
    /**
     * @param  array  $assetIds  Comma-separated list of asset IDs. Include up to 50 asset IDs.
     * @param  string  $startTime  RFC 3339 timestamp that indicates when to begin receiving data. Value is compared against `updatedAtTime` or `tripStartTime` depending on the queryBy parameter.
     * @param  string  $endTime  RFC 3339 timestamp which is compared against `updatedAtTime` or `tripStartTime` depending on the queryBy parameter. If not provided then the endpoint behaves as an unending feed of changes.
     * @param  string  $queryBy  Decide which timestamp the `startTime` and `endTime` are compared to.  Valid values: `updatedAtTime`, `tripStartTime`
     * @param  bool  $includeAsset  Indicates whether or not to return expanded “asset” data
     * @param  bool  $includeDriverId  Indicates whether or not to return trip's driver id
     * @param  array  $severityLevels  Optional string of comma-separated severity levels to filter speeding intervals by. Valid values:  “light”, ”moderate”, ”heavy”, “severe”.
     */
    public function get(
        array $assetIds,
        string $startTime,
        ?string $endTime = null,
        ?string $queryBy = null,
        ?bool $includeAsset = null,
        ?bool $includeDriverId = null,
        ?array $severityLevels = null
    ): Response {
        return $this->connector->send(
            new GetSpeedingIntervals(
                $assetIds,
                $startTime,
                $endTime,
                $queryBy,
                $includeAsset,
                $includeDriverId,
                $severityLevels
            )
        );
    }
}
