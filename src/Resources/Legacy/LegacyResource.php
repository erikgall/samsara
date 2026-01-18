<?php

namespace ErikGall\Samsara\Resources\Legacy;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Resources\Resource;

/**
 * Legacy resource for the Samsara API.
 *
 * Provides access to deprecated v1 endpoints.
 * These endpoints are maintained for backwards compatibility but may be removed.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class LegacyResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/v1';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Entity>
     */
    protected string $entity = Entity::class;

    /**
     * Get asset locations (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function assetLocations(array $data): array
    {
        $response = $this->client()->post('/v1/fleet/assets/locations', $data);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get asset reefers (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function assetReefers(array $data): array
    {
        $response = $this->client()->post('/v1/fleet/assets/reefers', $data);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get dispatch routes (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function dispatchRoutes(array $data): array
    {
        $response = $this->client()->get('/v1/fleet/dispatch/routes', $data);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get driver safety score (v1).
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function driverSafetyScore(string $driverId, array $params): array
    {
        $response = $this->client()->get("/v1/fleet/drivers/{$driverId}/safety/score", $params);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get fleet assets (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function fleetAssets(array $data): array
    {
        $response = $this->client()->post('/v1/fleet/assets', $data);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get HOS authentication logs (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function hosAuthenticationLogs(array $data): array
    {
        $response = $this->client()->post('/v1/fleet/hos_authentication_logs', $data);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get machines (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function machines(array $data): array
    {
        $response = $this->client()->post('/v1/machines/list', $data);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get maintenance list (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function maintenanceList(array $data): array
    {
        $response = $this->client()->post('/v1/fleet/maintenance/list', $data);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get messages (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function messages(array $data): array
    {
        $response = $this->client()->post('/v1/fleet/messages', $data);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get trips (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function trips(array $data): array
    {
        $response = $this->client()->post('/v1/fleet/trips', $data);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get vehicle harsh event (v1).
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function vehicleHarshEvent(string $vehicleId, array $params): array
    {
        $response = $this->client()->get("/v1/fleet/vehicles/{$vehicleId}/safety/harsh_event", $params);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get vehicle safety score (v1).
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function vehicleSafetyScore(string $vehicleId, array $params): array
    {
        $response = $this->client()->get("/v1/fleet/vehicles/{$vehicleId}/safety/score", $params);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get vision cameras (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function visionCameras(array $data): array
    {
        $response = $this->client()->post('/v1/industrial/vision/cameras', $data);

        $this->handleError($response);

        return $response->json();
    }
}
