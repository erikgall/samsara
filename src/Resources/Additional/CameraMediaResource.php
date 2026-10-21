<?php

namespace Samsara\Resources\Additional;

use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Resources\Resource;

/**
 * CameraMedia resource for the Samsara API.
 *
 * Provides access to camera media retrieval endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Entity>
 */
class CameraMediaResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/cameras/media';

    /**
     * Get camera media.
     *
     * @param  array<string, mixed>  $parameters
     * @return array<string, mixed>
     */
    public function get(array $parameters): array
    {
        $response = $this->executeSdkGet(
            $this->endpoint,
            $parameters,
            ['inputs', 'mediaTypes', 'triggerReasons'],
        );

        return $this->decodeJsonArray($response, $this->endpoint);
    }

    /**
     * Get a media retrieval by ID.
     *
     * @return array<string, mixed>
     */
    public function getRetrieval(string $id): array
    {
        $endpoint = '/cameras/media/retrieval';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint, ['retrievalId' => $id]),
            $endpoint
        );

        return $this->decodeJsonData($response, $endpoint);
    }

    /**
     * Get a query builder for camera media.
     *
     * @return Builder<Entity>
     */
    public function query(): Builder
    {
        return $this->newQuery()
            ->withoutDefaultPageSize()
            ->withResponseCollectionKey('data.media')
            ->repeatQueryParameters('inputs', 'mediaTypes', 'triggerReasons');
    }

    /**
     * Request media retrieval.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function retrieve(array $data): array
    {
        $endpoint = '/cameras/media/retrieval';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, $data),
            $endpoint
        );

        return $this->decodeJsonData($response, $endpoint);
    }
}
