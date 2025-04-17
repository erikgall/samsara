<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\DriverQrCodes\GetDriversQrCodes;
use ErikGall\Samsara\Requests\DriverQrCodes\CreateDriverQrCode;
use ErikGall\Samsara\Requests\DriverQrCodes\DeleteDriverQrCode;

class DriverQrCodes extends Resource
{
    public function createDriverQrCode(array $payload = []): Response
    {
        return $this->connector->send(new CreateDriverQrCode($payload));
    }

    public function deleteDriverQrCode(): Response
    {
        return $this->connector->send(new DeleteDriverQrCode);
    }

    /**
     * @param  array  $driverIds  String of comma separated driver IDs. List of driver - QR codes for specified driver(s) will be returned.
     */
    public function getDriversQrCodes(array $driverIds): Response
    {
        return $this->connector->send(new GetDriversQrCodes($driverIds));
    }
}
