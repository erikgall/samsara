<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\CarrierProposedAssignments\ListCarrierProposedAssignments;
use ErikGall\Samsara\Requests\CarrierProposedAssignments\CreateCarrierProposedAssignment;
use ErikGall\Samsara\Requests\CarrierProposedAssignments\DeleteCarrierProposedAssignment;

class CarrierProposedAssignments extends Resource
{
    public function createCarrierProposedAssignment(array $payload = []): Response
    {
        return $this->connector->send(new CreateCarrierProposedAssignment($payload));
    }

    /**
     * @param  string  $id  ID of the assignment.
     */
    public function deleteCarrierProposedAssignment(string $id): Response
    {
        return $this->connector->send(new DeleteCarrierProposedAssignment($id));
    }

    /**
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  array  $driverIds  A filter on the data based on this comma-separated list of driver IDs and externalIds. Example: `driverIds=1234,5678,payroll:4841`
     * @param  string  $activeTime  If specified, shows assignments that will be active at this time. Defaults to now, which would show current active assignments. In RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     */
    public function listCarrierProposedAssignments(
        ?int $limit = null,
        ?array $driverIds = null,
        ?string $activeTime = null
    ): Response {
        return $this->connector->send(
            new ListCarrierProposedAssignments($limit, $driverIds, $activeTime)
        );
    }
}
