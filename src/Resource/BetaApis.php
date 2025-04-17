<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\BetaApis\GetJobs;
use ErikGall\Samsara\Requests\BetaApis\GetTrips;
use ErikGall\Samsara\Requests\BetaApis\PatchJob;
use ErikGall\Samsara\Requests\BetaApis\CreateJob;
use ErikGall\Samsara\Requests\BetaApis\DeleteJob;
use ErikGall\Samsara\Requests\BetaApis\GetIssues;
use ErikGall\Samsara\Requests\BetaApis\GetDevices;
use ErikGall\Samsara\Requests\BetaApis\ListAssets;
use ErikGall\Samsara\Requests\BetaApis\PatchIssue;
use ErikGall\Samsara\Requests\BetaApis\CreateAsset;
use ErikGall\Samsara\Requests\BetaApis\DeleteAsset;
use ErikGall\Samsara\Requests\BetaApis\UpdateAsset;
use ErikGall\Samsara\Requests\BetaApis\GetWorkOrders;
use ErikGall\Samsara\Requests\BetaApis\PatchEquipment;
use ErikGall\Samsara\Requests\BetaApis\PostWorkOrders;
use ErikGall\Samsara\Requests\BetaApis\GetAssetsInputs;
use ErikGall\Samsara\Requests\BetaApis\GetHosEldEvents;
use ErikGall\Samsara\Requests\BetaApis\GetIssuesStream;
use ErikGall\Samsara\Requests\BetaApis\GetServiceTasks;
use ErikGall\Samsara\Requests\BetaApis\PatchWorkOrders;
use ErikGall\Samsara\Requests\BetaApis\DeleteWorkOrders;
use ErikGall\Samsara\Requests\BetaApis\StreamWorkOrders;
use ErikGall\Samsara\Requests\BetaApis\ListUploadedMedia;
use ErikGall\Samsara\Requests\BetaApis\GetFormSubmissions;
use ErikGall\Samsara\Requests\BetaApis\GetTrainingCourses;
use ErikGall\Samsara\Requests\BetaApis\PostFormSubmission;
use ErikGall\Samsara\Requests\BetaApis\UpdateShippingDocs;
use ErikGall\Samsara\Requests\BetaApis\GetDriverEfficiency;
use ErikGall\Samsara\Requests\BetaApis\GetTrailerStatsFeed;
use ErikGall\Samsara\Requests\BetaApis\PatchFormSubmission;
use ErikGall\Samsara\Requests\BetaApis\GetAempEquipmentList;
use ErikGall\Samsara\Requests\BetaApis\GetTrailerStatsHistory;
use ErikGall\Samsara\Requests\BetaApis\GetTrailerStatsSnapshot;
use ErikGall\Samsara\Requests\BetaApis\PostDriverRemoteSignout;
use ErikGall\Samsara\Requests\BetaApis\PostTrainingAssignments;
use ErikGall\Samsara\Requests\BetaApis\GetFormSubmissionsStream;
use ErikGall\Samsara\Requests\BetaApis\PatchTrainingAssignments;
use ErikGall\Samsara\Requests\BetaApis\DeleteTrainingAssignments;
use ErikGall\Samsara\Requests\BetaApis\GetEngineImmobilizerStates;
use ErikGall\Samsara\Requests\BetaApis\GetDriverTrailerAssignments;
use ErikGall\Samsara\Requests\BetaApis\GetFormSubmissionsPdfExports;
use ErikGall\Samsara\Requests\BetaApis\GetTrainingAssignmentsStream;
use ErikGall\Samsara\Requests\BetaApis\UpdateEngineImmobilizerState;
use ErikGall\Samsara\Requests\BetaApis\CreateDriverTrailerAssignment;
use ErikGall\Samsara\Requests\BetaApis\PostFormSubmissionsPdfExports;
use ErikGall\Samsara\Requests\BetaApis\UpdateDriverTrailerAssignment;

class BetaApis extends Resource
{
    public function createAsset(array $payload = []): Response
    {
        return $this->connector->send(new CreateAsset($payload));
    }

    public function createDriverTrailerAssignment(array $payload = []): Response
    {
        return $this->connector->send(new CreateDriverTrailerAssignment($payload));
    }

    public function createJob(array $payload = []): Response
    {
        return $this->connector->send(new CreateJob($payload));
    }

    /**
     * @param  string  $id  A filter selecting a single asset by id.
     */
    public function deleteAsset(string $id): Response
    {
        return $this->connector->send(new DeleteAsset($id));
    }

    /**
     * @param  string  $id  A jobId or uuid in STRING format. JobId must be prefixed with `jobId:`(Examples: `"8d218e6c-7a16-4f9f-90f7-cc1d93b9e596"`, `"jobId:98765"`).
     */
    public function deleteJob(string $id): Response
    {
        return $this->connector->send(new DeleteJob($id));
    }

    /**
     * @param  array  $ids  String of comma separated assignments IDs. Max value for this value is 100 objects .Example: `ids=a4db8702-79d5-4396-a717-e301d52ecc11,c6490f6a-d84e-49b5-b0ad-b6baae304075`
     */
    public function deleteTrainingAssignments(array $ids): Response
    {
        return $this->connector->send(new DeleteTrainingAssignments($ids));
    }

    /**
     * @param  string  $id  The unique id of the work order.
     */
    public function deleteWorkOrders(string $id): Response
    {
        return $this->connector->send(new DeleteWorkOrders($id));
    }

    /**
     * @param  string  $pageNumber  The number corresponding to a specific page of paginated results, defaulting to the first page if not provided. The default page size is 100 records.
     */
    public function getAempEquipmentList(string $pageNumber): Response
    {
        return $this->connector->send(new GetAempEquipmentList($pageNumber));
    }

