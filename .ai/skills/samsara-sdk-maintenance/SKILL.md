---
name: samsara-sdk-maintenance
description: Maintain the erikgall/samsara package itself, including resources, DTOs, query builders, exceptions, webhooks, fakes, contract evidence, and package or Boost docs. For integrating the released SDK into an application, use samsara-sdk-development.
---

# Samsara SDK Maintenance

- Contract authority: `contracts/samsara/samsara-api-2026-08-10.json` and `docs/api-sync/`. Root `samsara.json` is a historical baseline only.
- A public behavior change also updates its `docs/resources/*.md` page, `docs/api-sync/documentation-coverage.md`, the v2 upgrade-guide disposition, and any affected `resources/boost/` file.
- Check every documented request against the pinned contract's required fields and types. `HttpFactory::fake()` accepts any payload, so a passing fake does not validate the request shape.
- Documented signatures use the source's exact parameter names; callers use them as named arguments.
- `Route::middleware()` stores middleware entries as strings. Document `VerifyWebhookSignature::class` or `'samsara.webhook:{secret},{tolerance}'`, never an instance.
- Derive counts such as enum cases and webhook events from source instead of hard-coding them in prose.
- Editing a file listed in `docs/api-sync/v2-upgrade-disposition.json` stales its hash. Run `composer check:v2-upgrade-guide -- --write` and review the diff.
- Run `composer check` before calling package work done. After agent or contract doc edits, also run `npm run format:check`.
