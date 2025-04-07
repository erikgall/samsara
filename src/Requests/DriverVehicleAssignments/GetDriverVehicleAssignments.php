<?php

namespace ErikGall\Samsara\Requests\DriverVehicleAssignments;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getDriverVehicleAssignments.
 *
 * Get all driver-vehicle assignments for the requested drivers or vehicles in the requested time
 * range. To fetch driver-vehicle assignments out of the vehicle trips' time ranges, assignmentType
 * needs to be specified. Note: this endpoint replaces past endpoints to fetch assignments by driver or
 * by vehicle. Visit [this migration
 * guide](https://developers.samsara.com/docs/migrating-from-driver-vehicle-assignment-or-vehicle-driver-assignment-endpoints)
 * for more information.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Assignments** under the Assignments category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetDriverVehicleAssignments extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $filterBy  Option to filter by drivers or vehicles.  Valid values: `drivers`, `vehicles`
     * @param  string|null  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array|null  $driverIds  A filter on the data based on this comma-separated list of driver IDs and externalIds. Example: `driverIds=1234,5678,payroll:4841`
     * @param  array|null  $vehicleIds  ID of the vehicle. This can either be the Samsara-specified ID, or an external ID. External IDs are customer specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: "key:value". For example, "maintenanceId:250020".
     * @param  string|null  $driverTagIds  A filter on the data based on this comma-separated list of driver tag IDs. Example: `tagIds=1234,5678`
     * @param  string|null  $vehicleTagIds  A filter on the data based on this comma-separated list of vehicle tag IDs. Example: `tagIds=1234,5678`
     * @param  string|null  $assignmentType  Specifies which assignment type to filter by.  Valid values: `HOS`, `idCard`, `static`, `faceId`, `tachograph`, `safetyManual`, `RFID`, `trailer`, `external`, `qrCode`
     */
    public function __construct(
        protected string $filterBy,
        protected ?string $startTime = null,
        protected ?string $endTime = null,
        protected ?array $driverIds = null,
        protected ?array $vehicleIds = null,
        protected ?string $driverTagIds = null,
        protected ?string $vehicleTagIds = null,
        protected ?string $assignmentType = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'filterBy'       => $this->filterBy,
            'startTime'      => $this->startTime,
            'endTime'        => $this->endTime,
            'driverIds'      => $this->driverIds,
            'vehicleIds'     => $this->vehicleIds,
            'driverTagIds'   => $this->driverTagIds,
            'vehicleTagIds'  => $this->vehicleTagIds,
            'assignmentType' => $this->assignmentType,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/driver-vehicle-assignments';
    }
}
