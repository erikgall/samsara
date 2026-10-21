---
name: upgrade-samsara-sdk-v2
description: Use this skill when upgrading a Laravel application from erikgall/samsara v1.1 to v2, assessing v2 compatibility, fixing v2 breaking changes, or verifying a completed Samsara SDK v2 migration. It covers capability-gated CRUD, builders, exceptions, pagination, query encoding, DTOs, enums, resource methods, HTTP paths, fakes, and the required migration evidence. Do not use it for routine work in an application already on v2.
---

# Upgrade Samsara SDK to Version 2

Treat the installed package's complete upgrade guide as the migration authority. This skill routes the work; it does not replace or abbreviate the guide.

## Required Workflow

1. Confirm the current constraint, installed version, and target package root:

    ```bash
    composer show erikgall/samsara
    composer show erikgall/samsara --path
    composer why-not erikgall/samsara ^2.0
    ```

2. From the package root, read `UPGRADE.md`, then read the linked canonical upgrade guide completely before editing. Composer distributions exclude `docs/`; follow the GitHub link in `UPGRADE.md` when the local guide is absent.
3. Inventory every Samsara import, facade or client call, resource extension, query-builder extension, caught exception, DTO access, serialized field, stored enum value, HTTP fake, fixture, cassette, cache key, signature, proxy rule, and outbound allowlist in the consuming repository.
4. Follow every `MIG-*` section in order. Record `not used` only when a reproducible search found zero relevant occurrences.
5. Add or update a focused HTTP-fake test for every Samsara operation the application uses.
6. Run the consuming application's targeted tests, static analysis, full test suite, and deployment-relevant verification.
7. Complete the guide's agent completion record and definition of done. Do not claim completion with blank evidence.

## High-Risk Version 2 Changes

- Generic `all()`, `find()`, `create()`, `update()`, and `delete()` calls are capability-gated per resource.
- Custom `QueryBuilderInterface`, `Builder`, and `Resource` extensions must match the new public and final method contracts.
- SDK operations map failures to `Samsara\Exceptions`; existing Laravel HTTP exception catches may no longer own the failure.
- Pagination can apply configured `per_page`, while operation-specific helpers suppress a limit where upstream does not allow one.
- Dedicated list filters, repeated query parameters, and arbitrary `where()` arrays have different wire encodings.
- Several operations now return endpoint-specific DTOs or wrappers; some helpers were removed and dynamic fields remain payload-backed.
- `DutyStatus::SLEEPER_BERTH` uses `sleeperBed` in version 2.
- Driver, alert, camera media, forms, fleet lookup, safety, assignment, maintenance, industrial, beta, and preview helpers include method or path changes.
- `Samsara::fake()` now installs all relevant facade and container bindings.

The complete guide contains exact before/after code, searches, path tables, fixture updates, rollback steps, and verification requirements. Do not invent replacements from this summary.

## Boundaries

- Do not change the Composer constraint before the application's migration work is ready for verification.
- Do not bypass an unsupported operation with a raw request unless the application deliberately owns and tests the upstream contract.
- Do not modify unrelated application architecture during the upgrade.
- Stop and ask when a used operation has no documented v2 replacement and application ownership of a raw integration is a product decision.
