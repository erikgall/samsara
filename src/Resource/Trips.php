<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Trips\V1getFleetTrips;

class Trips extends Resource
{
    /**
     * @param  int  $vehicleId  Vehicle ID to query.
     * @param  int  $startMs  Beginning of the time range, specified in milliseconds UNIX time. Limited to a 90 day window with respect to startMs and endMs
     * @param  int  $endMs  End of the time range, specified in milliseconds UNIX time.
     */
    public function v1getFleetTrips(int $vehicleId, int $startMs, int $endMs): Response
    {
        return $this->connector->send(new V1getFleetTrips($vehicleId, $startMs, $endMs));
    }
}
