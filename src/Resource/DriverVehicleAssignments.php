<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\DriverVehicleAssignments\GetDriverVehicleAssignments;
use ErikGall\Samsara\Requests\DriverVehicleAssignments\CreateDriverVehicleAssignment;
use ErikGall\Samsara\Requests\DriverVehicleAssignments\UpdateDriverVehicleAssignment;
use ErikGall\Samsara\Requests\DriverVehicleAssignments\DeleteDriverVehicleAssignments;

class DriverVehicleAssignments extends Resource
{
    /**
     * Create a new Driver Vehicle Assignment resource.
     *
     * @param  array  $payload  The data to create the assignment.
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new CreateDriverVehicleAssignment($payload));
    }

    /**
     * Delete all Driver Vehicle Assignments.
     *
     * @return Response
     */
    public function delete(): Response
    {
        return $this->connector->send(new DeleteDriverVehicleAssignments);
    }

    /**
     * Get Driver Vehicle Assignments.
     *
     * @param  string  $filterBy  Option to filter by drivers or vehicles.
     * @param  string|null  $startTime  Start time (RFC 3339).
     * @param  string|null  $endTime  End time (RFC 3339).
     * @param  array|null  $driverIds  Filter by driver IDs.
     * @param  array|null  $vehicleIds  Filter by vehicle IDs.
     * @param  string|null  $driverTagIds  Filter by driver tag IDs.
     * @param  string|null  $vehicleTagIds  Filter by vehicle tag IDs.
     * @param  string|null  $assignmentType  Filter by assignment type.
     * @return Response
     */
    public function get(
        string $filterBy,
        ?string $startTime = null,
        ?string $endTime = null,
        ?array $driverIds = null,
        ?array $vehicleIds = null,
        ?string $driverTagIds = null,
        ?string $vehicleTagIds = null,
        ?string $assignmentType = null
    ): Response {
        return $this->connector->send(
            new GetDriverVehicleAssignments(
                $filterBy,
                $startTime,
                $endTime,
                $driverIds,
                $vehicleIds,
                $driverTagIds,
                $vehicleTagIds,
                $assignmentType
            )
        );
    }

    /**
     * Update a Driver Vehicle Assignment.
     *
     * @param  array  $payload  The data to update the assignment.
     * @return Response
     */
    public function update(array $payload = []): Response
    {
        return $this->connector->send(new UpdateDriverVehicleAssignment($payload));
    }
}
