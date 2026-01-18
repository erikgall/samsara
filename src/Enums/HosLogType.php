<?php

namespace Samsara\Enums;

/**
 * HOS log type enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
enum HosLogType: string
{
    case CERTIFICATION = 'certification';
    case DIAGNOSTIC_MALFUNCTION = 'diagnosticMalfunction';
    case DRIVER_INDICATION = 'driverIndication';
    case DUTY_STATUS = 'dutyStatus';
    case INTERMEDIATE = 'intermediate';
    case LOGIN_LOGOUT = 'loginLogout';
    case POWER_UP_DOWN = 'powerUpDown';
    case REMARK = 'remark';
    case SHIPPING_DOCUMENT = 'shippingDocument';
}
