<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Coaching\GetCoachingSessions;
use ErikGall\Samsara\Requests\Coaching\GetDriverCoachAssignment;
use ErikGall\Samsara\Requests\Coaching\PutDriverCoachAssignment;

class Coaching extends Resource
{
    /**
     * @param  array  $driverIds  Optional string of comma separated driver IDs. If driver ID is present, sessions for the specified driver(s) will be returned.
     * @param  array  $coachIds  Optional string of comma separated user IDs. If coach ID is present, sessions for the specified coach(s) will be returned for either assignedCoach or completedCoach. If both driverId(s) and coachId(s) are present, sessions with specified driver(s) and coach(es) will be returned.
     * @param  array  $sessionStatuses  Optional string of comma separated statuses. Valid values:  “upcoming”, “completed”, “deleted”.
     * @param  bool  $includeCoachableEvents  Optional boolean to control whether behaviors will include coachableEvents in the response. Defaults to false.
     * @param  string  $startTime  Required RFC 3339 timestamp that indicates when to begin receiving data. Value is compared against `updatedAtTime`
     * @param  string  $endTime  Optional RFC 3339 timestamp. If not provided then the endpoint behaves as an unending feed of changes. If endTime is set the same as startTime, the most recent data point before that time will be returned per asset. Value is compared against `updatedAtTime`
     * @param  bool  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     */
    public function getCoachingSessions(
        ?array $driverIds,
        ?array $coachIds,
        ?array $sessionStatuses,
        ?bool $includeCoachableEvents,
        string $startTime,
        ?string $endTime = null,
        ?bool $includeExternalIds = null
    ): Response {
        return $this->connector->send(
            new GetCoachingSessions(
                $driverIds,
                $coachIds,
                $sessionStatuses,
                $includeCoachableEvents,
                $startTime,
                $endTime,
                $includeExternalIds
            )
        );
    }

    /**
     * @param  array  $driverIds  Optional string of comma separated IDs of the drivers. This can be either a unique Samsara driver ID or an external ID for the driver.
     * @param  array  $coachIds  Optional string of comma separated IDs of the coaches.
     * @param  bool  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     */
    public function getDriverCoachAssignment(
        ?array $driverIds = null,
        ?array $coachIds = null,
        ?bool $includeExternalIds = null
    ): Response {
        return $this->connector->send(
            new GetDriverCoachAssignment($driverIds, $coachIds, $includeExternalIds)
        );
    }

    /**
     * @param  string  $driverId  Required string ID of the driver. This is a unique Samsara ID of a driver.
     * @param  string  $coachId  Optional string ID of the coach. This is a unique Samsara user ID. If not provided, existing coach assignment will be removed.
     */
    public function putDriverCoachAssignment(string $driverId, ?string $coachId = null, array $payload = []): Response
    {
        return $this->connector->send(new PutDriverCoachAssignment($driverId, $coachId, $payload));
    }
}
