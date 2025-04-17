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
     * @param  string  $id  Gateway serial number
     */
    public function deleteGateway(string $id): Response
    {
        return $this->connector->send(new DeleteGateway($id));
    }

    /**
     * @param  array  $models  Filter by a comma separated list of gateway models.
     */
    public function getGateways(?array $models = null): Response
    {
        return $this->connector->send(new GetGateways($models));
    }

    public function postGateway(array $payload = []): Response
    {
        return $this->connector->send(new PostGateway($payload));
    }
}
