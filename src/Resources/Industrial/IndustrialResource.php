<?php

namespace Samsara\Resources\Industrial;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
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
        $response = $this->client()->post('/industrial/assets', $data);

        $this->handleError($response);

        /** @var IndustrialAsset */
        return $this->mapToEntity($response->json('data', $response->json()));
    }

    /**
     * Get a query builder for data inputs.
     */
    public function dataInputs(): Builder
    {
        return $this->createBuilderWithEndpoint('/industrial/data-inputs');
    }

    /**
     * Get a query builder for data points.
     */
    public function dataPoints(): Builder
    {
        return $this->createBuilderWithEndpoint('/industrial/data-points');
    }

    /**
     * Get a query builder for data points feed.
     */
    public function dataPointsFeed(): Builder
    {
        return $this->createBuilderWithEndpoint('/industrial/data-points/feed');
    }

    /**
     * Get a query builder for data points history.
     */
    public function dataPointsHistory(): Builder
    {
        return $this->createBuilderWithEndpoint('/industrial/data-points/history');
    }

    /**
     * Delete an industrial asset.
     */
    public function deleteAsset(string $id): bool
    {
        $response = $this->client()->delete("/industrial/assets/{$id}");

        $this->handleError($response);

        return $response->successful();
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }

    /**
     * Update an industrial asset.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateAsset(string $id, array $data): IndustrialAsset
    {
        $response = $this->client()->patch("/industrial/assets/{$id}", $data);

        $this->handleError($response);

        /** @var IndustrialAsset */
        return $this->mapToEntity($response->json('data', $response->json()));
    }

    /**
     * Create a builder with a custom endpoint.
     */
    protected function createBuilderWithEndpoint(string $endpoint): Builder
    {
        $originalEndpoint = $this->endpoint;
        $this->endpoint = $endpoint;
        $builder = new Builder($this);
        $this->endpoint = $originalEndpoint;

        return $builder;
    }
}
