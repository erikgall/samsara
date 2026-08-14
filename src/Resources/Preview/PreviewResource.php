<?php

namespace Samsara\Resources\Preview;

use Samsara\Data\Entity;
use Samsara\Resources\Resource;

/**
 * Preview resource for the Samsara API.
 *
 * Provides access to preview endpoints that are in early testing.
 * These endpoints may change significantly or be removed.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class PreviewResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/preview';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Entity>
     */
    protected string $entity = Entity::class;

    /**
     * Create a driver authentication token.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createDriverAuthToken(array $data): array
    {
        $endpoint = '/preview/fleet/drivers/create-auth-token';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, $data),
            $endpoint
        );

        /** @var array<string, mixed> */
        return $this->decodeJsonData($response, $endpoint);
    }

    /**
     * Lock a vehicle.
     */
    public function lockVehicle(string $vehicleId): bool
    {
        $endpoint = '/preview/fleet/vehicles/'.rawurlencode($vehicleId).'/lock';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->put($endpoint),
            $endpoint
        );

        return $response->successful();
    }

    /**
     * Unlock a vehicle.
     */
    public function unlockVehicle(string $vehicleId): bool
    {
        $endpoint = '/preview/fleet/vehicles/'.rawurlencode($vehicleId).'/lock';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->delete($endpoint),
            $endpoint
        );

        return $response->successful();
    }

    /**
     * {@inheritDoc}
     */
    protected function supportedGenericOperations(): array
    {
        return [];
    }
}
