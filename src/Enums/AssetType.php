<?php

namespace ErikGall\Samsara\Enums;

/**
 * Asset type enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
enum AssetType: string
{
    case CONTAINER = 'container';
    case EQUIPMENT = 'equipment';
    case GENERATOR = 'generator';
    case OTHER = 'other';
    case REEFER = 'reefer';
    case TRAILER = 'trailer';
    case UNPOWERED = 'unpowered';
}
