<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Industrial\V1getCameras;
use ErikGall\Samsara\Requests\Industrial\GetDataInputs;
use ErikGall\Samsara\Requests\Industrial\V1getMachines;
use ErikGall\Samsara\Requests\Industrial\V1getVisionRuns;
use ErikGall\Samsara\Requests\Industrial\GetIndustrialAssets;
use ErikGall\Samsara\Requests\Industrial\GetDataInputDataFeed;
use ErikGall\Samsara\Requests\Industrial\PatchIndustrialAsset;
use ErikGall\Samsara\Requests\Industrial\V1getMachinesHistory;
use ErikGall\Samsara\Requests\Industrial\CreateIndustrialAsset;
use ErikGall\Samsara\Requests\Industrial\DeleteIndustrialAsset;
use ErikGall\Samsara\Requests\Industrial\GetVisionRunsByCamera;
use ErikGall\Samsara\Requests\Industrial\PatchAssetDataOutputs;
use ErikGall\Samsara\Requests\Industrial\GetDataInputDataHistory;
use ErikGall\Samsara\Requests\Industrial\GetDataInputDataSnapshot;
use ErikGall\Samsara\Requests\Industrial\V1getVisionLatestRunCamera;
use ErikGall\Samsara\Requests\Industrial\V1getVisionProgramsByCamera;
use ErikGall\Samsara\Requests\Industrial\V1getVisionRunsByCameraAndProgram;

class Industrial extends Resource
{
    public function createIndustrialAsset(array $payload = []): Response
    {
        return $this->connector->send(new CreateIndustrialAsset($payload));
    }

    /**
     * @param  string  $id  Id of the asset to be deleted.
     */
    public function deleteIndustrialAsset(string $id): Response
    {
        return $this->connector->send(new DeleteIndustrialAsset($id));
    }

