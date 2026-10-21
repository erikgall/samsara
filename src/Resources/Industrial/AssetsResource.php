<?php

namespace Samsara\Resources\Industrial;

use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Data\Asset\Asset;
use Samsara\Resources\Resource;
use Samsara\Data\Asset\AssetLocation;
use Samsara\Resources\Concerns\CreatesEntities;

/**
 * Assets resource for the Samsara API.
 *
 * Provides access to asset management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Asset>
 */
class AssetsResource extends Resource
{
    /** @use CreatesEntities<Asset> */
    use CreatesEntities;

    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/assets';

    /**
     * The entity class for this resource.
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
     *
     * @return Builder<Entity>
     */
    public function depreciation(): Builder
    {
        return $this->newQuery('/assets/depreciation')
            ->withoutDefaultPageSize()
            ->repeatQueryParameters('assetIds')
            ->usingEntity(Entity::class);
    }

    /**
     * Get a query builder for asset inputs stream.
     *
     * @return Builder<Entity>
     */
    public function inputsStream(): Builder
    {
        return $this->newQuery('/assets/inputs/stream')
            ->withoutDefaultPageSize()
            ->usingEntity(Entity::class);
    }

    /**
     * Get a query builder for asset location and speed stream.
     *
     * @return Builder<AssetLocation>
     */
    public function locationAndSpeedStream(): Builder
    {
        return $this->newQuery('/assets/location-and-speed/stream')
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

        return $this->decodeJsonArray($response, $endpoint);
    }

    /**
     * Get a query builder for legacy asset locations.
     *
     * @return Builder<Entity>
     */
    public function locationsQuery(): Builder
    {
        return $this->newQuery('/v1/fleet/assets/locations')
            ->withResponseCollectionKey('assets')
            ->withCursorParameter('startingAfter')
            ->usingEntity(Entity::class);
    }

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<Asset>
     */
    public function query(): Builder
    {
        return $this->newQuery()
            ->withoutDefaultPageSize()
            ->repeatQueryParameters('attributes');
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

        return $this->decodeJsonArray($response, $endpoint);
    }

    /**
     * Get a query builder for legacy reefer stats.
     *
     * @return Builder<Entity>
     */
    public function reefersQuery(): Builder
    {
        return $this->newQuery('/v1/fleet/assets/reefers')
            ->withResponseCollectionKey('data')
            ->withCursorParameter('startingAfter')
            ->usingEntity(Entity::class);
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

        return $this->mapToEntity($this->decodeJsonData($response, $endpoint));
    }
}
