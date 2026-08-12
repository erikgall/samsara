---
name: samsara-sdk-maintenance
description: Maintain the erikgall/samsara package itself against its current v2 contracts. Use when adding or changing SDK resources, DTOs, query builders, pagination, exceptions, webhook support, fakes, fixtures, contract evidence, or package documentation. Do not use for integrating the released SDK into a consuming Laravel application; use the published samsara-sdk-development skill for that work.
---

# Samsara SDK Maintenance

Keep the v2 SDK aligned with its pinned contract, explicit resource capabilities, runtime behavior, tests, and consumer documentation.

## Workflow

1. Confirm the current branch, diff, package constraints, and pinned contract evidence before editing.
2. Trace the existing owner from `src/Samsara.php` through the resource, builder, DTO or raw result, exception boundary, and focused tests.
3. Verify upstream claims against `contracts/samsara/samsara-api-2026-08-10.json` and the readable ledgers in `docs/api-sync/`. Do not infer an operation from a method name.
4. Make the smallest coherent contract change. Preserve generic capability gates, exact HTTP method and path, query encoding, response unwrap, mapped entity, return type, and error behavior.
5. Add or update focused HTTP-fake tests before changing broader documentation.
6. Update every directly affected consumer page, capability matrix entry, v2 upgrade disposition, and public agent artifact.
7. Run the focused test, full PHPUnit suite, PHPStan, Pint, and relevant maintenance checkers.

## Package Contracts

- `Resource::supportedGenericOperations()` is the authority for inherited CRUD.
- Builder terminals share the SDK request executor and mapped exception boundary.
- Typed DTOs must match the endpoint wrapper and payload; do not add convenience helpers without contract evidence and tests.
- Dedicated comma-separated filters, declared repeated parameters, and arbitrary Laravel query arrays are distinct encodings.
- `get()`, `paginate()`, `lazy()`, explicit helpers, and generic operations may intentionally return different shapes.
- Package docs and Boost skills must describe shipped behavior only. Keep the always-loaded guideline small and move detailed procedures into skills or references.

For published consumer guidance, edit `resources/boost/guidelines/` and `resources/boost/skills/`; those are Laravel Boost's third-party package discovery paths.
