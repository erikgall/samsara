<?php

namespace Samsara\Resources\Integrations;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\LiveShare\LiveShare;

/**
 * LiveSharingLinks resource for the Samsara API.
 *
 * Provides access to live sharing link management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class LiveSharingLinksResource extends Resource
{
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
     */
    public function query(): Builder
    {
        return new Builder($this);
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

        return LiveShare::make($response->json('data', []));
    }

    /**
     * Get the inherited generic operations supported by this resource.
     *
     * @return array<int, string>
     */
    protected function supportedGenericOperations(): array
    {
        return ['all', 'create', 'update', 'delete'];
    }
}
