<?php

namespace Samsara\Data\Equipment;

use Samsara\Data\Vehicle\GpsLocation;

/**
 * GPS location returned by equipment stats endpoints.
 *
 * @property-read float|null $latitude GPS latitude in degrees
 * @property-read float|null $longitude GPS longitude in degrees
 * @property-read string|null $time Timestamp of the location reading
 * @property-read float|null $headingDegrees Heading in degrees
 * @property-read float|null $speedMilesPerHour Speed in miles per hour
 * @property-read array{id?: string, name?: string}|null $address Matching Samsara address
 * @property-read array<string, mixed>|null $reverseGeo Reverse geocoded data
 */
class EquipmentGpsLocation extends GpsLocation {}
