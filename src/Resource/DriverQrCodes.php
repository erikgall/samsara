<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\DriverQrCodes\GetDriversQrCodes;
use ErikGall\Samsara\Requests\DriverQrCodes\CreateDriverQrCode;
use ErikGall\Samsara\Requests\DriverQrCodes\DeleteDriverQrCode;

class DriverQrCodes extends Resource
{
    /**
     * Create a new Driver QR Code resource.
     *
     * @param  array  $payload  The data to create the driver QR code.
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new CreateDriverQrCode($payload));
    }

    /**
     * Delete a Driver QR Code resource.
     *
     * @return Response
     */
    public function delete(): Response
    {
        return $this->connector->send(new DeleteDriverQrCode);
    }

    /**
     * Get Driver QR Codes for specified drivers.
     *
     * @param  array  $driverIds  List of driver IDs.
     * @return Response
     */
    public function get(array $driverIds): Response
    {
        return $this->connector->send(new GetDriversQrCodes($driverIds));
    }
}
