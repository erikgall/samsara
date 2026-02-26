<?php

namespace Samsara\Resources\Industrial;

use Samsara\Query\Builder;
use Samsara\Data\Asset\Asset;
use Samsara\Resources\Resource;

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
     * Get a query builder for asset depreciation.
     */
    public function depreciation(): Builder
    {
        return $this->createBuilderWithEndpoint('/assets/depreciation/stream');
    }

    /**
     * Get a query builder for asset inputs stream.
     */
    public function inputsStream(): Builder
    {
        return $this->createBuilderWithEndpoint('/assets/inputs/stream');
    }

    /**
     * Get a query builder for asset location and speed stream.
     */
    public function locationAndSpeedStream(): Builder
    {
        return $this->createBuilderWithEndpoint('/assets/location-and-speed/stream');
    }

    /**
     * Get asset locations (Legacy v1 endpoint).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function locations(array $data): array
    {
        $response = $this->client()->get('/v1/fleet/assets/locations', $data);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }

    /**
     * Get reefer stats (Legacy v1 endpoint).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function reefers(array $data): array
    {
        $response = $this->client()->get('/v1/fleet/assets/reefers', $data);

        $this->handleError($response);

        return $response->json();
    }
}
