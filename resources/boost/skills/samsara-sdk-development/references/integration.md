# Integration and Resource Selection

## Install and Configure

For a fresh application, install the package and publish its configuration:

```bash
composer require erikgall/samsara
php artisan vendor:publish --provider="Samsara\SamsaraServiceProvider"
```

The package requires PHP 8.2 or later and Laravel 12 or 13. Configuration lives in `config/samsara.php`:

- `api_key` / `SAMSARA_API_KEY`
- `region` / `SAMSARA_REGION`: `us`, `eu`, or `ca`
- `timeout` / `SAMSARA_TIMEOUT`
- `attempts` / `SAMSARA_ATTEMPTS`
- `per_page` / `SAMSARA_PER_PAGE`
- `webhook_secret` / `SAMSARA_WEBHOOK_SECRET`

Never commit tokens or webhook secrets.

## Resolve a Client

Use the facade for ordinary application calls:

```php
use Samsara\Facades\Samsara;

$drivers = Samsara::drivers()->all();
```

Inject `Samsara\Samsara` when a service owns several calls or benefits from an explicit dependency:

```php
use Samsara\Samsara;

final class FleetService
{
    public function __construct(private Samsara $samsara) {}
}
```

Use `Samsara\Samsara::make($token, $overrides)` for an intentionally separate client, such as an application that stores a token per account. It inherits package request settings and region unless overridden. `withToken()` also returns an isolated clone; retain its return value. Do not mutate shared credentials for per-account work.

## Select an Operation

1. Locate the installed package with `composer show erikgall/samsara --path`.
2. Start with its `README.md` resource inventory.
3. Read the matching `docs/resources/*.md` page.
4. For generic CRUD, check `docs/api-sync/documentation-coverage.md` and the resource's `supportedGenericOperations()` implementation.
5. Confirm the method signature and return type in `src/Resources/` and its focused test in `tests/Unit/Resources/`.

Use the package's explicit resource helper when one exists. Use `where()` only for a documented query parameter that lacks a typed helper. Treat `client()` as an escape hatch that transfers ownership of the raw HTTP contract, error handling, and tests to the consuming application.

## Source Priority

For package behavior, prefer sources in this order:

1. Installed v2 source and tests.
2. Installed package documentation and capability matrix.
3. The pinned contract evidence shipped by the package.
4. Current upstream Samsara documentation for an upstream detail the package does not establish.

Do not infer package behavior from an upstream endpoint alone. An upstream operation can exist without an SDK wrapper.
