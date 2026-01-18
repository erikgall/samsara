<?php

namespace ErikGall\Samsara\Enums;

/**
 * Vehicle stat types enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
enum VehicleStatType: string
{
    case AMBIENT_AIR_TEMPERATURE_MILLIC = 'ambientAirTemperatureMilliC';
    case BAROMETRIC_PRESSURE_PA = 'barometricPressurePa';
    case BATTERY_MILLI_VOLTS = 'batteryMilliVolts';
    case DEF_LEVEL_MILLI_PERCENT = 'defLevelMilliPercent';
    case ENGINE_COOLANT_TEMPERATURE_MILLIC = 'engineCoolantTemperatureMilliC';
    case ENGINE_LOAD_PERCENT = 'engineLoadPercent';
    case ENGINE_OIL_PRESSURE_K_PA = 'engineOilPressureKPa';
    case ENGINE_RPM = 'engineRpm';
    case ENGINE_STATES = 'engineStates';
    case EV_BATTERY_CURRENT_MILLI_AMPS = 'evBatteryCurrentMilliAmps';
    case EV_BATTERY_STATE_OF_HEALTH_MILLI_PERCENT = 'evBatteryStateOfHealthMilliPercent';
    case EV_BATTERY_TEMPERATURE_MILLIC = 'evBatteryTemperatureMilliC';
    case EV_BATTERY_VOLTAGE_MILLI_VOLTS = 'evBatteryVoltageMilliVolts';
    case EV_CHARGING_CURRENT_MILLI_AMPS = 'evChargingCurrentMilliAmps';
    case EV_CHARGING_ENERGY_MICRO_WH = 'evChargingEnergyMicroWh';
    case EV_CHARGING_STATUS = 'evChargingStatus';
    case EV_CHARGING_VOLTAGE_MILLI_VOLTS = 'evChargingVoltageMilliVolts';
    case EV_CONSUMED_ENERGY_MICRO_WH = 'evConsumedEnergyMicroWh';
    case EV_DISTANCE_DRIVEN_METERS = 'evDistanceDrivenMeters';
    case EV_REGENERATED_ENERGY_MICRO_WH = 'evRegeneratedEnergyMicroWh';
    case EV_STATE_OF_CHARGE_MILLI_PERCENT = 'evStateOfChargeMilliPercent';
    case FUEL_PERCENTS = 'fuelPercents';
    case GPS = 'gps';
    case GPS_DISTANCE_METERS = 'gpsDistanceMeters';
    case GPS_ODOMETER_METERS = 'gpsOdometerMeters';
    case INTAKE_MANIFOLD_TEMPERATURE_MILLIC = 'intakeManifoldTemperatureMilliC';
    case OBD_ENGINE_SECONDS = 'obdEngineSeconds';
    case OBD_ODOMETER_METERS = 'obdOdometerMeters';
    case SEATBELT_DRIVER = 'seatbeltDriver';
    case SPREADER_ACTIVE = 'spreaderActive';
    case SPREADER_AIR_TEMP = 'spreaderAirTemp';
    case SPREADER_AUGER_RUNNING = 'spreaderAugerRunning';
    case SPREADER_BLOCK_HEIGHT = 'spreaderBlockHeight';
    case SPREADER_CONVEYOR_RUNNING = 'spreaderConveyorRunning';
    case SPREADER_GRANULAR_BLAST_STATE = 'spreaderGranularBlastState';
    case SPREADER_GRANULAR_NAME = 'spreaderGranularName';
    case SPREADER_GRANULAR_RATE = 'spreaderGranularRate';
    case SPREADER_HOPPER_PERCENT_FULL = 'spreaderHopperPercentFull';
    case SPREADER_LEFT_SPINNER_RUNNING = 'spreaderLeftSpinnerRunning';
    case SPREADER_LIQUID_BLAST_STATE = 'spreaderLiquidBlastState';
    case SPREADER_LIQUID_NAME = 'spreaderLiquidName';
    case SPREADER_LIQUID_RATE = 'spreaderLiquidRate';
    case SPREADER_ON_GROUND = 'spreaderOnGround';
    case SPREADER_PLOW_STATUS = 'spreaderPlowStatus';
    case SPREADER_PREWET_BLAST_STATE = 'spreaderPrewetBlastState';
    case SPREADER_PREWET_NAME = 'spreaderPrewetName';
    case SPREADER_PREWET_RATE = 'spreaderPrewetRate';
    case SPREADER_RIGHT_SPINNER_RUNNING = 'spreaderRightSpinnerRunning';
    case SPREADER_ROAD_TEMP = 'spreaderRoadTemp';
    case SPREADER_VIBRATOR_RUNNING = 'spreaderVibratorRunning';
    case SPREADER_WORK_LIGHT = 'spreaderWorkLight';
    case TIRE_PRESSURE_MILLI_PSIS = 'tirePressureMilliPsis';
}
