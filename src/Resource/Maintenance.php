<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Maintenance\GetDvirs;
use ErikGall\Samsara\Requests\Maintenance\CreateDvir;
use ErikGall\Samsara\Requests\Maintenance\UpdateDvir;
use ErikGall\Samsara\Requests\Maintenance\StreamDefects;
use ErikGall\Samsara\Requests\Maintenance\GetDefectTypes;
use ErikGall\Samsara\Requests\Maintenance\UpdateDvirDefect;
use ErikGall\Samsara\Requests\Maintenance\V1getFleetMaintenanceList;

class Maintenance extends Resource
{
    public function createDvir(): Response
    {
        return $this->connector->send(new CreateDvir);
    }

    /**
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  array  $ids  A filter on the data based on this comma-separated list of defect type IDs.
     */
    public function getDefectTypes(?int $limit, ?array $ids): Response
    {
        return $this->connector->send(new GetDefectTypes($limit, $ids));
    }

    /**
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 200 objects.
     * @param  bool  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     * @param  string  $startTime  Required RFC 3339 timestamp to begin the feed or history by `updatedAtTime` at `startTime`.
     * @param  string  $endTime  Optional RFC 3339 timestamp. If not provided then the endpoint behaves as an unending feed of changes.
     * @param  array  $safetyStatus  Optional list of safety statuses. Valid values: [safe, unsafe, resolved]
     */
    public function getDvirs(
        ?int $limit,
        ?bool $includeExternalIds,
        string $startTime,
        ?string $endTime,
        ?array $safetyStatus,
    ): Response {
        return $this->connector->send(new GetDvirs($limit, $includeExternalIds, $startTime, $endTime, $safetyStatus));
    }

    /**
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 200 objects.
     * @param  string  $startTime  Required RFC 3339 timestamp to begin the feed or history by `updatedAtTime` at `startTime`.
     * @param  string  $endTime  Optional RFC 3339 timestamp. If not provided then the endpoint behaves as an unending feed of changes.
     * @param  bool  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     * @param  bool  $isResolved  Boolean value for whether filter defects by resolved status.
     */
    public function streamDefects(
        ?int $limit,
        string $startTime,
        ?string $endTime,
        ?bool $includeExternalIds,
        ?bool $isResolved,
    ): Response {
        return $this->connector->send(new StreamDefects($limit, $startTime, $endTime, $includeExternalIds, $isResolved));
    }

    /**
     * @param  string  $id  ID of the DVIR.
     */
    public function updateDvir(string $id): Response
    {
        return $this->connector->send(new UpdateDvir($id));
    }

    /**
     * @param  string  $id  ID of the defect.
     */
    public function updateDvirDefect(string $id): Response
    {
        return $this->connector->send(new UpdateDvirDefect($id));
    }

    public function v1getFleetMaintenanceList(): Response
    {
        return $this->connector->send(new V1getFleetMaintenanceList);
    }
}
