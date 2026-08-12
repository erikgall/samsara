# Upgrading Samsara SDK

Version 2 is a contract-alignment release. It changes public method signatures, generic resource capabilities, DTO classes and payload fields, exception behavior, pagination defaults, query encoding, and several HTTP paths.

Do not upgrade a production application by changing only the Composer constraint.

Use the canonical [v1.1.0 to v2.0.0 upgrade guide](docs/upgrade-guide-v2.md). It contains:

- ordered, mandatory migration steps;
- before-and-after PHP for every developer-facing break;
- exact searches for removed calls, fields, values, and paths;
- HTTP-fake, cache, proxy, serializer, and persistence checks;
- an agent completion record and release checklist; and
- a [machine-checkable 257-file disposition ledger](docs/api-sync/v2-upgrade-disposition.json) covering the frozen 239-file implementation baseline plus all 18 final publication artifacts, including the Laravel Boost guideline and skill overhaul.

`BREAKING_CHANGES.md` is the compact release record. The upgrade guide is the migration authority.
