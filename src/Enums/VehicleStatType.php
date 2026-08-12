<?php

namespace Samsara\Enums;

/**
 * Vehicle stat types enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
enum VehicleStatType: string
{
    case AMBIENT_AIR_TEMPERATURE_MILLIC = 'ambientAirTemperatureMilliC';
    case AUX_INPUT_1 = 'auxInput1';
    case AUX_INPUT_10 = 'auxInput10';
    case AUX_INPUT_11 = 'auxInput11';
    case AUX_INPUT_12 = 'auxInput12';
    case AUX_INPUT_13 = 'auxInput13';
    case AUX_INPUT_2 = 'auxInput2';
    case AUX_INPUT_3 = 'auxInput3';
    case AUX_INPUT_4 = 'auxInput4';
    case AUX_INPUT_5 = 'auxInput5';
    case AUX_INPUT_6 = 'auxInput6';
    case AUX_INPUT_7 = 'auxInput7';
    case AUX_INPUT_8 = 'auxInput8';
    case AUX_INPUT_9 = 'auxInput9';
    case BAROMETRIC_PRESSURE_PA = 'barometricPressurePa';
    case BATTERY_MILLI_VOLTS = 'batteryMilliVolts';
    case DEF_LEVEL_MILLI_PERCENT = 'defLevelMilliPercent';
    case ECU_DOOR_STATUS = 'ecuDoorStatus';
    case ECU_SPEED_MPH = 'ecuSpeedMph';
    case ENGINE_COOLANT_TEMPERATURE_MILLIC = 'engineCoolantTemperatureMilliC';
    case ENGINE_IMMOBILIZER = 'engineImmobilizer';
    case ENGINE_LOAD_PERCENT = 'engineLoadPercent';
    case ENGINE_OIL_PRESSURE_K_PA = 'engineOilPressureKPa';
    case ENGINE_RPM = 'engineRpm';
    case ENGINE_STATES = 'engineStates';
    case EV_AVERAGE_BATTERY_TEMPERATURE_MILLI_CELSIUS = 'evAverageBatteryTemperatureMilliCelsius';
    case EV_BATTERY_CURRENT_MILLI_AMP = 'evBatteryCurrentMilliAmp';
    case EV_BATTERY_CURRENT_MILLI_AMPS = 'evBatteryCurrentMilliAmps';
    case EV_BATTERY_STATE_OF_HEALTH_MILLI_PERCENT = 'evBatteryStateOfHealthMilliPercent';
    case EV_BATTERY_TEMPERATURE_MILLIC = 'evBatteryTemperatureMilliC';
    case EV_BATTERY_VOLTAGE_MILLI_VOLT = 'evBatteryVoltageMilliVolt';
    case EV_BATTERY_VOLTAGE_MILLI_VOLTS = 'evBatteryVoltageMilliVolts';
    case EV_CHARGING_CURRENT_MILLI_AMP = 'evChargingCurrentMilliAmp';
    case EV_CHARGING_CURRENT_MILLI_AMPS = 'evChargingCurrentMilliAmps';
    case EV_CHARGING_ENERGY_MICRO_WH = 'evChargingEnergyMicroWh';
    case EV_CHARGING_STATUS = 'evChargingStatus';
    case EV_CHARGING_VOLTAGE_MILLI_VOLT = 'evChargingVoltageMilliVolt';
    case EV_CHARGING_VOLTAGE_MILLI_VOLTS = 'evChargingVoltageMilliVolts';
    case EV_CONSUMED_ENERGY_MICRO_WH = 'evConsumedEnergyMicroWh';
    case EV_DISTANCE_DRIVEN_METERS = 'evDistanceDrivenMeters';
    case EV_REGENERATED_ENERGY_MICRO_WH = 'evRegeneratedEnergyMicroWh';
    case EV_STATE_OF_CHARGE_MILLI_PERCENT = 'evStateOfChargeMilliPercent';
    case FAULT_CODES = 'faultCodes';
    case FUEL_CONSUMED_MILLILITERS = 'fuelConsumedMilliliters';
    case FUEL_PERCENTS = 'fuelPercents';
    case GPS = 'gps';
    case GPS_DISTANCE_METERS = 'gpsDistanceMeters';
    case GPS_ODOMETER_METERS = 'gpsOdometerMeters';
    case IDLING_DURATION_MILLISECONDS = 'idlingDurationMilliseconds';
    case INTAKE_MANIFOLD_TEMPERATURE_MILLIC = 'intakeManifoldTemperatureMilliC';
    case NFC_CARD_SCANS = 'nfcCardScans';
    case OBD_ENGINE_SECONDS = 'obdEngineSeconds';
    case OBD_ODOMETER_METERS = 'obdOdometerMeters';
    case SEATBELT_DRIVER = 'seatbeltDriver';
    case SPREADER_ACTIVE = 'spreaderActive';
    case SPREADER_AIR_TEMP = 'spreaderAirTemp';
    case SPREADER_AUGER_RUNNING = 'spreaderAugerRunning';
    case SPREADER_BLAST_STATE = 'spreaderBlastState';
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
    case SPREADER_ON_STATE = 'spreaderOnState';
    case SPREADER_PLOW_STATUS = 'spreaderPlowStatus';
    case SPREADER_PREWET_BLAST_STATE = 'spreaderPrewetBlastState';
    case SPREADER_PREWET_NAME = 'spreaderPrewetName';
    case SPREADER_PREWET_RATE = 'spreaderPrewetRate';
    case SPREADER_RIGHT_SPINNER_RUNNING = 'spreaderRightSpinnerRunning';
    case SPREADER_ROAD_TEMP = 'spreaderRoadTemp';
    case SPREADER_VIBRATOR_RUNNING = 'spreaderVibratorRunning';
    case SPREADER_WORK_LIGHT = 'spreaderWorkLight';
    case SYNTHETIC_ENGINE_SECONDS = 'syntheticEngineSeconds';
    case TELL_TALES = 'tellTales';
    case TIRE_PRESSURE_MILLI_PSIS = 'tirePressureMilliPsis';
}
