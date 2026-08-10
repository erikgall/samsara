---
title: Preview Resources
nav_order: 91
description: Preview Samsara endpoints surfaced through the SDK.
permalink: /resources/preview
---

# Preview Resources

> **Warning:** Samsara's current first-party policy says preview endpoints are **not functional** and must not support business-critical applications. The package implements the documented preview paths and verbs for contract-stage testing only.

- [Introduction](#introduction)
- [Method Reference](#method-reference)
- [Related Resources](#related-resources)

## Introduction

`Samsara::preview()` returns a `PreviewResource`. No generic CRUD operation is supported. The driver-token helper returns the raw payload; vehicle lock and unlock return success booleans.

## Method Reference

### `createDriverAuthToken(array $data): array`

Sends the payload to `POST /preview/fleet/drivers/create-auth-token` and returns the decoded data payload.

### `lockVehicle(string $vehicleId): bool`

Sends `PUT /preview/fleet/vehicles/{vehicleId}/lock` and returns success.

### `unlockVehicle(string $vehicleId): bool`

Sends `DELETE /preview/fleet/vehicles/{vehicleId}/lock` and returns success.

## Related Resources

- [Resources Index](index.html) — full list of stable resources.
- [Beta Resources](beta.html) — staged endpoints, including `vehicleImmobilizerStream()`.
- [Drivers](drivers.html) — manage the drivers referenced by `createDriverAuthToken()`.
- [Vehicles](vehicles.html) — manage the vehicles referenced by `lockVehicle()` and `unlockVehicle()`.
