<?php

namespace ErikGall\Samsara\Enums;

/**
 * Vehicle engine state enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
enum EngineState: string
{
    case IDLE = 'Idle';
    case OFF = 'Off';
    case ON = 'On';
}
