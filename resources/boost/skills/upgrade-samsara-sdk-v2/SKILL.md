---
name: upgrade-samsara-sdk-v2
description: Migrate a Laravel application from erikgall/samsara v1.1 to v2, or verify that migration.
---

# Upgrade Samsara SDK to Version 2

Read the target package's `UPGRADE.md` and its complete linked guide before editing. Composer distributions omit `docs/`; use the linked repository guide when the local file is absent, checking it against the installed or target release.

In particular, `MIG-26` covers final resource/factory removals, write retries, immutable clients, fakes, cursors, webhook context, and DTO serialization. Complete every `MIG-*` section, the agent completion record, and the definition of done. Mark a section `not used` only with reproducible search evidence.

Do not change the Composer constraint before migration work is ready for verification. A used operation with no documented replacement requires an application-owner decision before implementing a raw integration.
