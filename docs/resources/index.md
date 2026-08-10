---
title: Resources
nav_order: 8
has_children: true
description: Reference for every resource exposed by the Samsara facade.
permalink: /resources/
---

# Resources

## Introduction

The facade exposes 41 resource accessors, covered by the pages below. Generic operations are capability-gated per resource; see the [Documentation Coverage](../api-sync/documentation-coverage.html) matrix for exact support. For package enums, see [Enums](../enums.html). For the concrete fluent API, see [Query Builder](../query-builder.html).

## Fleet Resources

| Resource | Description |
|----------|-------------|
| [Drivers](drivers.html) | Manage drivers, activation status, and driver data. |
| [Vehicles](vehicles.html) | Manage vehicles and vehicle information. |
| [Trailers](trailers.html) | Manage trailers and trailer tracking. |
| [Equipment](equipment.html) | Read equipment assets, locations, and stats. |

## Telematics Resources

| Resource | Description |
|----------|-------------|
| [Vehicle Stats](vehicle-stats.html) | Vehicle telemetry — GPS, fuel, engine state, odometer. |
| [Vehicle Locations](vehicle-locations.html) | Current and historical vehicle locations. |
| [Trips](trips.html) | Engine-on/engine-off trip records. |

## Safety Resources

| Resource | Description |
|----------|-------------|
| [Hours of Service](hours-of-service.html) | HOS logs, clocks, and violations. |
| [Maintenance](maintenance.html) | DVIRs and defects. |
| [Safety Events](safety-events.html) | Safety event tracking and audit logs. |

## Dispatch Resources

| Resource | Description |
|----------|-------------|
| [Routes](routes.html) | Dispatch routes. |
| [Addresses](addresses.html) | Addresses and geofences. |
| [Route Events](route-events.html) | Route event data. |

## Organization Resources

| Resource | Description |
|----------|-------------|
| [Users](users.html) | Dashboard users and access. |
| [Tags](tags.html) | Tags for grouping resources. |
| [Contacts](contacts.html) | Organization contacts. |

## Integration Resources

| Resource | Description |
|----------|-------------|
| [Webhooks](webhooks.html) | Webhook configurations and signature verification. |
| [Gateways](gateways.html) | Gateway device information. |
| [Live Shares](live-shares.html) | Live-sharing links. |

## Industrial Resources

| Resource | Description |
|----------|-------------|
| [Assets](assets.html) | Fleet assets with tracking. |
| [Industrial](industrial.html) | Industrial assets and data inputs. |
| [Sensors](sensors.html) | Legacy sensor data (v1 API). |

## Additional Resources

| Resource | Description |
|----------|-------------|
| [Alerts](alerts.html) | Alert configurations and incidents. |
| [Forms](forms.html) | Form submissions and templates. |
| [IFTA](ifta.html) | IFTA reporting for tax compliance. |
| [Idling](idling.html) | Vehicle idling events. |
| [Work Orders](work-orders.html) | Maintenance work orders. |
| [Camera Media](camera-media.html) | Camera footage retrieval. |
| [Speeding](speeding.html) | Speeding interval data. |
| [Tachograph](tachograph.html) | Tachograph data (EU only). |
| [Fuel and Energy](fuel-and-energy.html) | Fuel and energy efficiency data. |
| [Issues](issues.html) | Vehicle and equipment issues. |
| [Hubs](hubs.html) | Read organization hubs. |
| [Settings](settings.html) | Organization settings. |
| [Assignments](assignments.html) | Driver, vehicle, and trailer assignments. |

## Beta & Legacy

The SDK has resources named for beta, preview, and legacy surfaces. `BetaResource` mixes implemented GA and beta operations, and Samsara says preview endpoints are not functional. Check each page before use.

| Resource | Description |
|----------|-------------|
| [Beta](beta.html) | Mixed implemented GA and beta helpers under one package resource. |
| [Preview](preview.html) | Contract-aligned helpers for the non-functional preview surface. |
| [Legacy](legacy.html) | Legacy v1 endpoints not yet migrated to v2; each method returns the raw v1 response array. |
| [Carrier Proposed Assignments](carrier-proposed-assignments.html) | Carrier-proposed driver assignments. |
