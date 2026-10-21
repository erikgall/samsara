<?php

namespace Samsara\Resources\Integrations;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\LiveShare\LiveShare;
use Samsara\Resources\Concerns\CreatesEntities;

/**
 * LiveSharingLinks resource for the Samsara API.
 *
 * Provides access to live sharing link management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<LiveShare>
 */
class LiveSharingLinksResource extends Resource
{
    /** @use CreatesEntities<LiveShare> */
    use CreatesEntities;

    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/live-shares';

    /**
     * The entity class for this resource.
     *
     * @var class-string<LiveShare>
     */
    protected string $entity = LiveShare::class;

    /**
     * Delete a live sharing link.
     */
    public function delete(string $id): bool
    {
        $endpoint = $this->endpoint.'?id='.rawurlencode($id);
        $response = $this->executeSdkRequest(
            fn () => $this->client()->delete($endpoint),
            $endpoint
        );

        return $response->successful();
    }

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<LiveShare>
     */
    public function query(): Builder
    {
        return $this->newQuery();
    }

    /**
     * Update a live sharing link.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(string $id, array $data): LiveShare
    {
        $endpoint = $this->endpoint.'?id='.rawurlencode($id);
        $response = $this->executeSdkRequest(
            fn () => $this->client()->patch($endpoint, $data),
            $endpoint
        );

        return $this->mapToEntity($this->decodeJsonData($response, $endpoint, false));
    }
}
