<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\PreviewApis\GetFormTemplates;
use ErikGall\Samsara\Requests\PreviewApis\GetDriverEfficiencyByDrivers;
use ErikGall\Samsara\Requests\PreviewApis\GetDriverEfficiencyByVehicles;

class PreviewApis extends Resource
{
    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Must be in multiple of hours and at least 1 day before endTime. Timezones are supported. Note that the most recent 72 hours of data may still be processing and is subject to change and latency, so it is not recommended to request data for the most recent 72 hours. (Examples: 2019-06-11T19:00:00Z, 2015-09-12T14:00:00-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Must be in multiple of hours and no later than 3 hours before the current time. Timezones are supported. Note that the most recent 72 hours of data may still be processing and is subject to change and latency, so it is not recommended to request data for the most recent 72 hours. (Examples: 2019-06-13T19:00:00Z, 2015-09-15T14:00:00-04:00).
     * @param  array  $driverIds  A filter on the data based on this comma-separated list of driver IDs and externalIds. Example: `driverIds=1234,5678,payroll:4841`
     * @param  array  $dataFormats  A comma-separated list of data formats you want to fetch. Valid values: `score`, `raw` and `percentage`. The default data format is `score`. Example: `dataFormats=raw,score`
     * @param  string  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     */
    public function getDriverEfficiencyByDrivers(
        string $startTime,
        string $endTime,
        ?array $driverIds,
        ?array $dataFormats,
        ?string $tagIds,
        ?string $parentTagIds,
    ): Response {
        return $this->connector->send(new GetDriverEfficiencyByDrivers($startTime, $endTime, $driverIds, $dataFormats, $tagIds, $parentTagIds));
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Must be in multiple of hours and at least 1 day before endTime. Timezones are supported. Note that the most recent 72 hours of data may still be processing and is subject to change and latency, so it is not recommended to request data for the most recent 72 hours. (Examples: 2019-06-11T19:00:00Z, 2015-09-12T14:00:00-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Must be in multiple of hours and no later than 3 hours before the current time. Timezones are supported. Note that the most recent 72 hours of data may still be processing and is subject to change and latency, so it is not recommended to request data for the most recent 72 hours. (Examples: 2019-06-13T19:00:00Z, 2015-09-15T14:00:00-04:00).
     * @param  string  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs and externalIds. Example: `vehicleIds=1234,5678,samsara.vin:1HGBH41JXMN109186`
     * @param  array  $dataFormats  A comma-separated list of data formats you want to fetch. Valid values: `score`, `raw` and `percentage`. The default data format is `score`. Example: `dataFormats=raw,score`
     * @param  string  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     */
    public function getDriverEfficiencyByVehicles(
        string $startTime,
        string $endTime,
        ?string $vehicleIds,
        ?array $dataFormats,
        ?string $tagIds,
        ?string $parentTagIds,
    ): Response {
        return $this->connector->send(new GetDriverEfficiencyByVehicles($startTime, $endTime, $vehicleIds, $dataFormats, $tagIds, $parentTagIds));
    }

    /**
     * @param  array  $ids  A comma-separated list containing up to 100 template IDs to filter on.
     */
    public function getFormTemplates(?array $ids): Response
    {
        return $this->connector->send(new GetFormTemplates($ids));
    }
}
