<?php

namespace Samsara\Data\Industrial;

use Samsara\Data\Entity;

/**
 * VisionCamera entity.
 *
 * Represents a vision camera.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read int|null $cameraId Camera ID
 * @property-read string|null $cameraName Camera name
 * @property-read string|null $ethernetIp Ethernet IP address
 * @property-read string|null $wifiIp Wi-Fi IP address
 */
class VisionCamera extends Entity {}
