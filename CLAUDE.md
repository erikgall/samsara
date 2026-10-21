# Samsara SDK

For package-internal work, read `.ai/skills/samsara-sdk-maintenance/SKILL.md` first.

- Follow TDD. Use `#[Test]` and name tests `it_does_something()`.
- Unit tests touch no database. Fake every HTTP call with `HttpFactory::fake()`.
- Prefer first-party Laravel packages and small value objects over raw arrays.
- Type every parameter and every method return.
- No `declare(strict_types=1)` and no `else` (return early). Nest at most two levels.
- Use `match` for value mapping and `??` for defaults.
- Every class PHPDoc carries `@author Erik Galloway <erik@erikgall.com>`.
- Give new local variables specific names, not `$data`, `$result` or `$temp`. Never rename an existing public parameter; callers pass them by name.
- Make small, incremental commits. Run `./vendor/bin/pint --dirty` before each one.
- Keep `resources/boost/guidelines/` concise; detailed agent procedures belong in `resources/boost/skills/`.
