# Samsara ELD Laravel SDK

Laravel SDK for the Samsara Fleet Management API.

## Workflow

- Follow TDD (Red -> Green -> Refactor)
- Keep `PLAN.md` and `TODO.md` updated
- Complete todo items fully before moving on
- Make small incremental commits
- Run `./vendor/bin/pint --dirty` before committing
- Ask clarifying questions interactively (use AskUserQuestion tool)

## Core Rules

- Use first-party Laravel packages
- Small objects/value objects over raw arrays
- All methods MUST have return types
- All parameters MUST have type hints
- No `declare(strict_types=1);`
- No `else` statements (use early returns)
- No nesting > 2 levels deep
- Use match expressions for value mapping
- Use null coalescing for defaults
- Mock all external HTTP calls in tests
- Author tag required in class PHPDoc

## SDK Development

Use the `samsara-sdk-development` skill for detailed patterns on:
- Creating resources, entities, and enums
- Query builder methods
- Exception handling
- Testing with HttpFactory
- Code style reference

## Testing

- Use `#[Test]` attribute
- Name tests: `it_{does_something}()`
- Test happy path, errors, and edge cases
- Unit tests: no database, mock HTTP
- Use `HttpFactory::fake()` for mocking

## Anti-Patterns

- Generic variable names (`$data`, `$result`, `$temp`)
- Missing return types or type hints
- Hardcoded credentials (use config)
- Unmocked HTTP calls in tests
- Omitting author tags in PHPDoc
