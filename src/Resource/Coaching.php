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
     * Get driver coach assignments.
     *
     * @param  array|null  $driverIds  Filter by driver IDs.
     * @param  array|null  $coachIds  Filter by coach IDs.
     * @param  bool|null  $includeExternalIds  Include external IDs.
     * @return Response
     */
    public function getAssignments(
        ?array $driverIds = null,
        ?array $coachIds = null,
        ?bool $includeExternalIds = null
    ): Response {
        return $this->connector->send(
            new GetDriverCoachAssignment($driverIds, $coachIds, $includeExternalIds)
        );
    }

    /**
     * Get coaching sessions.
     *
     * @param  array|null  $driverIds  Filter by driver IDs.
     * @param  array|null  $coachIds  Filter by coach IDs.
     * @param  array|null  $sessionStatuses  Filter by session statuses.
     * @param  bool|null  $includeCoachableEvents  Include coachable events.
     * @param  string  $startTime  Start time (RFC 3339).
     * @param  string|null  $endTime  End time (RFC 3339).
     * @param  bool|null  $includeExternalIds  Include external IDs.
     * @return Response
     */
    public function getSessions(
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
     * Update a driver coach assignment.
     *
     * @param  string  $driverId  Driver ID.
     * @param  string|null  $coachId  Coach ID.
     * @param  array  $payload  Data to update the assignment.
     * @return Response
     */
    public function updateAssignment(string $driverId, ?string $coachId = null, array $payload = []): Response
    {
        return $this->connector->send(new PutDriverCoachAssignment($driverId, $coachId, $payload));
    }
}
