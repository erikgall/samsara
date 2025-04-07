<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Sensors\V1getSensors;
use ErikGall\Samsara\Requests\Sensors\V1getSensorsDoor;
use ErikGall\Samsara\Requests\Sensors\V1getSensorsCargo;
use ErikGall\Samsara\Requests\Sensors\V1getSensorsHistory;
use ErikGall\Samsara\Requests\Sensors\V1getSensorsHumidity;
use ErikGall\Samsara\Requests\Sensors\V1getSensorsTemperature;

class Sensors extends Resource
{
    public function v1getSensors(): Response
    {
        return $this->connector->send(new V1getSensors);
    }

    public function v1getSensorsCargo(): Response
    {
        return $this->connector->send(new V1getSensorsCargo);
    }

    public function v1getSensorsDoor(): Response
    {
        return $this->connector->send(new V1getSensorsDoor);
    }

    public function v1getSensorsHistory(): Response
    {
        return $this->connector->send(new V1getSensorsHistory);
    }

    public function v1getSensorsHumidity(): Response
    {
        return $this->connector->send(new V1getSensorsHumidity);
    }

    public function v1getSensorsTemperature(): Response
    {
        return $this->connector->send(new V1getSensorsTemperature);
    }
}
