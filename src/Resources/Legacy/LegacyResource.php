<?php

namespace Samsara\Resources\Legacy;

use Samsara\Data\Entity;
use Samsara\Resources\Resource;

/**
 * Legacy resource for the Samsara API.
 *
 * Provides access to deprecated v1 endpoints.
 * These endpoints are maintained for backwards compatibility but may be removed.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Entity>
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
        return $this->getLegacy('/v1/fleet/assets/locations', $data);
    }

    /**
     * Get asset reefers (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function assetReefers(array $data): array
    {
        return $this->getLegacy('/v1/fleet/assets/reefers', $data);
    }

    /**
     * Get driver safety score (v1).
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function driverSafetyScore(string $driverId, array $params): array
    {
        $endpoint = '/v1/fleet/drivers/'.rawurlencode($driverId).'/safety/score';

        return $this->getLegacy($endpoint, $params);
    }

    /**
     * Get fleet assets (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function fleetAssets(array $data): array
    {
        return $this->getLegacy('/v1/fleet/assets', $data);
    }

    /**
     * Get HOS authentication logs (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function hosAuthenticationLogs(array $data): array
    {
        return $this->getLegacy('/v1/fleet/hos_authentication_logs', $data);
    }

    /**
     * Get machines (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function machines(array $data): array
    {
        return $this->postLegacy('/v1/machines/list', $data);
    }

    /**
     * Get maintenance list (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function maintenanceList(array $data): array
    {
        return $this->getLegacy('/v1/fleet/maintenance/list', $data);
    }

    /**
     * Get messages (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function messages(array $data): array
    {
        return $this->getLegacy('/v1/fleet/messages', $data);
    }

    /**
     * Send a message (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function sendMessage(array $data): array
    {
        return $this->postLegacy('/v1/fleet/messages', $data);
    }

    /**
     * Get trips (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function trips(array $data): array
    {
        return $this->getLegacy('/v1/fleet/trips', $data);
    }

    /**
     * Get vehicle harsh event (v1).
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function vehicleHarshEvent(string $vehicleId, array $params): array
    {
        $endpoint = '/v1/fleet/vehicles/'.rawurlencode($vehicleId).'/safety/harsh_event';

        return $this->getLegacy($endpoint, $params);
    }

    /**
     * Get vehicle safety score (v1).
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function vehicleSafetyScore(string $vehicleId, array $params): array
    {
        $endpoint = '/v1/fleet/vehicles/'.rawurlencode($vehicleId).'/safety/score';

        return $this->getLegacy($endpoint, $params);
    }

    /**
     * Get vision cameras (v1).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function visionCameras(array $data): array
    {
        return $this->getLegacy('/v1/industrial/vision/cameras', $data);
    }

    /**
     * Execute a legacy GET request through the shared SDK boundary.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function getLegacy(string $endpoint, array $data): array
    {
        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint, $data),
            $endpoint
        );

        return $this->decodeJsonArray($response, $endpoint);
    }

    /**
     * Execute a legacy POST request through the shared SDK boundary.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function postLegacy(string $endpoint, array $data): array
    {
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, $data),
            $endpoint
        );

        return $this->decodeJsonArray($response, $endpoint);
    }
}
