# Upgrading Samsara SDK

Version 2 is a contract-alignment release. It changes public method signatures, generic resource capabilities, DTO classes and payload fields, exception behavior, pagination defaults, query encoding, and several HTTP paths.

Do not upgrade a production application by changing only the Composer constraint.

Use the canonical [v1.1.0 to v2.0.0 upgrade guide](https://github.com/erikgall/samsara/blob/dev/docs/upgrade-guide-v2.md). It contains:

- ordered, mandatory migration steps;
- before-and-after PHP for every developer-facing break;
- exact searches for removed calls, fields, values, and paths;
- HTTP-fake, cache, proxy, serializer, and persistence checks;
- an agent completion record and release checklist; and
- a [machine-checkable disposition ledger](https://github.com/erikgall/samsara/blob/dev/docs/api-sync/v2-upgrade-disposition.json) covering the frozen 239-file implementation baseline plus all integrated quality, test, documentation, and publication paths.

`BREAKING_CHANGES.md` is the compact release record. The upgrade guide is the migration authority.

## Maintainer integration status

The parallel quality lanes have been integrated and classified. The ledger retains its frozen implementation comparison at `1750395c98c1724c40c82ba82b96b4866dd2106b`, then derives the full release scope from the live tag-to-worktree diff. Present records store the classified file's SHA-256 content identity, deletions carry an explicit deleted state, and only the self-referential ledger is excluded. A future added, edited, deleted, or restored path makes the maintenance check stale until its migration or no-action disposition is reviewed and regenerated.
