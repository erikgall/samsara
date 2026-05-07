---
title: Resources
nav_order: 8
has_children: true
description: Reference for every resource exposed by the Samsara facade.
permalink: /resources/
---

# Resources

## Introduction

Samsara organizes its API around resources — drivers, vehicles, trips, and so on. Reach for the page that matches the data you need: each one shows how to retrieve, filter, and inspect that resource through the `Samsara` facade. For the typed string values you will pass to filters and create payloads (event names, stat types, duty statuses), see the [Enums](../enums.md) reference. For the fluent API the resource pages chain against, see the [Query Builder](../query-builder.md).

## Fleet Resources

| Resource | Description |
|----------|-------------|
| [Drivers](drivers.md) | Manage drivers, activation status, and driver data. |
| [Vehicles](vehicles.md) | Manage vehicles and vehicle information. |
| [Trailers](trailers.md) | Manage trailers and trailer tracking. |
| [Equipment](equipment.md) | Manage equipment assets. |

## Telematics Resources

| Resource | Description |
|----------|-------------|
| [Vehicle Stats](vehicle-stats.md) | Vehicle telemetry — GPS, fuel, engine state, odometer. |
| [Vehicle Locations](vehicle-locations.md) | Current and historical vehicle locations. |
| [Trips](trips.md) | Engine-on/engine-off trip records. |

## Safety Resources

| Resource | Description |
|----------|-------------|
| [Hours of Service](hours-of-service.md) | HOS logs, clocks, and violations. |
| [Maintenance](maintenance.md) | DVIRs and defects. |
| [Safety Events](safety-events.md) | Safety event tracking and audit logs. |

## Dispatch Resources

| Resource | Description |
|----------|-------------|
| [Routes](routes.md) | Dispatch routes. |
| [Addresses](addresses.md) | Addresses and geofences. |
| [Route Events](route-events.md) | Route event data. |

## Organization Resources

| Resource | Description |
|----------|-------------|
| [Users](users.md) | Dashboard users and access. |
| [Tags](tags.md) | Tags for grouping resources. |
| [Contacts](contacts.md) | Organization contacts. |

## Integration Resources

| Resource | Description |
|----------|-------------|
| [Webhooks](webhooks.md) | Webhook configurations and signature verification. |
| [Gateways](gateways.md) | Gateway device information. |
| [Live Shares](live-shares.md) | Live-sharing links. |

## Industrial Resources

| Resource | Description |
|----------|-------------|
| [Assets](assets.md) | Fleet assets with tracking. |
| [Industrial](industrial.md) | Industrial assets and data inputs. |
| [Sensors](sensors.md) | Legacy sensor data (v1 API). |

## Additional Resources

| Resource | Description |
|----------|-------------|
| [Alerts](alerts.md) | Alert configurations and incidents. |
| [Forms](forms.md) | Form submissions and templates. |
| [IFTA](ifta.md) | IFTA reporting for tax compliance. |
| [Idling](idling.md) | Vehicle idling events. |
| [Work Orders](work-orders.md) | Maintenance work orders. |
| [Camera Media](camera-media.md) | Camera footage retrieval. |
| [Speeding](speeding.md) | Speeding interval data. |
| [Tachograph](tachograph.md) | Tachograph data (EU only). |
| [Fuel and Energy](fuel-and-energy.md) | Fuel and energy efficiency data. |
| [Issues](issues.md) | Vehicle and equipment issues. |
| [Hubs](hubs.md) | Hub management. |
| [Settings](settings.md) | Organization settings. |
| [Assignments](assignments.md) | Driver, vehicle, and trailer assignments. |

## Beta & Legacy

The SDK exposes Samsara's beta, preview, and legacy endpoints through dedicated resources. Method signatures and response shapes on these resources are not covered by the same stability guarantees as the resources above.

| Resource | Description |
|----------|-------------|
| [Beta](beta.md) | Beta endpoints — AEMP, industrial jobs, qualifications, readings, reports, safety scores, trailer stats, training. |
| [Preview](preview.md) | Preview endpoints — early-access features that may change before general availability. |
| [Legacy](legacy.md) | Legacy v1 endpoints not yet migrated to v2; each method returns the raw v1 response array. |
| [Carrier Proposed Assignments](carrier-proposed-assignments.md) | Carrier-proposed driver assignments. |
