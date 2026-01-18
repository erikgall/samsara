<?php

namespace ErikGall\Samsara\Data\Industrial;

use ErikGall\Samsara\Data\Entity;

/**
 * IndustrialAsset entity.
 *
 * Represents an industrial asset.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Industrial asset ID
 * @property-read string|null $name Asset name
 * @property-read array<int, array{id?: string, name?: string}>|null $dataInputs Data inputs
 * @property-read array{latitude?: float, longitude?: float}|null $location Asset location
 * @property-read array<int, array{id?: string, name?: string}>|null $tags Tags
 */
class IndustrialAsset extends Entity {}
