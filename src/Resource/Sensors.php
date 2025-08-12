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
    /**
     * Get all sensors.
     *
     * @return Response
     */
    public function getSensors(): Response
    {
        return $this->connector->send(new V1getSensors);
    }

    /**
     * Get cargo sensors.
     *
     * @return Response
     */
    public function getSensorsCargo(): Response
    {
        return $this->connector->send(new V1getSensorsCargo);
    }

    /**
     * Get door sensors.
     *
     * @return Response
     */
    public function getSensorsDoor(): Response
    {
        return $this->connector->send(new V1getSensorsDoor);
    }

    /**
     * Get sensors history.
     *
     * @return Response
     */
    public function getSensorsHistory(): Response
    {
        return $this->connector->send(new V1getSensorsHistory);
    }

    /**
     * Get sensors humidity.
     *
     * @return Response
     */
    public function getSensorsHumidity(): Response
    {
        return $this->connector->send(new V1getSensorsHumidity);
    }

    /**
     * Get sensors temperature.
     *
     * @return Response
     */
    public function getSensorsTemperature(): Response
    {
        return $this->connector->send(new V1getSensorsTemperature);
    }
}
