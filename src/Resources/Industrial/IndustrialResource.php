<?php

namespace Samsara\Resources\Industrial;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Industrial\DataInput;
use Samsara\Data\Industrial\IndustrialAsset;

/**
 * Industrial resource for the Samsara API.
 *
 * Provides access to industrial assets and data inputs endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class IndustrialResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/industrial/assets';

    /**
     * The entity class for this resource.
     *
     * @var class-string<IndustrialAsset>
     */
    protected string $entity = IndustrialAsset::class;

    /**
     * Get a query builder for industrial assets.
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
        /** @var IndustrialAsset */
        return $this->create($data);
    }

    /**
     * Get a query builder for data inputs.
     */
    public function dataInputs(): Builder
    {
        return $this->createBuilderWithEndpoint('/industrial/data-inputs')
            ->usingEntity(DataInput::class);
    }

    /**
     * Get a query builder for data points.
     */
    public function dataPoints(): Builder
    {
        return $this->createBuilderWithEndpoint('/industrial/data-inputs/data-points')
            ->withoutDefaultPageSize()
            ->usingEntity(DataInput::class);
    }

    /**
     * Get a query builder for data points feed.
     */
    public function dataPointsFeed(): Builder
    {
        return $this->createBuilderWithEndpoint('/industrial/data-inputs/data-points/feed')
            ->withoutDefaultPageSize()
            ->usingEntity(DataInput::class);
    }

    /**
     * Get a query builder for data points history.
     */
    public function dataPointsHistory(): Builder
    {
        return $this->createBuilderWithEndpoint('/industrial/data-inputs/data-points/history')
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
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return (new Builder($this))
            ->usingEntity(IndustrialAsset::class);
    }

    /**
     * Update an industrial asset.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateAsset(string $id, array $data): IndustrialAsset
    {
        /** @var IndustrialAsset */
        return $this->update($id, $data);
    }

    /**
     * {@inheritDoc}
     */
    protected function supportedGenericOperations(): array
    {
        return ['all', 'create', 'delete', 'update'];
    }
}
