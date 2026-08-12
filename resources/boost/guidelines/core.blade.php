## Samsara SDK

`erikgall/samsara` is a Laravel SDK for selected Samsara Fleet Management API operations. Treat the installed package version and its local documentation as the contract authority; version 2 intentionally rejects operations the SDK does not support.

- Activate `samsara-sdk-development` for Samsara integration, resource, query, response, error-handling, testing, or webhook work.
- Activate `upgrade-samsara-sdk-v2` for migrations from version 1.1 to version 2.
- Prefer the `Samsara` facade or injected client plus the SDK's resource and query-builder methods. Do not invent helpers or bypass `UnsupportedOperationException` with an undocumented raw request.
- Confirm each operation and return shape in the installed package before coding. Locate it with `composer show erikgall/samsara --path`.
