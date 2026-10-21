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
 *
 * @extends \Samsara\Resources\Resource<Entity>
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
        $endpoint = '/v1/sensors/cargo';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, $data),
            $endpoint
        );

        return $this->decodeJsonArray($response, $endpoint);
    }

    /**
     * Get door status from sensors.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function door(array $data): array
    {
        $endpoint = '/v1/sensors/door';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, $data),
            $endpoint
        );

        return $this->decodeJsonArray($response, $endpoint);
    }

    /**
     * Get sensor history.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function history(array $data): array
    {
        $endpoint = '/v1/sensors/history';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, $data),
            $endpoint
        );

        return $this->decodeJsonArray($response, $endpoint);
    }

    /**
     * Get humidity readings from sensors.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function humidity(array $data): array
    {
        $endpoint = '/v1/sensors/humidity';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, $data),
            $endpoint
        );

        return $this->decodeJsonArray($response, $endpoint);
    }

    /**
     * List all sensors.
     *
     * @return EntityCollection<int, Entity>
     */
    public function list(): EntityCollection
    {
        $endpoint = '/v1/sensors/list';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint),
            $endpoint
        );

        $sensors = $this->decodeJsonList($response, $endpoint, 'sensors');

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
        $endpoint = '/v1/sensors/temperature';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, $data),
            $endpoint
        );

        return $this->decodeJsonArray($response, $endpoint);
    }
}