    /**
     * @param  array  $ids  Comma-separated list of asset IDs. Limited to 100 ID's for each request.
     * @param  string  $type  Input stat type to query for.  Valid values: `auxInput1`, `auxInput2`, `auxInput3`, `auxInput4`, `auxInput5`, `auxInput6`, `auxInput7`, `auxInput8`, `auxInput9`, `auxInput10`, `auxInput11`, `auxInput12`, `auxInput13`, `analogInput1Voltage`, `analogInput2Voltage`, `analogInput1Current`, `analogInput2Current`, `batteryVoltage`
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to never if not provided; if not provided then pagination will not cease, and a valid pagination cursor will always be returned. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  bool  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     * @param  bool  $includeTags  Optional boolean indicating whether to return tags on supported entities
     * @param  bool  $includeAttributes  Optional boolean indicating whether to return attributes on supported entities
     */
    public function getAssetsInputs(
        array $ids,
        string $type,
        string $startTime,
        ?string $endTime = null,
        ?bool $includeExternalIds = null,
        ?bool $includeTags = null,
        ?bool $includeAttributes = null
    ): Response {
        return $this->connector->send(
            new GetAssetsInputs(
                $ids,
                $type,
                $startTime,
                $endTime,
                $includeExternalIds,
                $includeTags,
                $includeAttributes
            )
        );
    }

    /**
     * @param  array  $models  Optional string of comma separated device models. Valid values: `CM31`, `CM32`, `CM33`, `CM34`, `VG34`, `VG34M`, `VG34EU`, `VG34FN`, `VG54NA`, `VG54EU`, `VG55NA`, `VG55EU`, `AG24`, `AG24EU`, `AG26`, `AG26EU`, `AG45`, `AG45EU`, `AG46`, `AG46EU`, `AG46P`, `AG46PEU`, `AG51`, `AG51EU`, `AG52`, `AG52EU`, `AG53`, `AG53EU`
     * @param  array  $healthStatuses  Optional string of comma separated device health statuses. Valid values: `healthy`, `needsAttention`, `needsReplacement`, `dataPending`.
     * @param  bool  $includeHealth  Optional boolean to control whether device health information is returned in the response. Defaults to false.
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 100 objects.
     */
    public function getDevices(
        ?array $models = null,
        ?array $healthStatuses = null,
        ?bool $includeHealth = null,
        ?int $limit = null
    ): Response {
        return $this->connector->send(
            new GetDevices($models, $healthStatuses, $includeHealth, $limit)
        );
    }

    /**
     * @param  string  $driverActivationStatus  If value is `deactivated`, only drivers that are deactivated will appear in the response. This parameter will default to `active` if not provided (fetching only active drivers).
     * @param  array  $driverIds  A filter on the data based on this comma-separated list of driver IDs. Cannot be used with tag filtering or driver status. Example: `driverIds=1234,5678`
     * @param  array  $driverTagIds  Filters summary to drivers based on this comma-separated list of tag IDs. Data from all the drivers' respective vehicles will be included in the summary, regardless of which tag the vehicle is associated with. Should not be provided in addition to `driverIds`. Example: driverTagIds=1234,5678
     * @param  array  $driverParentTagIds  Filters like `driverTagIds` but includes descendants of all the given parent tags. Should not be provided in addition to `driverIds`. Example: `driverParentTagIds=1234,5678`
     * @param  string  $startTime  A start time in RFC 3339 format. The results will be truncated to the hour mark for the provided time. For example, if `startTime` is 2020-03-17T12:06:19Z then the results will include data starting from 2020-03-17T12:00:00Z. The provided start time cannot be in the future. Start time can be at most 31 days before the end time. If the start time is within the last hour, the results will be empty. Default: 24 hours prior to endTime.
     *
     * Note that the most recent 72 hours of data may still be processing and is subject to change and latency, so it is not recommended to request data for the most recent 72 hours.
     * @param  string  $endTime  An end time in RFC 3339 format. The results will be truncated to the hour mark for the provided time. For example, if `endTime` is 2020-03-17T12:06:19Z then the results will include data up until 2020-03-17T12:00:00Z. The provided end time cannot be in the future. End time can be at most 31 days after the start time. Default: The current time truncated to the hour mark.
     *
     * Note that the most recent 72 hours of data may still be processing and is subject to change and latency, so it is not recommended to request data for the most recent 72 hours
     */
    public function getDriverEfficiency(
        ?string $driverActivationStatus = null,
        ?array $driverIds = null,
        ?array $driverTagIds = null,
        ?array $driverParentTagIds = null,
        ?string $startTime = null,
        ?string $endTime = null
    ): Response {
        return $this->connector->send(
            new GetDriverEfficiency(
                $driverActivationStatus,
                $driverIds,
                $driverTagIds,
                $driverParentTagIds,
                $startTime,
                $endTime
            )
        );
    }

    /**
     * @param  array  $driverIds  A filter on the data based on this comma-separated list of driver IDs and externalIds. Example: `driverIds=1234,5678,payroll:4841`
     * @param  bool  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     */
    public function getDriverTrailerAssignments(
        array $driverIds,
        ?bool $includeExternalIds = null
    ): Response {
        return $this->connector->send(
            new GetDriverTrailerAssignments($driverIds, $includeExternalIds)
        );
    }

