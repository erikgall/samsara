<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Idling\GetVehicleIdlingReports;

class Idling extends Resource
{
    /**
     * Get vehicle idling reports.
     *
     * @param  int|null  $limit  The limit for how many objects will be in the response.
     * @param  string  $startTime  Start time (RFC 3339).
     * @param  string  $endTime  End time (RFC 3339).
     * @param  string|null  $vehicleIds  Filter by vehicle IDs.
     * @param  string|null  $tagIds  Filter by tag IDs.
     * @param  string|null  $parentTagIds  Filter by parent tag IDs.
     * @param  bool|null  $isPtoActive  Filter by PTO active status.
     * @param  int|null  $minIdlingDurationMinutes  Filter by minimum idling duration.
     * @return Response
     */
    public function get(
        ?int $limit,
        string $startTime,
        string $endTime,
        ?string $vehicleIds = null,
        ?string $tagIds = null,
        ?string $parentTagIds = null,
        ?bool $isPtoActive = null,
        ?int $minIdlingDurationMinutes = null
    ): Response {
        return $this->connector->send(
            new GetVehicleIdlingReports(
                $limit,
                $startTime,
                $endTime,
                $vehicleIds,
                $tagIds,
                $parentTagIds,
                $isPtoActive,
                $minIdlingDurationMinutes
            )
        );
    }
}
