<?php

namespace ErikGall\Samsara\Resources\Additional;

use ErikGall\Samsara\Resources\Resource;
use ErikGall\Samsara\Data\EntityCollection;

/**
 * CameraMedia resource for the Samsara API.
 *
 * Provides access to camera media retrieval endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class CameraMediaResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/camera-media';

    /**
     * Get camera media.
     *
     * @return EntityCollection<int, \ErikGall\Samsara\Data\Entity>
     */
    public function get(): EntityCollection
    {
        $response = $this->client()->get('/camera-media');

        $this->handleError($response);

        return $this->mapToEntities($response->json('data', []));
    }

    /**
     * Get a media retrieval by ID.
     *
     * @return array<string, mixed>
     */
    public function getRetrieval(string $id): array
    {
        $response = $this->client()->get("/camera-media/retrieve/{$id}");

        $this->handleError($response);

        return $response->json('data', $response->json());
    }

    /**
     * Request media retrieval.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function retrieve(array $data): array
    {
        $response = $this->client()->post('/camera-media/retrieve', $data);

        $this->handleError($response);

        return $response->json('data', $response->json());
    }
}