    /**
     * @param  string  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs and externalIds. Example: `vehicleIds=1234,5678,samsara.vin:1HGBH41JXMN109186`
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     */
    public function getEngineImmobilizerStates(
        string $vehicleIds,
        string $startTime,
        ?string $endTime = null
    ): Response {
        return $this->connector->send(
            new GetEngineImmobilizerStates($vehicleIds, $startTime, $endTime)
        );
    }

    /**
     * @param  array  $ids  A comma-separated list containing up to 100 form submission IDs to filter on. Can be either a unique Samsara ID or an [external ID](https://developers.samsara.com/docs/external-ids) for the form submission.
     * @param  array  $include  A comma-separated list of strings indicating whether to return additional information. Valid values: `externalIds`, `fieldLabels`
     */
    public function getFormSubmissions(array $ids, ?array $include = null): Response
    {
        return $this->connector->send(new GetFormSubmissions($ids, $include));
    }

    /**
     * @param  string  $pdfId  ID of the form submission PDF export.
     */
    public function getFormSubmissionsPdfExports(string $pdfId): Response
    {
        return $this->connector->send(new GetFormSubmissionsPdfExports($pdfId));
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. Value is compared against `updatedAtTime`. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. Value is compared against `updatedAtTime`. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array  $formTemplateIds  A comma-separated list containing up to 50 template IDs to filter data to.
     * @param  array  $userIds  A comma-separated list containing up to 50 user IDs to filter data to.
     * @param  array  $driverIds  A comma-separated list containing up to 50 user IDs to filter data to.
     * @param  array  $include  A comma-separated list of strings indicating whether to return additional information. Valid values: `externalIds`, `fieldLabels`
     * @param  array  $assignedToRouteStopIds  A comma-separated list containing up to 50 route stop IDs to filter data to.
     */
    public function getFormSubmissionsStream(
        string $startTime,
        ?string $endTime = null,
        ?array $formTemplateIds = null,
        ?array $userIds = null,
        ?array $driverIds = null,
        ?array $include = null,
        ?array $assignedToRouteStopIds = null
    ): Response {
        return $this->connector->send(
            new GetFormSubmissionsStream(
                $startTime,
                $endTime,
                $formTemplateIds,
                $userIds,
                $driverIds,
                $include,
                $assignedToRouteStopIds
            )
        );
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array  $driverIds  A filter on the data based on this comma-separated list of driver IDs and externalIds. Example: `driverIds=1234,5678,payroll:4841`
     * @param  string  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  string  $driverActivationStatus  If value is `deactivated`, only drivers that are deactivated will appear in the response. This parameter will default to `active` if not provided (fetching only active drivers).  Valid values: `active`, `deactivated`
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 25 objects.
     */
    public function getHosEldEvents(
        string $startTime,
        string $endTime,
        ?array $driverIds = null,
        ?string $tagIds = null,
        ?string $parentTagIds = null,
        ?string $driverActivationStatus = null,
        ?int $limit = null
    ): Response {
        return $this->connector->send(
            new GetHosEldEvents(
                $startTime,
                $endTime,
                $driverIds,
                $tagIds,
                $parentTagIds,
                $driverActivationStatus,
                $limit
            )
        );
    }

    /**
     * @param  array  $ids  A comma-separated list containing up to 100 issue IDs to filter on. Can be either a unique Samsara ID or an [external ID](https://developers.samsara.com/docs/external-ids) for the issue.
     * @param  array  $include  A comma separated list of additional fields to include on requested objects. Valid values: `externalIds`
     */
    public function getIssues(array $ids, ?array $include = null): Response
    {
        return $this->connector->send(new GetIssues($ids, $include));
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. Value is compared against `updatedAtTime`. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. Value is compared against `updatedAtTime`. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array  $status  A comma-separated list containing status values to filter issues on. Valid values: `open`, `inProgress`, `resolved`, `dismissed`
     * @param  array  $assetIds  A comma-separated list containing up to 50 asset IDs to filter issues on. Issues with untracked assets can also be included by passing the value: 'untracked'.
     * @param  array  $include  A comma separated list of additional fields to include on requested objects. Valid values: `externalIds`
     */
    public function getIssuesStream(
        string $startTime,
        ?string $endTime = null,
        ?array $status = null,
        ?array $assetIds = null,
        ?array $include = null
    ): Response {
        return $this->connector->send(
            new GetIssuesStream($startTime, $endTime, $status, $assetIds, $include)
        );
    }

    /**
     * @param  string  $id  A jobId or uuid in STRING format. JobId must be prefixed with `jobId:`(Examples: `"8d218e6c-7a16-4f9f-90f7-cc1d93b9e596"`, `"jobId:98765"`).
     * @param  string  $startDate  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endDate  An end time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array  $industrialAssetIds  IndustrialAssetId in STRING format. (Example: `"8d218e6c-7a16-4f9f-90f7-cc1d93b9e596"`).
     * @param  array  $fleetDeviceIds  FleetDeviceId in INTEGER format. (Example: `123456`).
     * @param  string  $status  A job status in STRING format. Job statuses can be one of three (ignores case): `"active", "scheduled", "completed"`  Valid values: `active`, `scheduled`, `completed`
     * @param  string  $customerName  Customer name to filter by
     */
    public function getJobs(
        ?string $id = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?array $industrialAssetIds = null,
        ?array $fleetDeviceIds = null,
        ?string $status = null,
        ?string $customerName = null
    ): Response {
        return $this->connector->send(
            new GetJobs(
                $id,
                $startDate,
                $endDate,
                $industrialAssetIds,
                $fleetDeviceIds,
                $status,
                $customerName
            )
        );
    }

    /**
     * @param  array  $ids  Filter by the IDs. If not provided, won't filter by id.
     * @param  bool  $includeArchived  Include archived service task definitions.
     */
    public function getServiceTasks(?array $ids = null, ?bool $includeArchived = null): Response
    {
        return $this->connector->send(new GetServiceTasks($ids, $includeArchived));
    }

    /**
     * @param  string  $types  The stat types you want this endpoint to return information on.
     *
     * You may list **up to 3** types using comma-separated format. For example: `types=gps,reeferAmbientAirTemperatureMilliC,gpsOdometerMeters`.
     *
     * * `gps`: GPS data including lat/long, heading, speed, and a reverse geocode address.
     * * `gpsOdometerMeters`: Odometer reading provided by GPS calculations. You must provide a manual odometer reading before this value is updated. Manual odometer readings can be provided via the PATCH /fleet/trailers/{id} endpoint or through the [cloud dashboard](https://kb.samsara.com/hc/en-us/articles/115005273667-Editing-Odometer-Reading). Odometer readings wthat are manually set will update as GPS trip data is gathered.
     * * `reeferAmbientAirTemperatureMilliC`: The ambient air temperature reading of the reefer in millidegree Celsius.
     * * `reeferObdEngineSeconds`: The cumulative number of seconds the reefer has run according to onboard diagnostics. Only supported on reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone1`: The supply or discharge air temperature zone 1 in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone2`: The supply or discharge air temperature zone 2 in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone3`: The supply or discharge air temperature zone 3 in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferFuelPercent`: The fuel level of the reefer unit in percentage points (e.g. `99`, `50`, etc). Only supported on reefer solutions.
     * * `carrierReeferState`: The overall state of the reefer (`Off`, `On`). Only supported on multizone Carrier reefer solutions.
     * * `reeferStateZone1`: The state of the reefer in zone 1. For single zone reefers, this applies tot he single zone. Only supported on multizone reefer solutions.
     * * `reeferStateZone2`: The state of the reefer in zone 2. Only supported on multizone reefer solutions.
     * * `reeferStateZone3`: The state of the reefer in zone 3. Only supported on multizone reefer solutions.
     * * `reeferRunMode`: The operational mode of the reefer (`Start/Stop`, `Continuous`)
     * * `reeferAlarms`: Any alarms that are present on the reefer. Only supported on reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone1`: The return air temperature in zone 1 of the reefer in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone2`: The return air temperature in zone 2 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone3`: The return air temperature in zone 3 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone1`: The set point temperature in zone 1 of the reefer in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone2`: The set point temperature in zone 2 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone3`: The set point temperature in zone 3 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferDoorStateZone1`: The door status in zone 1 of the reefer. For single zone reefers, this applies to the single zone.
     * * `reeferDoorStateZone2`: The door status in zone 2 of the reefer. Only supported on multizone reefer solutions.
     * * `reeferDoorStateZone3`: The door status in zone 3 of the reefer. Only supported on multizone reefer solutions.
     * @param  string  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  string  $trailerIds  A filter on the data based on this comma-separated list of trailer IDs and externalIds. Example: `trailerIds=1234,5678,samsara.vin:1HGBH41JXMN109186`
     * @param  string  $decorations  Decorations add to the primary stats listed in the `types` parameter. For example, if you wish to know the trailer's location whenever the odometer updates, you may set `types=gpsOdometerMeters&decorations=gps`.
     *
     * You may list **up to 2** types using comma-separated format. If multiple stats are listed in the types parameter, the decorations will be added to each type. For example: `types=reeferStateZone1,reeferAmbientAirTemperatureMilliC,gpsOdometerMeters&decorations=gps` will list GPS decorations for each reeferStateZone1 reading, each reeferAmbientAirTemperatureMilliC reding, and gpsOdometerMeters reading.
     *
     * Note that decorations may significantly increase the response payload size.
     *
     * * `gps`: GPS data including lat/long, heading, speed, and a reverse geocode address.
     * * `gpsOdometerMeters`: Odometer reading provided by GPS calculations. You must provide a manual odometer reading before this value is updated. Manual odometer readings can be provided via the PATCH /fleet/trailers/{id} endpoint or through the [cloud dashboard](https://kb.samsara.com/hc/en-us/articles/115005273667-Editing-Odometer-Reading). Odometer readings wthat are manually set will update as GPS trip data is gathered.
     * * `reeferAmbientAirTemperatureMilliC`: The ambient air temperature reading of the reefer in millidegree Celsius.
     * * `reeferObdEngineSeconds`: The cumulative number of seconds the reefer has run according to onboard diagnostics. Only supported on reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone1`: The supply or discharge air temperature zone 1 in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone2`: The supply or discharge air temperature zone 2 in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone3`: The supply or discharge air temperature zone 3 in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferFuelPercent`: The fuel level of the reefer unit in percentage points (e.g. `99`, `50`, etc). Only supported on reefer solutions.
     * * `carrierReeferState`: The overall state of the reefer (`Off`, `On`). Only supported on multizone Carrier reefer solutions.
     * * `reeferStateZone1`: The state of the reefer in zone 1. For single zone reefers, this applies tot he single zone. Only supported on multizone reefer solutions.
     * * `reeferStateZone2`: The state of the reefer in zone 2. Only supported on multizone reefer solutions.
     * * `reeferStateZone3`: The state of the reefer in zone 3. Only supported on multizone reefer solutions.
     * * `reeferRunMode`: The operational mode of the reefer (`Start/Stop`, `Continuous`)
     * * `reeferAlarms`: Any alarms that are present on the reefer. Only supported on reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone1`: The return air temperature in zone 1 of the reefer in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone2`: The return air temperature in zone 2 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone3`: The return air temperature in zone 3 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone1`: The set point temperature in zone 1 of the reefer in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone2`: The set point temperature in zone 2 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone3`: The set point temperature in zone 3 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferDoorStateZone1`: The door status in zone 1 of the reefer. For single zone reefers, this applies to the single zone.
     * * `reeferDoorStateZone2`: The door status in zone 2 of the reefer. Only supported on multizone reefer solutions.
     * * `reeferDoorStateZone3`: The door status in zone 3 of the reefer. Only supported on multizone reefer solutions.
     */
    public function getTrailerStatsFeed(
        string $types,
        ?string $tagIds = null,
        ?string $parentTagIds = null,
        ?string $trailerIds = null,
        ?string $decorations = null
    ): Response {
        return $this->connector->send(
            new GetTrailerStatsFeed($types, $tagIds, $parentTagIds, $trailerIds, $decorations)
        );
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $types  The stat types you want this endpoint to return information on.
     *
     * You may list **up to 3** types using comma-separated format. For example: `types=gps,reeferAmbientAirTemperatureMilliC,gpsOdometerMeters`.
     *
     * * `gps`: GPS data including lat/long, heading, speed, and a reverse geocode address.
     * * `gpsOdometerMeters`: Odometer reading provided by GPS calculations. You must provide a manual odometer reading before this value is updated. Manual odometer readings can be provided via the PATCH /fleet/trailers/{id} endpoint or through the [cloud dashboard](https://kb.samsara.com/hc/en-us/articles/115005273667-Editing-Odometer-Reading). Odometer readings wthat are manually set will update as GPS trip data is gathered.
     * * `reeferAmbientAirTemperatureMilliC`: The ambient air temperature reading of the reefer in millidegree Celsius.
     * * `reeferObdEngineSeconds`: The cumulative number of seconds the reefer has run according to onboard diagnostics. Only supported on reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone1`: The supply or discharge air temperature zone 1 in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone2`: The supply or discharge air temperature zone 2 in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone3`: The supply or discharge air temperature zone 3 in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferFuelPercent`: The fuel level of the reefer unit in percentage points (e.g. `99`, `50`, etc). Only supported on reefer solutions.
     * * `carrierReeferState`: The overall state of the reefer (`Off`, `On`). Only supported on multizone Carrier reefer solutions.
     * * `reeferStateZone1`: The state of the reefer in zone 1. For single zone reefers, this applies tot he single zone. Only supported on multizone reefer solutions.
     * * `reeferStateZone2`: The state of the reefer in zone 2. Only supported on multizone reefer solutions.
     * * `reeferStateZone3`: The state of the reefer in zone 3. Only supported on multizone reefer solutions.
     * * `reeferRunMode`: The operational mode of the reefer (`Start/Stop`, `Continuous`)
     * * `reeferAlarms`: Any alarms that are present on the reefer. Only supported on reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone1`: The return air temperature in zone 1 of the reefer in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone2`: The return air temperature in zone 2 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone3`: The return air temperature in zone 3 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone1`: The set point temperature in zone 1 of the reefer in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone2`: The set point temperature in zone 2 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone3`: The set point temperature in zone 3 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferDoorStateZone1`: The door status in zone 1 of the reefer. For single zone reefers, this applies to the single zone.
     * * `reeferDoorStateZone2`: The door status in zone 2 of the reefer. Only supported on multizone reefer solutions.
     * * `reeferDoorStateZone3`: The door status in zone 3 of the reefer. Only supported on multizone reefer solutions.
     * @param  string  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  string  $trailerIds  A filter on the data based on this comma-separated list of trailer IDs and externalIds. Example: `trailerIds=1234,5678,samsara.vin:1HGBH41JXMN109186`
     * @param  string  $decorations  Decorations add to the primary stats listed in the `types` parameter. For example, if you wish to know the trailer's location whenever the odometer updates, you may set `types=gpsOdometerMeters&decorations=gps`.
     *
     * You may list **up to 2** types using comma-separated format. If multiple stats are listed in the types parameter, the decorations will be added to each type. For example: `types=reeferStateZone1,reeferAmbientAirTemperatureMilliC,gpsOdometerMeters&decorations=gps` will list GPS decorations for each reeferStateZone1 reading, each reeferAmbientAirTemperatureMilliC reding, and gpsOdometerMeters reading.
     *
     * Note that decorations may significantly increase the response payload size.
     *
     * * `gps`: GPS data including lat/long, heading, speed, and a reverse geocode address.
     * * `gpsOdometerMeters`: Odometer reading provided by GPS calculations. You must provide a manual odometer reading before this value is updated. Manual odometer readings can be provided via the PATCH /fleet/trailers/{id} endpoint or through the [cloud dashboard](https://kb.samsara.com/hc/en-us/articles/115005273667-Editing-Odometer-Reading). Odometer readings wthat are manually set will update as GPS trip data is gathered.
     * * `reeferAmbientAirTemperatureMilliC`: The ambient air temperature reading of the reefer in millidegree Celsius.
     * * `reeferObdEngineSeconds`: The cumulative number of seconds the reefer has run according to onboard diagnostics. Only supported on reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone1`: The supply or discharge air temperature zone 1 in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone2`: The supply or discharge air temperature zone 2 in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone3`: The supply or discharge air temperature zone 3 in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferFuelPercent`: The fuel level of the reefer unit in percentage points (e.g. `99`, `50`, etc). Only supported on reefer solutions.
     * * `carrierReeferState`: The overall state of the reefer (`Off`, `On`). Only supported on multizone Carrier reefer solutions.
     * * `reeferStateZone1`: The state of the reefer in zone 1. For single zone reefers, this applies tot he single zone. Only supported on multizone reefer solutions.
     * * `reeferStateZone2`: The state of the reefer in zone 2. Only supported on multizone reefer solutions.
     * * `reeferStateZone3`: The state of the reefer in zone 3. Only supported on multizone reefer solutions.
     * * `reeferRunMode`: The operational mode of the reefer (`Start/Stop`, `Continuous`)
     * * `reeferAlarms`: Any alarms that are present on the reefer. Only supported on reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone1`: The return air temperature in zone 1 of the reefer in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone2`: The return air temperature in zone 2 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone3`: The return air temperature in zone 3 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone1`: The set point temperature in zone 1 of the reefer in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone2`: The set point temperature in zone 2 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone3`: The set point temperature in zone 3 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferDoorStateZone1`: The door status in zone 1 of the reefer. For single zone reefers, this applies to the single zone.
     * * `reeferDoorStateZone2`: The door status in zone 2 of the reefer. Only supported on multizone reefer solutions.
     * * `reeferDoorStateZone3`: The door status in zone 3 of the reefer. Only supported on multizone reefer solutions.
     */
    public function getTrailerStatsHistory(
        string $startTime,
        string $endTime,
        string $types,
        ?string $tagIds = null,
        ?string $parentTagIds = null,
        ?string $trailerIds = null,
        ?string $decorations = null
    ): Response {
        return $this->connector->send(
            new GetTrailerStatsHistory(
                $startTime,
                $endTime,
                $types,
                $tagIds,
                $parentTagIds,
                $trailerIds,
                $decorations
            )
        );
    }

    /**
     * @param  string  $types  The stat types you want this endpoint to return information on.
     *
     * You may list **up to 3** types using comma-separated format. For example: `types=gps,reeferAmbientAirTemperatureMilliC,gpsOdometerMeters`.
     *
     * * `gps`: GPS data including lat/long, heading, speed, and a reverse geocode address.
     * * `gpsOdometerMeters`: Odometer reading provided by GPS calculations. You must provide a manual odometer reading before this value is updated. Manual odometer readings can be provided via the PATCH /fleet/trailers/{id} endpoint or through the [cloud dashboard](https://kb.samsara.com/hc/en-us/articles/115005273667-Editing-Odometer-Reading). Odometer readings wthat are manually set will update as GPS trip data is gathered.
     * * `reeferAmbientAirTemperatureMilliC`: The ambient air temperature reading of the reefer in millidegree Celsius.
     * * `reeferObdEngineSeconds`: The cumulative number of seconds the reefer has run according to onboard diagnostics. Only supported on reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone1`: The supply or discharge air temperature zone 1 in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone2`: The supply or discharge air temperature zone 2 in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSupplyAirTemperatureMilliCZone3`: The supply or discharge air temperature zone 3 in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferFuelPercent`: The fuel level of the reefer unit in percentage points (e.g. `99`, `50`, etc). Only supported on reefer solutions.
     * * `carrierReeferState`: The overall state of the reefer (`Off`, `On`). Only supported on multizone Carrier reefer solutions.
     * * `reeferStateZone1`: The state of the reefer in zone 1. For single zone reefers, this applies tot he single zone. Only supported on multizone reefer solutions.
     * * `reeferStateZone2`: The state of the reefer in zone 2. Only supported on multizone reefer solutions.
     * * `reeferStateZone3`: The state of the reefer in zone 3. Only supported on multizone reefer solutions.
     * * `reeferRunMode`: The operational mode of the reefer (`Start/Stop`, `Continuous`)
     * * `reeferAlarms`: Any alarms that are present on the reefer. Only supported on reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone1`: The return air temperature in zone 1 of the reefer in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone2`: The return air temperature in zone 2 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferReturnAirTemperatureMilliCZone3`: The return air temperature in zone 3 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone1`: The set point temperature in zone 1 of the reefer in millidegrees Celsius. For single zone reefers, this applies to the single zone. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone2`: The set point temperature in zone 2 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferSetPointTemperatureMilliCZone3`: The set point temperature in zone 3 of the reefer in millidegrees Celsius. Only supported on multizone reefer solutions.
     * * `reeferDoorStateZone1`: The door status in zone 1 of the reefer. For single zone reefers, this applies to the single zone.
     * * `reeferDoorStateZone2`: The door status in zone 2 of the reefer. Only supported on multizone reefer solutions.
     * * `reeferDoorStateZone3`: The door status in zone 3 of the reefer. Only supported on multizone reefer solutions.
     * @param  string  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  string  $trailerIds  A filter on the data based on this comma-separated list of trailer IDs and externalIds. Example: `trailerIds=1234,5678,samsara.vin:1HGBH41JXMN109186`
     * @param  string  $time  A filter on the data that returns the last known data points with timestamps less than or equal to this value. Defaults to now if not provided. Must be a string in RFC 3339 Format. Millisecond precision and timezones are supported.
     */
    public function getTrailerStatsSnapshot(
        string $types,
        ?string $tagIds = null,
        ?string $parentTagIds = null,
        ?string $trailerIds = null,
        ?string $time = null
    ): Response {
        return $this->connector->send(
            new GetTrailerStatsSnapshot($types, $tagIds, $parentTagIds, $trailerIds, $time)
        );
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array  $learnerIds  Optional string of comma separated learner IDs. If learner ID is present, training assignments for the specified learner(s) will be returned. Max value for this value is 100 objects. Example: `learnerIds=driver-281474,driver-46282156`
     * @param  array  $courseIds  Optional string of comma separated course IDs. If course ID is present, training assignments for the specified course ID(s) will be returned. Max value for this value is 100 objects. Defaults to returning all courses. Example: `courseIds=a4db8702-79d5-4396-a717-e301d52ecc11,c6490f6a-d84e-49b5-b0ad-b6baae304075`
     * @param  array  $status  Optional string of comma separated values. If status is present, training assignments for the specified status(s) will be returned. Valid values: "notStarted", "inProgress", "completed". Defaults to returning all courses.
     */
    public function getTrainingAssignmentsStream(
        string $startTime,
        ?string $endTime = null,
        ?array $learnerIds = null,
        ?array $courseIds = null,
        ?array $status = null
    ): Response {
        return $this->connector->send(
            new GetTrainingAssignmentsStream($startTime, $endTime, $learnerIds, $courseIds, $status)
        );
    }

    /**
     * @param  array  $courseIds  Optional string of comma separated course IDs. If course ID is present, training assignments for the specified course ID(s) will be returned. Max value for this value is 100 objects. Defaults to returning all courses. Example: `courseIds=a4db8702-79d5-4396-a717-e301d52ecc11,c6490f6a-d84e-49b5-b0ad-b6baae304075`
     * @param  array  $categoryIds  Optional string of comma separated course category IDs. If courseCategoryId is present, training courses for the specified course category(s) will be returned. Max value for this value is 100 objects. Defaults to returning all courses.  Example: `categoryIds=a4db8702-79d5-4396-a717-e301d52ecc11,c6490f6a-d84e-49b5-b0ad-b6baae304075`
     * @param  array  $status  Optional string of comma separated values. If status is present, training courses with the specified status(s) will be returned. Valid values: “published”, “deleted”, “archived”. Defaults to returning all courses.
     */
    public function getTrainingCourses(
        ?array $courseIds = null,
        ?array $categoryIds = null,
        ?array $status = null
    ): Response {
        return $this->connector->send(new GetTrainingCourses($courseIds, $categoryIds, $status));
    }

    /**
     * @param  bool  $includeAsset  Indicates whether or not to return expanded “asset” data
     * @param  string  $completionStatus  Filters trips based on a specific completion status  Valid values: `inProgress`, `completed`, `all`
     * @param  string  $startTime  RFC 3339 timestamp that indicates when to begin receiving data. Value is compared against `updatedAtTime` or `tripStartTime` depending on the queryBy parameter.
     * @param  string  $endTime  RFC 3339 timestamp which is compared against `updatedAtTime` or `tripStartTime` depending on the queryBy parameter. If not provided then the endpoint behaves as an unending feed of changes.
     * @param  string  $queryBy  Decide which timestamp the `startTime` and `endTime` are compared to.  Valid values: `updatedAtTime`, `tripStartTime`
     * @param  array  $ids  Comma-separated list of asset IDs. Include up to 50 asset IDs.
     */
    public function getTrips(
        ?bool $includeAsset,
        ?string $completionStatus,
        string $startTime,
        ?string $endTime,
        ?string $queryBy,
        array $ids
    ): Response {
        return $this->connector->send(
            new GetTrips($includeAsset, $completionStatus, $startTime, $endTime, $queryBy, $ids)
        );
    }

    /**
     * @param  array  $ids  Filter by the IDs. Up to 100 ids. Returns all if no ids are provided.
     */
    public function getWorkOrders(?array $ids = null): Response
    {
        return $this->connector->send(new GetWorkOrders($ids));
    }

    /**
     * @param  string  $type  The operational context in which the asset interacts with the Samsara system. Examples: Vehicle (eg: truck, bus...), Trailer (eg: dry van, reefer, flatbed...), Powered Equipment (eg: dozer, crane...), Unpowered Equipment (eg: container, dumpster...), or Uncategorized.  Valid values: `uncategorized`, `trailer`, `equipment`, `unpowered`, `vehicle`
     * @param  string  $updatedAfterTime  A filter on data to have an updated at time after or equal to this specified time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  bool  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     * @param  bool  $includeTags  Optional boolean indicating whether to return tags on supported entities
     * @param  string  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $ids  A filter on the data based on this comma-separated list of asset IDs and External IDs.
     * @param  string  $attributeValueIds  A filter on the data based on this comma-separated list of attribute value IDs. Only entities associated with ALL of the referenced values will be returned (i.e. the intersection of the sets of entities with each value). Example: `attributeValueIds=076efac2-83b5-47aa-ba36-18428436dcac,6707b3f0-23b9-4fe3-b7be-11be34aea544`
     */
    public function listAssets(
        ?string $type = null,
        ?string $updatedAfterTime = null,
        ?bool $includeExternalIds = null,
        ?bool $includeTags = null,
        ?string $tagIds = null,
        ?string $parentTagIds = null,
        ?array $ids = null,
        ?string $attributeValueIds = null
    ): Response {
        return $this->connector->send(
            new ListAssets(
                $type,
                $updatedAfterTime,
                $includeExternalIds,
                $includeTags,
                $tagIds,
                $parentTagIds,
                $ids,
                $attributeValueIds
            )
        );
    }

    /**
     * @param  string  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs and externalIds. You can specify up to 20 vehicles. Example: `vehicleIds=1234,5678,samsara.vin:1HGBH41JXMN109186`
     * @param  array  $inputs  An optional list of desired camera inputs for which to return captured media. If empty, media for all available inputs will be returned.
     * @param  array  $mediaTypes  An optional list of desired media types for which to return captured media. If empty, media for all available media types will be returned. Possible options include: image, videoHighRes.
     * @param  array  $triggerReasons  An optional list of desired trigger reasons for which to return captured media. If empty, media for all available trigger reasons will be returned. Possible options include: api, panicButton, periodicStill, safetyEvent, tripEndStill, tripStartStill, videoRetrieval. videoRetrieval represents media captured for a dashboard video retrieval request.
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. End time cannot be more than 24 hours after startTime. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $availableAfterTime  An optional timestamp in RFC 3339 format that can act as a cursor to track which media has previously been retrieved; only media whose availableAtTime comes after this parameter will be returned. Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00
     */
    public function listUploadedMedia(
        string $vehicleIds,
        ?array $inputs,
        ?array $mediaTypes,
        ?array $triggerReasons,
        string $startTime,
        string $endTime,
        ?string $availableAfterTime = null
    ): Response {
        return $this->connector->send(
            new ListUploadedMedia(
                $vehicleIds,
                $inputs,
                $mediaTypes,
                $triggerReasons,
                $startTime,
                $endTime,
                $availableAfterTime
            )
        );
    }

    /**
     * @param  string  $id  The unique Samsara ID of the Equipment. This is automatically generated when the Equipment object is created. It cannot be changed.
     */
    public function patchEquipment(string $id, array $payload = []): Response
    {
        return $this->connector->send(new PatchEquipment($id, $payload));
    }

    public function patchFormSubmission(array $payload = []): Response
    {
        return $this->connector->send(new PatchFormSubmission($payload));
    }

    public function patchIssue(array $payload = []): Response
    {
        return $this->connector->send(new PatchIssue($payload));
    }

    /**
     * @param  string  $id  A jobId or uuid in STRING format. JobId must be prefixed with `jobId:`(Examples: `"8d218e6c-7a16-4f9f-90f7-cc1d93b9e596"`, `"jobId:98765"`).
     */
    public function patchJob(string $id, array $payload = []): Response
    {
        return $this->connector->send(new PatchJob($id, $payload));
    }

    /**
     * @param  array  $ids  String of comma separated assignments IDs. Max value for this value is 100 objects .Example: `ids=a4db8702-79d5-4396-a717-e301d52ecc11,c6490f6a-d84e-49b5-b0ad-b6baae304075`
     * @param  string  $dueAtTime  Due date of the training assignment in RFC 3339 format. Millisecond precision and timezones are supported.
     */
    public function patchTrainingAssignments(array $ids, string $dueAtTime, array $payload = []): Response
    {
        return $this->connector->send(new PatchTrainingAssignments($ids, $dueAtTime, $payload));
    }

    public function patchWorkOrders(array $payload = []): Response
    {
        return $this->connector->send(new PatchWorkOrders($payload));
    }

    public function postDriverRemoteSignout(array $payload = []): Response
    {
        return $this->connector->send(new PostDriverRemoteSignout($payload));
    }

    public function postFormSubmission(array $payload = []): Response
    {
        return $this->connector->send(new PostFormSubmission($payload));
    }

    /**
     * @param  string  $id  ID of the form submission to create a PDF export from.
     */
    public function postFormSubmissionsPdfExports(string $id, array $payload = []): Response
    {
        return $this->connector->send(new PostFormSubmissionsPdfExports($id, $payload));
    }

    /**
     * @param  string  $courseId  String for the course ID.
     * @param  string  $dueAtTime  Due date of the training assignment in RFC 3339 format. Millisecond precision and timezones are supported.
     * @param  array  $learnerIds  Optional string of comma separated learner IDs. If learner ID is present, training assignments for the specified learner(s) will be returned. Max value for this value is 100 objects. Example: `learnerIds=driver-281474,driver-46282156`
     */
    public function postTrainingAssignments(
        string $courseId,
        string $dueAtTime,
        array $learnerIds,
        array $payload = []
    ): Response {
        return $this->connector->send(
            new PostTrainingAssignments($courseId, $dueAtTime, $learnerIds, $payload)
        );
    }

    public function postWorkOrders(array $payload = []): Response
    {
        return $this->connector->send(new PostWorkOrders($payload));
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array  $workOrderStatuses  Work Order status filter.
     * @param  array  $assetIds  Work Order asset id filter. Up to 50 ids.
     * @param  array  $assignedUserIds  Work Order assigned user id filter. Up to 50 ids.
     */
    public function streamWorkOrders(
        string $startTime,
        ?string $endTime = null,
        ?array $workOrderStatuses = null,
        ?array $assetIds = null,
        ?array $assignedUserIds = null
    ): Response {
        return $this->connector->send(
            new StreamWorkOrders(
                $startTime,
                $endTime,
                $workOrderStatuses,
                $assetIds,
                $assignedUserIds
            )
        );
    }

    /**
     * @param  string  $id  A filter selecting a single asset by id.
     */
    public function updateAsset(string $id, array $payload = []): Response
    {
        return $this->connector->send(new UpdateAsset($id, $payload));
    }

    /**
     * @param  string  $id  Samsara ID for the assignment.
     */
    public function updateDriverTrailerAssignment(string $id, array $payload = []): Response
    {
        return $this->connector->send(new UpdateDriverTrailerAssignment($id, $payload));
    }

    /**
     * @param  int  $id  Vehicle ID
     */
    public function updateEngineImmobilizerState(int $id, array $payload = []): Response
    {
        return $this->connector->send(new UpdateEngineImmobilizerState($id, $payload));
    }

    /**
     * @param  string  $hosDate  A start date in yyyy-mm-dd format. Required.
     * @param  string  $driverId  ID of the driver for whom the duty status is being set.
     */
    public function updateShippingDocs(string $hosDate, string $driverId, array $payload = []): Response
    {
        return $this->connector->send(new UpdateShippingDocs($hosDate, $driverId, $payload));
    }
}
