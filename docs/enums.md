---
title: Enums
layout: default
nav_order: 7
description: Reference for every enum class shipped with the Samsara SDK.
permalink: /enums
---

# Enums

- [Introduction](#introduction)
- [AlertType](#alerttype)
- [AssetType](#assettype)
- [DocumentType](#documenttype)
- [DriverActivationStatus](#driveractivationstatus)
- [DutyStatus](#dutystatus)
- [EngineState](#enginestate)
- [HosLogType](#hoslogtype)
- [MaintenanceStatus](#maintenancestatus)
- [RouteState](#routestate)
- [SafetyEventType](#safetyeventtype)
- [VehicleStatType](#vehiclestattype)
- [WebhookEvent](#webhookevent)

## Introduction

The SDK ships strongly-typed enums for the string values the Samsara API returns or accepts. Every enum is a string-backed `BackedEnum` whose `value` matches the wire string. You may pass enum cases anywhere the API accepts the matching string, except where noted (see the [VehicleStatType](#vehiclestattype) caveat about `Builder::types()`).

## AlertType

`Samsara\Enums\AlertType`

Categorizes the alert configurations returned by the [Alerts](resources/alerts.md) resource. Use it when you want to compare or filter the `alertType` field on an `AlertConfiguration` against a known case rather than a raw string.

| Case | Value | Description |
|------|-------|-------------|
| `BATTERY` | `battery` | Battery-state alerts. |
| `CARGO` | `cargo` | Cargo-state alerts. |
| `CUSTOM` | `custom` | Custom alert configurations defined in Samsara Cloud. |
| `DOOR` | `door` | Door open or close alerts. |
| `ENGINE_FAULT` | `engineFault` | Engine fault code alerts. |
| `FUEL` | `fuel` | Fuel-level and fuel-event alerts. |
| `GEOFENCE` | `geofence` | Geofence entry and exit alerts. |
| `HOS_VIOLATION` | `hosViolation` | Hours-of-service violation alerts. |
| `HUMIDITY` | `humidity` | Humidity-threshold sensor alerts. |
| `IDLE` | `idle` | Idling-duration alerts. |
| `MAINTENANCE` | `maintenance` | Maintenance and service alerts. |
| `PANIC` | `panic` | Panic-button alerts from the Samsara Driver app. |
| `POWER` | `power` | Gateway power alerts. |
| `SAFETY_EVENT` | `safetyEvent` | Safety-event alerts (harsh braking, distracted driving, etc.). |
| `SPEEDING` | `speeding` | Speeding alerts. |
| `TEMPERATURE` | `temperature` | Temperature-threshold sensor alerts. |

## AssetType

`Samsara\Enums\AssetType`

Identifies what kind of asset a record describes on the [Assets](resources/assets.md) resource.

| Case | Value | Description |
|------|-------|-------------|
| `CONTAINER` | `container` | A shipping container. |
| `EQUIPMENT` | `equipment` | Generic powered or unpowered equipment. |
| `GENERATOR` | `generator` | A generator asset. |
| `OTHER` | `other` | An asset that does not fit the other categories. |
| `REEFER` | `reefer` | A refrigerated trailer. |
| `TRAILER` | `trailer` | A standard trailer. |
| `UNPOWERED` | `unpowered` | An unpowered asset (no Samsara gateway). |

## DocumentType

`Samsara\Enums\DocumentType`

Classifies driver-submitted documents on the documents endpoint. Used wherever a `documentType` field is returned for a driver document or document-submission webhook.

| Case | Value | Description |
|------|-------|-------------|
| `BILL_OF_LADING` | `billOfLading` | A bill of lading. |
| `DISPATCH` | `dispatch` | A dispatch document. |
| `INSPECTION` | `inspection` | An inspection document. |
| `OTHER` | `other` | A document that does not match the other types. |
| `PHOTO` | `photo` | A photo attachment. |
| `PROOF_OF_DELIVERY` | `proofOfDelivery` | Proof of delivery. |
| `RECEIPT` | `receipt` | A receipt. |
| `SCAN` | `scan` | A scanned document. |
| `SIGNATURE` | `signature` | A captured signature. |

## DriverActivationStatus

`Samsara\Enums\DriverActivationStatus`

The activation state of a record returned by the [Drivers](resources/drivers.md) resource. Pass either case to the driver-status filter when you need to scope a query.

| Case | Value | Description |
|------|-------|-------------|
| `ACTIVE` | `active` | The driver is active. |
| `DEACTIVATED` | `deactivated` | The driver has been deactivated. |

## DutyStatus

`Samsara\Enums\DutyStatus`

The duty status reported on hours-of-service logs. See the [Hours of Service](resources/hours-of-service.md) resource.

| Case | Value | Description |
|------|-------|-------------|
| `DRIVING` | `driving` | The driver is driving. |
| `OFF_DUTY` | `offDuty` | The driver is off duty. |
| `ON_DUTY` | `onDuty` | The driver is on duty (not driving). |
| `PERSONAL_CONVEYANCE` | `personalConveyance` | The driver is using the vehicle for personal conveyance. |
| `SLEEPER_BERTH` | `sleeperBerth` | The driver is in the sleeper berth. |
| `YARD_MOVE` | `yardMove` | The driver is performing a yard move. |

## EngineState

`Samsara\Enums\EngineState`

The engine state returned by `vehicleStats()->engineStates()`. See the [Vehicle Stats](resources/vehicle-stats.md) resource. The wire values are capitalized — `Idle`, `Off`, `On` — not lowercase.

| Case | Value | Description |
|------|-------|-------------|
| `IDLE` | `Idle` | The engine is idling. |
| `OFF` | `Off` | The engine is off. |
| `ON` | `On` | The engine is running and not idling. |

## HosLogType

`Samsara\Enums\HosLogType`

The kind of hours-of-service log a record represents. See the [Hours of Service](resources/hours-of-service.md) resource.

| Case | Value | Description |
|------|-------|-------------|
| `CERTIFICATION` | `certification` | A certification record. |
| `DIAGNOSTIC_MALFUNCTION` | `diagnosticMalfunction` | A diagnostic or malfunction event. |
| `DRIVER_INDICATION` | `driverIndication` | A driver-supplied indication. |
| `DUTY_STATUS` | `dutyStatus` | A duty-status change. |
| `INTERMEDIATE` | `intermediate` | An intermediate log entry. |
| `LOGIN_LOGOUT` | `loginLogout` | A driver login or logout. |
| `POWER_UP_DOWN` | `powerUpDown` | A power-up or power-down event. |
| `REMARK` | `remark` | A driver remark. |
| `SHIPPING_DOCUMENT` | `shippingDocument` | A shipping-document entry. |

## MaintenanceStatus

`Samsara\Enums\MaintenanceStatus`

The status of a maintenance record on the [Maintenance](resources/maintenance.md) resource.

| Case | Value | Description |
|------|-------|-------------|
| `CANCELLED` | `cancelled` | The maintenance work was cancelled. |
| `COMPLETED` | `completed` | The maintenance work is complete. |
| `IN_PROGRESS` | `inProgress` | The maintenance work is in progress. |
| `OPEN` | `open` | The maintenance work has not started. |

## RouteState

`Samsara\Enums\RouteState`

The state of a route or stop returned by the [Routes](resources/routes.md) resource.

| Case | Value | Description |
|------|-------|-------------|
| `CANCELLED` | `cancelled` | The route or stop was cancelled. |
| `COMPLETED` | `completed` | The route or stop was completed. |
| `IN_PROGRESS` | `inProgress` | The route or stop is in progress. |
| `NOT_STARTED` | `notStarted` | The route or stop has not started. |
| `SKIPPED` | `skipped` | The stop was skipped. |

## SafetyEventType

`Samsara\Enums\SafetyEventType`

Identifies the kind of safety event returned by the [Safety Events](resources/safety-events.md) resource.

| Case | Value | Description |
|------|-------|-------------|
| `CAMERA_OBSTRUCTION` | `cameraObstruction` | The camera was obstructed. |
| `CELL_PHONE_USAGE` | `cellPhoneUsage` | The driver used a cell phone. |
| `CRASH` | `crash` | A crash was detected. |
| `DEFENSIVE_DRIVING` | `defensiveDriving` | A defensive-driving event. |
| `DISTRACTED_DRIVING` | `distractedDriving` | The driver was distracted. |
| `DRIVER_DETECTED` | `driverDetected` | A driver was detected in the cab. |
| `DROWSY_DRIVING` | `drowsyDriving` | The driver appeared drowsy. |
| `FOLLOWING_DISTANCE` | `followingDistance` | A following-distance event. |
| `HARSH_ACCELERATION` | `harshAcceleration` | Harsh acceleration. |
| `HARSH_BRAKING` | `harshBraking` | Harsh braking. |
| `HARSH_TURN` | `harshTurn` | A harsh turn. |
| `LANE_DEPARTURE` | `laneDeparture` | A lane-departure event. |
| `MAX_SPEED` | `maxSpeed` | A maximum-speed event. |
| `NEAR_COLLISION` | `nearCollision` | A near collision. |
| `NO_DRIVER_DETECTED` | `noDriverDetected` | No driver was detected in the cab. |
| `ROLLING_STOP` | `rollingStop` | A rolling stop. |
| `SEATBELT` | `seatbelt` | A seatbelt event. |
| `SMOKING` | `smoking` | The driver was smoking. |
| `SPEEDING` | `speeding` | A speeding event. |
| `UNKNOWN` | `unknown` | An unclassified safety event. |

## VehicleStatType

`Samsara\Enums\VehicleStatType`

The full list of telemetry channels exposed by the [Vehicle Stats](resources/vehicle-stats.md) resource.

> **Warning:** `Builder::types()` is typed as `array<string>|string` only. Do not pass enum cases to it directly — pass the wire string values shown below (for example `'gps'` or `'engineStates'`). See the [Query Builder](query-builder.md) reference for the canonical chaining pattern through `vehicleStats()->current()`, `history()`, `feed()`, `gps()`, `engineStates()`, `fuelPercents()`, or `odometer()`.

| Case | Value | Description |
|------|-------|-------------|
| `AMBIENT_AIR_TEMPERATURE_MILLIC` | `ambientAirTemperatureMilliC` | Ambient air temperature, in milli-degrees Celsius. |
| `BAROMETRIC_PRESSURE_PA` | `barometricPressurePa` | Barometric pressure, in pascals. |
| `BATTERY_MILLI_VOLTS` | `batteryMilliVolts` | Battery voltage, in millivolts. |
| `DEF_LEVEL_MILLI_PERCENT` | `defLevelMilliPercent` | Diesel exhaust fluid level, in milli-percent. |
| `ENGINE_COOLANT_TEMPERATURE_MILLIC` | `engineCoolantTemperatureMilliC` | Engine coolant temperature, in milli-degrees Celsius. |
| `ENGINE_LOAD_PERCENT` | `engineLoadPercent` | Engine load, in percent. |
| `ENGINE_OIL_PRESSURE_K_PA` | `engineOilPressureKPa` | Engine oil pressure, in kilopascals. |
| `ENGINE_RPM` | `engineRpm` | Engine RPM. |
| `ENGINE_STATES` | `engineStates` | Engine state samples. |
| `EV_BATTERY_CURRENT_MILLI_AMPS` | `evBatteryCurrentMilliAmps` | EV battery current, in milliamps. |
| `EV_BATTERY_STATE_OF_HEALTH_MILLI_PERCENT` | `evBatteryStateOfHealthMilliPercent` | EV battery state of health, in milli-percent. |
| `EV_BATTERY_TEMPERATURE_MILLIC` | `evBatteryTemperatureMilliC` | EV battery temperature, in milli-degrees Celsius. |
| `EV_BATTERY_VOLTAGE_MILLI_VOLTS` | `evBatteryVoltageMilliVolts` | EV battery voltage, in millivolts. |
| `EV_CHARGING_CURRENT_MILLI_AMPS` | `evChargingCurrentMilliAmps` | EV charging current, in milliamps. |
| `EV_CHARGING_ENERGY_MICRO_WH` | `evChargingEnergyMicroWh` | EV charging energy, in micro-watt-hours. |
| `EV_CHARGING_STATUS` | `evChargingStatus` | EV charging status. |
| `EV_CHARGING_VOLTAGE_MILLI_VOLTS` | `evChargingVoltageMilliVolts` | EV charging voltage, in millivolts. |
| `EV_CONSUMED_ENERGY_MICRO_WH` | `evConsumedEnergyMicroWh` | EV consumed energy, in micro-watt-hours. |
| `EV_DISTANCE_DRIVEN_METERS` | `evDistanceDrivenMeters` | EV distance driven, in meters. |
| `EV_REGENERATED_ENERGY_MICRO_WH` | `evRegeneratedEnergyMicroWh` | EV regenerated energy, in micro-watt-hours. |
| `EV_STATE_OF_CHARGE_MILLI_PERCENT` | `evStateOfChargeMilliPercent` | EV state of charge, in milli-percent. |
| `FUEL_PERCENTS` | `fuelPercents` | Fuel level samples, in percent. |
| `GPS` | `gps` | GPS location samples. |
| `GPS_DISTANCE_METERS` | `gpsDistanceMeters` | GPS-derived distance, in meters. |
| `GPS_ODOMETER_METERS` | `gpsOdometerMeters` | GPS-derived odometer reading, in meters. |
| `INTAKE_MANIFOLD_TEMPERATURE_MILLIC` | `intakeManifoldTemperatureMilliC` | Intake manifold temperature, in milli-degrees Celsius. |
| `OBD_ENGINE_SECONDS` | `obdEngineSeconds` | OBD engine-on seconds. |
| `OBD_ODOMETER_METERS` | `obdOdometerMeters` | OBD odometer reading, in meters. |
| `SEATBELT_DRIVER` | `seatbeltDriver` | Driver seatbelt state. |
| `SPREADER_ACTIVE` | `spreaderActive` | Spreader active flag. |
| `SPREADER_AIR_TEMP` | `spreaderAirTemp` | Spreader air temperature. |
| `SPREADER_AUGER_RUNNING` | `spreaderAugerRunning` | Spreader auger running flag. |
| `SPREADER_BLOCK_HEIGHT` | `spreaderBlockHeight` | Spreader block height. |
| `SPREADER_CONVEYOR_RUNNING` | `spreaderConveyorRunning` | Spreader conveyor running flag. |
| `SPREADER_GRANULAR_BLAST_STATE` | `spreaderGranularBlastState` | Spreader granular blast state. |
| `SPREADER_GRANULAR_NAME` | `spreaderGranularName` | Spreader granular material name. |
| `SPREADER_GRANULAR_RATE` | `spreaderGranularRate` | Spreader granular rate. |
| `SPREADER_HOPPER_PERCENT_FULL` | `spreaderHopperPercentFull` | Spreader hopper fill, in percent. |
| `SPREADER_LEFT_SPINNER_RUNNING` | `spreaderLeftSpinnerRunning` | Spreader left-spinner running flag. |
| `SPREADER_LIQUID_BLAST_STATE` | `spreaderLiquidBlastState` | Spreader liquid blast state. |
| `SPREADER_LIQUID_NAME` | `spreaderLiquidName` | Spreader liquid material name. |
| `SPREADER_LIQUID_RATE` | `spreaderLiquidRate` | Spreader liquid rate. |
| `SPREADER_ON_GROUND` | `spreaderOnGround` | Spreader on-ground flag. |
| `SPREADER_PLOW_STATUS` | `spreaderPlowStatus` | Spreader plow status. |
| `SPREADER_PREWET_BLAST_STATE` | `spreaderPrewetBlastState` | Spreader pre-wet blast state. |
| `SPREADER_PREWET_NAME` | `spreaderPrewetName` | Spreader pre-wet material name. |
| `SPREADER_PREWET_RATE` | `spreaderPrewetRate` | Spreader pre-wet rate. |
| `SPREADER_RIGHT_SPINNER_RUNNING` | `spreaderRightSpinnerRunning` | Spreader right-spinner running flag. |
| `SPREADER_ROAD_TEMP` | `spreaderRoadTemp` | Spreader road temperature. |
| `SPREADER_VIBRATOR_RUNNING` | `spreaderVibratorRunning` | Spreader vibrator running flag. |
| `SPREADER_WORK_LIGHT` | `spreaderWorkLight` | Spreader work-light flag. |
| `TIRE_PRESSURE_MILLI_PSIS` | `tirePressureMilliPsis` | Tire pressure, in milli-PSI. |

## WebhookEvent

`Samsara\Enums\WebhookEvent`

The valid event types for Samsara webhook subscriptions. See the [Webhooks](resources/webhooks.md) resource. The enum exposes three helpers: `WebhookEvent::all()` returns every wire value as an `array<int, string>`, `WebhookEvent::collect()` returns a Laravel `Collection` of cases, and `WebhookEvent::values()` returns a `Collection` of wire values.

| Case | Value | Description |
|------|-------|-------------|
| `ADDRESS_CREATED` | `AddressCreated` | An address was created. |
| `ADDRESS_DELETED` | `AddressDeleted` | An address was deleted. |
| `ADDRESS_UPDATED` | `AddressUpdated` | An address was updated. |
| `DOCUMENT_SUBMITTED` | `DocumentSubmitted` | A driver submitted a document. |
| `DRIVER_CREATED` | `DriverCreated` | A driver was created. |
| `DRIVER_UPDATED` | `DriverUpdated` | A driver was updated. |
| `DVIR_SUBMITTED` | `DvirSubmitted` | A DVIR was submitted. |
| `ENGINE_FAULT_OFF` | `EngineFaultOff` | An engine fault cleared. |
| `ENGINE_FAULT_ON` | `EngineFaultOn` | An engine fault triggered. |
| `FORM_SUBMITTED` | `FormSubmitted` | A form was submitted. |
| `FORM_UPDATED` | `FormUpdated` | A form was updated. |
| `GATEWAY_UNPLUGGED` | `GatewayUnplugged` | A gateway was unplugged. |
| `GEOFENCE_ENTRY` | `GeofenceEntry` | A geofence was entered. |
| `GEOFENCE_EXIT` | `GeofenceExit` | A geofence was exited. |
| `ISSUE_CREATED` | `IssueCreated` | An issue was created. |
| `PREDICTIVE_MAINTENANCE_ALERT` | `PredictiveMaintenanceAlert` | A predictive-maintenance alert fired. |
| `ROUTE_STOP_ARRIVAL` | `RouteStopArrival` | A driver arrived at a route stop. |
| `ROUTE_STOP_DEPARTURE` | `RouteStopDeparture` | A driver departed a route stop. |
| `ROUTE_STOP_EARLY_LATE_ARRIVAL` | `RouteStopEarlyLateArrival` | A driver arrived early or late. |
| `ROUTE_STOP_ETA_UPDATED` | `RouteStopEtaUpdated` | A route-stop ETA was updated. |
| `ROUTE_STOP_RESEQUENCE` | `RouteStopResequence` | A route-stop sequence was changed. |
| `SEVERE_SPEEDING_ENDED` | `SevereSpeedingEnded` | A severe-speeding event ended. |
| `SEVERE_SPEEDING_STARTED` | `SevereSpeedingStarted` | A severe-speeding event started. |
| `SPEEDING_EVENT_ENDED` | `SpeedingEventEnded` | A speeding event ended. |
| `SPEEDING_EVENT_STARTED` | `SpeedingEventStarted` | A speeding event started. |
| `VEHICLE_CREATED` | `VehicleCreated` | A vehicle was created. |
| `VEHICLE_UPDATED` | `VehicleUpdated` | A vehicle was updated. |
