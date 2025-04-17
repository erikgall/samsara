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
    public function createDriverVehicleAssignment(array $payload = []): Response
    {
        return $this->connector->send(new CreateDriverVehicleAssignment($payload));
    }

    public function deleteDriverVehicleAssignments(): Response
    {
        return $this->connector->send(new DeleteDriverVehicleAssignments);
    }

    /**
     * @param  string  $filterBy  Option to filter by drivers or vehicles.  Valid values: `drivers`, `vehicles`
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array  $driverIds  A filter on the data based on this comma-separated list of driver IDs and externalIds. Example: `driverIds=1234,5678,payroll:4841`
     * @param  array  $vehicleIds  ID of the vehicle. This can either be the Samsara-specified ID, or an external ID. External IDs are customer specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: "key:value". For example, "maintenanceId:250020".
     * @param  string  $driverTagIds  A filter on the data based on this comma-separated list of driver tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $vehicleTagIds  A filter on the data based on this comma-separated list of vehicle tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $assignmentType  Specifies which assignment type to filter by.  Valid values: `HOS`, `idCard`, `static`, `faceId`, `tachograph`, `safetyManual`, `RFID`, `trailer`, `external`, `qrCode`
     */
    public function getDriverVehicleAssignments(
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

    public function updateDriverVehicleAssignment(array $payload = []): Response
    {
        return $this->connector->send(new UpdateDriverVehicleAssignment($payload));
    }
}
