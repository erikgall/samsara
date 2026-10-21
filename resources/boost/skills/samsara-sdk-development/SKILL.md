---
name: samsara-sdk-development
description: Build or change a Laravel application's integration with erikgall/samsara v2, including resources, queries, pagination, errors, fakes, and webhooks. For v1.1 to v2 migrations, use upgrade-samsara-sdk-v2.
---

# Samsara SDK Development

Find the installed version and root with `composer show erikgall/samsara --path`. Its `src/`, `config/samsara.php` and `README.md` define shipped behavior. Composer distributions omit `docs/`, `tests/` and `contracts/`; read those from the matching release tag in the package repository.

- `get()` and `paginate()` read one page. `lazy()` follows cursors automatically; `nextPage()` fetches one subsequent page.
- A supported `find()` returns `null` on 404 instead of throwing.
- Transport failures throw `Illuminate\Http\Client\ConnectionException`. `Samsara\Exceptions\ConnectionException` exists but is never thrown.
- Protect webhook routes with `->middleware(VerifyWebhookSignature::class)` or `->middleware('samsara.webhook:{secret},{tolerance}')`. Passing an instance fails at route registration.
