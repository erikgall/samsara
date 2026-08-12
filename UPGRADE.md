# Upgrading Samsara SDK

Version 2 is a contract-alignment release. It changes public method signatures, generic resource capabilities, DTO classes and payload fields, exception behavior, pagination defaults, query encoding, and several HTTP paths.

Do not upgrade a production application by changing only the Composer constraint.

Use the canonical [v1.1.0 to v2.0.0 upgrade guide](docs/upgrade-guide-v2.md). It contains:

- ordered, mandatory migration steps;
- before-and-after PHP for every developer-facing break;
- exact searches for removed calls, fields, values, and paths;
- HTTP-fake, cache, proxy, serializer, and persistence checks;
- an agent completion record and release checklist; and
- a [machine-checkable 337-file disposition ledger](docs/api-sync/v2-upgrade-disposition.json) covering the frozen 239-file implementation baseline plus all integrated quality, test, documentation, and publication paths.

`BREAKING_CHANGES.md` is the compact release record. The upgrade guide is the migration authority.

## Maintainer integration status

The parallel quality lanes have been integrated and classified. The ledger retains its frozen implementation comparison at `1750395c98c1724c40c82ba82b96b4866dd2106b`, then derives the full 337-path release scope from the live tag-to-worktree diff. A future changed path makes the checked ledger stale until its migration or no-action disposition is recorded.
