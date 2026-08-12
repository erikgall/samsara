<?php

namespace Samsara\Resources\Industrial;

use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Data\Asset\Asset;
use Samsara\Resources\Resource;
use Samsara\Data\Asset\AssetLocation;

/**
 * Assets resource for the Samsara API.
 *
 * Provides access to asset management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AssetsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/assets';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Asset>
     */
    protected string $entity = Asset::class;

    /**
     * Delete an asset through the current collection query contract.
     */
    public function delete(string $id): bool
    {
        $endpoint = '/assets?id='.rawurlencode($id);
        $response = $this->executeSdkRequest(
            fn () => $this->client()->delete($endpoint),
            $endpoint
        );

        return $response->successful();
    }

    /**
     * Get a query builder for asset depreciation.
     */
    public function depreciation(): Builder
    {
        return $this->createBuilderWithEndpoint('/assets/depreciation')
            ->withoutDefaultPageSize()
            ->usingEntity(Entity::class);
    }

    /**
     * Get a query builder for asset inputs stream.
     */
    public function inputsStream(): Builder
    {
        return $this->createBuilderWithEndpoint('/assets/inputs/stream')
            ->withoutDefaultPageSize()
            ->usingEntity(Entity::class);
    }

    /**
     * Get a query builder for asset location and speed stream.
     */
    public function locationAndSpeedStream(): Builder
    {
        return $this->createBuilderWithEndpoint('/assets/location-and-speed/stream')
            ->usingEntity(AssetLocation::class);
    }

    /**
     * Get asset locations (Legacy v1 endpoint).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function locations(array $data = []): array
    {
        $endpoint = '/v1/fleet/assets/locations';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint, $data),
            $endpoint
        );

        return $response->json();
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return $this->createBuilderWithEndpoint($this->endpoint)
            ->withoutDefaultPageSize();
    }

    /**
     * Get reefer stats (Legacy v1 endpoint).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function reefers(array $data): array
    {
        $endpoint = '/v1/fleet/assets/reefers';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint, $data),
            $endpoint
        );

        return $response->json();
    }

    /**
     * Update an asset through the current collection query contract.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(string $id, array $data): Asset
    {
        $endpoint = '/assets?id='.rawurlencode($id);
        $response = $this->executeSdkRequest(
            fn () => $this->client()->patch($endpoint, $data),
            $endpoint
        );

        /** @var Asset */
        return $this->mapToEntity($response->json('data', $response->json()));
    }

    /**
     * {@inheritDoc}
     */
    protected function supportedGenericOperations(): array
    {
        return ['all', 'create'];
    }
}
