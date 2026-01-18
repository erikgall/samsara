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
    public function createDriverAuthToken(string $driverId, array $data = []): array
    {
        $response = $this->client()->post("/fleet/drivers/{$driverId}/auth-token", $data);

        $this->handleError($response);

        return $response->json('data', $response->json());
    }

    /**
     * Lock a vehicle.
     *
     * @return array<string, mixed>
     */
    public function lockVehicle(string $vehicleId): array
    {
        $response = $this->client()->post("/fleet/vehicles/{$vehicleId}/lock");

        $this->handleError($response);

        return $response->json('data', $response->json());
    }

    /**
     * Unlock a vehicle.
     *
     * @return array<string, mixed>
     */
    public function unlockVehicle(string $vehicleId): array
    {
        $response = $this->client()->post("/fleet/vehicles/{$vehicleId}/unlock");

        $this->handleError($response);

        return $response->json('data', $response->json());
    }
}