    /**
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $dataInputIds  A comma-separated list of data input IDs. Example: `dataInputIds=1234,5678`
     * @param  array  $assetIds  A comma-separated list of industrial asset UUIDs. Example: `assetIds=076efac2-83b5-47aa-ba36-18428436dcac,6707b3f0-23b9-4fe3-b7be-11be34aea544`
     */
    public function getDataInputDataFeed(
        ?array $parentTagIds = null,
        ?array $tagIds = null,
        ?array $dataInputIds = null,
        ?array $assetIds = null
    ): Response {
        return $this->connector->send(
            new GetDataInputDataFeed($parentTagIds, $tagIds, $dataInputIds, $assetIds)
        );
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $dataInputIds  A comma-separated list of data input IDs. Example: `dataInputIds=1234,5678`
     * @param  array  $assetIds  A comma-separated list of industrial asset UUIDs. Example: `assetIds=076efac2-83b5-47aa-ba36-18428436dcac,6707b3f0-23b9-4fe3-b7be-11be34aea544`
     */
    public function getDataInputDataHistory(
        string $startTime,
        string $endTime,
        ?array $parentTagIds = null,
        ?array $tagIds = null,
        ?array $dataInputIds = null,
        ?array $assetIds = null
    ): Response {
        return $this->connector->send(
            new GetDataInputDataHistory(
                $startTime,
                $endTime,
                $parentTagIds,
                $tagIds,
                $dataInputIds,
                $assetIds
            )
        );
    }

    /**
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $dataInputIds  A comma-separated list of data input IDs. Example: `dataInputIds=1234,5678`
     * @param  array  $assetIds  A comma-separated list of industrial asset UUIDs. Example: `assetIds=076efac2-83b5-47aa-ba36-18428436dcac,6707b3f0-23b9-4fe3-b7be-11be34aea544`
     */
    public function getDataInputDataSnapshot(
        ?array $parentTagIds = null,
        ?array $tagIds = null,
        ?array $dataInputIds = null,
        ?array $assetIds = null
    ): Response {
        return $this->connector->send(
            new GetDataInputDataSnapshot($parentTagIds, $tagIds, $dataInputIds, $assetIds)
        );
    }

    /**
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $assetIds  A comma-separated list of industrial asset UUIDs. Example: `assetIds=076efac2-83b5-47aa-ba36-18428436dcac,6707b3f0-23b9-4fe3-b7be-11be34aea544`
     */
    public function getDataInputs(
        ?int $limit = null,
        ?array $parentTagIds = null,
        ?array $tagIds = null,
        ?array $assetIds = null
    ): Response {
        return $this->connector->send(new GetDataInputs($limit, $parentTagIds, $tagIds, $assetIds));
    }

    /**
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  array  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  array  $assetIds  A comma-separated list of industrial asset UUIDs. Example: `assetIds=076efac2-83b5-47aa-ba36-18428436dcac,6707b3f0-23b9-4fe3-b7be-11be34aea544`
     */
    public function getIndustrialAssets(
        ?int $limit = null,
        ?array $parentTagIds = null,
        ?array $tagIds = null,
        ?array $assetIds = null
    ): Response {
        return $this->connector->send(
            new GetIndustrialAssets($limit, $parentTagIds, $tagIds, $assetIds)
        );
    }

    /**
     * @param  int  $cameraId  The camera_id should be valid for the given accessToken.
     * @param  int  $durationMs  DurationMs is a required param. This works with the EndMs parameter. Indicates the duration in which the visionRuns will be fetched
     * @param  int  $endMs  EndMs is an optional param. It will default to the current time.
     */
    public function getVisionRunsByCamera(
        int $cameraId,
        int $durationMs,
        ?int $endMs = null
    ): Response {
        return $this->connector->send(new GetVisionRunsByCamera($cameraId, $durationMs, $endMs));
    }

    /**
     * @param  string  $id  Asset ID
     */
    public function patchAssetDataOutputs(string $id, array $payload = []): Response
    {
        return $this->connector->send(new PatchAssetDataOutputs($id, $payload));
    }

    /**
     * @param  string  $id  Id of the asset to be updated
     */
    public function patchIndustrialAsset(string $id, array $payload = []): Response
    {
        return $this->connector->send(new PatchIndustrialAsset($id, $payload));
    }

    public function v1getCameras(): Response
    {
        return $this->connector->send(new V1getCameras);
    }

    public function v1getMachines(): Response
    {
        return $this->connector->send(new V1getMachines);
    }

    public function v1getMachinesHistory(): Response
    {
        return $this->connector->send(new V1getMachinesHistory);
    }

    /**
     * @param  int  $cameraId  The camera_id should be valid for the given accessToken.
     * @param  int  $programId  The configured program's ID on the camera.
     * @param  int  $startedAtMs  EndMs is an optional param. It will default to the current time.
     * @param  string  $include  Include is a filter parameter. Accepts 'pass', 'reject' or 'no_read'.
     * @param  int  $limit  Limit is an integer value from 1 to 1,000.
     */
    public function v1getVisionLatestRunCamera(
        int $cameraId,
        ?int $programId = null,
        ?int $startedAtMs = null,
        ?string $include = null,
        ?int $limit = null
    ): Response {
        return $this->connector->send(
            new V1getVisionLatestRunCamera($cameraId, $programId, $startedAtMs, $include, $limit)
        );
    }

    /**
     * @param  int  $cameraId  The camera_id should be valid for the given accessToken.
     */
    public function v1getVisionProgramsByCamera(int $cameraId): Response
    {
        return $this->connector->send(new V1getVisionProgramsByCamera($cameraId));
    }

    /**
     * @param  int  $durationMs  DurationMs is a required param. This works with the EndMs parameter. Indicates the duration in which the visionRuns will be fetched
     * @param  int  $endMs  EndMs is an optional param. It will default to the current time.
     */
    public function v1getVisionRuns(int $durationMs, ?int $endMs = null): Response
    {
        return $this->connector->send(new V1getVisionRuns($durationMs, $endMs));
    }

    /**
     * @param  int  $cameraId  The camera_id should be valid for the given accessToken.
     * @param  int  $programId  The configured program's ID on the camera.
     * @param  int  $startedAtMs  Started_at_ms is a required param. Indicates the start time of the run to be fetched.
     * @param  string  $include  Include is a filter parameter. Accepts 'pass', 'reject' or 'no_read'.
     */
    public function v1getVisionRunsByCameraAndProgram(
        int $cameraId,
        int $programId,
        int $startedAtMs,
        ?string $include = null
    ): Response {
        return $this->connector->send(
            new V1getVisionRunsByCameraAndProgram($cameraId, $programId, $startedAtMs, $include)
        );
    }
}
