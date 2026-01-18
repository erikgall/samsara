<?php

namespace Samsara\Resources\Industrial;

use Samsara\Data\Entity;
use Samsara\Resources\Resource;
use Samsara\Data\EntityCollection;

/**
 * Sensors resource for the Samsara API (Legacy v1).
 *
 * Provides access to legacy sensor endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SensorsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/v1/sensors';

    /**
     * Get cargo status from sensors.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function cargo(array $data): array
    {
        $response = $this->client()->post('/v1/sensors/cargo', $data);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get door status from sensors.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function door(array $data): array
    {
        $response = $this->client()->post('/v1/sensors/door', $data);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get sensor history.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function history(array $data): array
    {
        $response = $this->client()->post('/v1/sensors/history', $data);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get humidity readings from sensors.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function humidity(array $data): array
    {
        $response = $this->client()->post('/v1/sensors/humidity', $data);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * List all sensors.
     *
     * @param  array<string, mixed>  $data
     * @return EntityCollection<int, Entity>
     */
    public function list(array $data): EntityCollection
    {
        $response = $this->client()->post('/v1/sensors/list', $data);

        $this->handleError($response);

        $sensors = $response->json('sensors', []);

        return $this->mapToEntities($sensors);
    }

    /**
     * Get temperature readings from sensors.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function temperature(array $data): array
    {
        $response = $this->client()->post('/v1/sensors/temperature', $data);

        $this->handleError($response);

        return $response->json();
    }
}
