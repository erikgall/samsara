# Samsara SDK Implementation - Ralph Loop Prompt

## Mission
Implement the complete Samsara SDK rewrite following the architecture in `PLAN.md` and tracking all progress in `TODO.md`.

---

## Critical Rules (MUST FOLLOW)

### 1. Reference Files - Read These First
- **`PLAN.md`** - Architecture, design patterns, code examples
- **`TODO.md`** - Task checklist with all phases and tasks
- **`samsara.json`** - OpenAPI spec for endpoint/schema reference

### 2. Test-Driven Development (TDD) - MANDATORY
For EVERY task, follow this exact sequence:

```
1. WRITE TEST FIRST
   - Create test file if needed
   - Write failing test(s) for the task

2. RUN TEST - VERIFY IT FAILS (RED)
   ./vendor/bin/phpunit <test-file>

3. IMPLEMENT - Minimal code to pass
   - Write only enough code to make test pass
   - Follow patterns from PLAN.md

4. RUN TEST - VERIFY IT PASSES (GREEN)
   ./vendor/bin/phpunit <test-file>

5. REFACTOR if needed
   - Keep tests green
   - Apply code standards
```

### 3. After EACH Completed Task - MANDATORY
```bash
# 1. Run full test suite
./vendor/bin/phpunit

# 2. Run static analysis
./vendor/bin/phpstan analyse

# 3. Fix code style
./vendor/bin/pint

# 4. Commit with descriptive message
git add -A
git commit -m "<type>(<scope>): <description>"

# 5. UPDATE TODO.md - Mark task complete
# Change [ ] to [x] for the completed task
```

### 4. Commit Message Format
```
<type>(<scope>): <short description>

<optional body with details>

Phase: X, Task: Y.Z
```

**Types:** `feat`, `test`, `refactor`, `fix`, `docs`, `chore`

**Examples:**
- `feat(client): add Samsara main client class`
- `test(query): add Builder filter method tests`
- `feat(entities): add Driver entity with helper methods`

### 5. TODO.md Progress Tracking
After completing each task:
1. Open `TODO.md`
2. Find the task you just completed
3. Change `- [ ]` to `- [x]`
4. Save the file
5. Include in your commit

---

## Code Standards

### PHP Requirements
- PHP 8.1+ with `declare(strict_types=1);`
- Methods returning `$this` MUST use `static` return type (not `self`)
- DTOs MUST extend `Illuminate\Support\Fluent`
- Use Laravel HTTP Client (`Illuminate\Http\Client`)
- No `readonly` or `final` classes
- Complete PHPDoc on all public methods

### Example Patterns (from PLAN.md)

**Entity (extends Fluent):**
```php
<?php

declare(strict_types=1);

namespace ErikGall\Samsara\Data\Driver;

use ErikGall\Samsara\Data\Entity;

class Driver extends Entity
{
    public function isActive(): bool
    {
        return $this->driverActivationStatus === 'active';
    }
}
```

**Fluent method with static return:**
```php
public function whereTag(array|string $tagIds): static
{
    $this->filters['tagIds'] = is_array($tagIds)
        ? implode(',', $tagIds)
        : $tagIds;

    return $this;
}
```

---

## Workflow

### Starting a Session
1. Read `TODO.md` to find current phase and next incomplete task `[ ]`
2. Read relevant section in `PLAN.md` for context
3. Begin TDD workflow for that task

### Completing a Phase
Before moving to next phase, verify:
- [ ] All phase tasks marked `[x]` in `TODO.md`
- [ ] All tests passing: `./vendor/bin/phpunit`
- [ ] Static analysis clean: `./vendor/bin/phpstan analyse`
- [ ] Code style clean: `./vendor/bin/pint`
- [ ] All changes committed

### If Tests Fail
1. Do NOT move to next task
2. Fix the failing test
3. Re-run test suite
4. Only proceed when green

---

## Phase Overview

| Phase | Focus | Key Files |
|-------|-------|-----------|
| 1 | Core Infrastructure | Samsara.php, config, ServiceProvider, Facade |
| 2 | Exceptions | src/Exceptions/*.php |
| 3 | Base Components | Entity, EntityCollection, Resource |
| 4 | Query Builder | src/Query/Builder.php, Pagination |
| 5 | Enums | src/Enums/*.php |
| 6 | Entities | src/Data/**/*.php |
| 7 | Resources | src/Resources/**/*.php |
| 8 | Beta/Preview/Legacy | Beta, Preview, Legacy resources |
| 9 | Testing | SamsaraFake, fixtures |
| 10 | Refactoring & QA | Code review, static analysis |
| 11 | Documentation | README, docs, Laravel Boost guidelines |
| 12 | Release | Cleanup, versioning, publish |

---

## Quick Reference Commands

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test file
./vendor/bin/phpunit tests/Unit/SamsaraTest.php

# Run tests in directory
./vendor/bin/phpunit tests/Unit/Query/

# Static analysis
./vendor/bin/phpstan analyse

# Code style fix
./vendor/bin/pint

# Check current git status
git status

# Commit changes
git add -A && git commit -m "feat(scope): description"
```

---

## Begin

1. **Read `TODO.md`** - Find the first incomplete task `[ ]`
2. **Read `PLAN.md`** - Understand the architecture for that task
3. **Write test first** - TDD is mandatory
4. **Implement** - Make test pass
5. **Commit** - With descriptive message
6. **Update `TODO.md`** - Mark task `[x]`
7. **Repeat** - Next task

Start now by reading TODO.md to find the current phase and next task.
