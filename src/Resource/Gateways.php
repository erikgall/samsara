<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Gateways\GetGateways;
use ErikGall\Samsara\Requests\Gateways\PostGateway;
use ErikGall\Samsara\Requests\Gateways\DeleteGateway;

class Gateways extends Resource
{
    /**
     * Create a new Gateway resource.
     *
     * @param  array  $payload  The data to create the gateway.
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new PostGateway($payload));
    }

    /**
     * Delete a Gateway resource.
     *
     * @param  string  $id  Gateway serial number.
     * @return Response
     */
    public function delete(string $id): Response
    {
        return $this->connector->send(new DeleteGateway($id));
    }

    /**
     * Get a list of Gateway resources.
     *
     * @param  array|null  $models  Filter by gateway models.
     * @return Response
     */
    public function get(?array $models = null): Response
    {
        return $this->connector->send(new GetGateways($models));
    }
}
