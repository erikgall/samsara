<?php

namespace Samsara\Data\Industrial;

use Samsara\Data\Entity;

/**
 * DataPoint entity.
 *
 * Represents a data point reading.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $time Timestamp (RFC 3339)
 * @property-read float|null $value Data value
 * @property-read array{id?: string, name?: string}|null $dataInput Associated data input
 */
class DataPoint extends Entity {}
