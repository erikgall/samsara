<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\CarrierProposedAssignments\ListCarrierProposedAssignments;
use ErikGall\Samsara\Requests\CarrierProposedAssignments\CreateCarrierProposedAssignment;
use ErikGall\Samsara\Requests\CarrierProposedAssignments\DeleteCarrierProposedAssignment;

class CarrierProposedAssignments extends Resource
{
    /**
     * Create a new Carrier Proposed Assignment.
     *
     * @param  array  $payload  The data to create the assignment.
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new CreateCarrierProposedAssignment($payload));
    }

    /**
     * Delete a Carrier Proposed Assignment.
     *
     * @param  string  $id  ID of the assignment.
     * @return Response
     */
    public function delete(string $id): Response
    {
        return $this->connector->send(new DeleteCarrierProposedAssignment($id));
    }

    /**
     * Get Carrier Proposed Assignments.
     *
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  array|null  $driverIds  Filter by driver IDs.
     * @param  string|null  $activeTime  Show assignments active at this time (RFC 3339).
     * @return Response
     */
    public function get(
        ?int $limit = null,
        ?array $driverIds = null,
        ?string $activeTime = null
    ): Response {
        return $this->connector->send(
            new ListCarrierProposedAssignments($limit, $driverIds, $activeTime)
        );
    }
}
