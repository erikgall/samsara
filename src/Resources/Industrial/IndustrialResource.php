<?php

namespace Samsara\Resources\Industrial;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\EntityCollection;
use Samsara\Data\Industrial\Machine;
use Samsara\Data\Industrial\DataInput;
use Samsara\Exceptions\SamsaraException;
use Samsara\Data\Industrial\VisionCamera;
use Samsara\Data\Industrial\IndustrialAsset;
use Samsara\Resources\Concerns\CreatesEntities;
use Samsara\Resources\Concerns\DeletesEntities;
use Samsara\Resources\Concerns\UpdatesEntities;

/**
 * Industrial resource for the Samsara API.
 *
 * Provides access to industrial assets and data inputs endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<IndustrialAsset>
 */
class IndustrialResource extends Resource
{
    /** @use CreatesEntities<IndustrialAsset> */
    use CreatesEntities;

    /** @use DeletesEntities<IndustrialAsset> */
    use DeletesEntities;

    /** @use UpdatesEntities<IndustrialAsset> */
    use UpdatesEntities;

    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/industrial/assets';

    /**
     * The entity class for this resource.
     */
    protected string $entity = IndustrialAsset::class;

    /**
     * Get a query builder for industrial assets.
     *
     * @return Builder<IndustrialAsset>
     */
    public function assets(): Builder
    {
        return $this->query();
    }

    /**
     * Create a new industrial asset.
     *
     * @param  array<string, mixed>  $data
     */
    public function createAsset(array $data): IndustrialAsset
    {
        return $this->create($data);
    }

    /**
     * Get a query builder for data inputs.
     *
     * @return Builder<DataInput>
     */
    public function dataInputs(): Builder
    {
        return $this->newQuery('/industrial/data-inputs')
            ->usingEntity(DataInput::class);
    }

    /**
     * Get a query builder for data points.
     *
     * @return Builder<DataInput>
     */
    public function dataPoints(): Builder
    {
        return $this->newQuery('/industrial/data-inputs/data-points')
            ->withoutDefaultPageSize()
            ->usingEntity(DataInput::class);
    }

    /**
     * Get a query builder for data points feed.
     *
     * @return Builder<DataInput>
     */
    public function dataPointsFeed(): Builder
    {
        return $this->newQuery('/industrial/data-inputs/data-points/feed')
            ->withoutDefaultPageSize()
            ->usingEntity(DataInput::class);
    }

    /**
     * Get a query builder for data points history.
     *
     * @return Builder<DataInput>
     */
    public function dataPointsHistory(): Builder
    {
        return $this->newQuery('/industrial/data-inputs/data-points/history')
            ->withoutDefaultPageSize()
            ->usingEntity(DataInput::class);
    }

    /**
     * Delete an industrial asset.
     */
    public function deleteAsset(string $id): bool
    {
        return $this->delete($id);
    }

    /**
     * Get legacy industrial machines as typed entities.
     *
     * @return EntityCollection<int, Machine>
     */
    public function machines(): EntityCollection
    {
        $endpoint = '/v1/machines/list';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint),
            $endpoint,
        );
        $machines = $this->decodeJsonList($response, $endpoint, 'machines');

        return new EntityCollection(array_map(Machine::make(...), $machines));
    }

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<IndustrialAsset>
     */
    public function query(): Builder
    {
        return $this->newQuery();
    }

    /**
     * Update an industrial asset.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateAsset(string $id, array $data): IndustrialAsset
    {
        return $this->update($id, $data);
    }

    /**
     * Get legacy vision cameras as typed entities.
     *
     * @return EntityCollection<int, VisionCamera>
     */
    public function visionCameras(): EntityCollection
    {
        $endpoint = '/v1/industrial/vision/cameras';
        $response = $this->executeSdkGet($endpoint);
        $cameras = $this->decodeJsonArray($response, $endpoint);

        if (! array_is_list($cameras)) {
            throw new SamsaraException(
                "Expected a JSON list from successful response for [{$endpoint}].",
                $response->status(),
                null,
                ['status' => $response->status(), 'endpoint' => $endpoint, 'body' => $response->body()],
            );
        }

        return new EntityCollection(array_map(VisionCamera::make(...), $cameras));
    }
}
