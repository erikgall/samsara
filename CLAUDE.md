# Samsara ELD Laravel SDK - AI Agent Instructions

## Workflow & Quick Guide

- Follow test driven development
- Always keep plan `PLAN.md` file updated and document and changes to the plan or new requirements that were discovered
- Always keep `TODO.md` updated
- Never stop a todo item half way. Complete the implementation.
- Make small incremental commits if possible.
- Small objects or value objects are better than raw arrays
- Use common software design patterns to solve common problems
- Test your code
- Make sure all tests pass before marking your task as complete
- Always run pint code fixer `./vendor/bin/pint --dirty` before committing
- Always prefer first-party Laravel packages over others
- Install libraries as needed but if it is simple to do yourself just do not reinvent the wheel
- ASK CLARIFYING QUESTIONS IN AN INTERACTIVE WAY. DO NOT MAKE ASSUMPTIONS.
- DO NOT USE `declare(strict_types=1);`

## Code Quality Standards

### Control Flow

**✅ ALWAYS:**

1. **Use early returns** instead of else statements

```php
// GOOD
if (! $condition) {
    return;
}
// Continue with main logic

// BAD - Don't do this
if ($condition) {
    // logic
} else {
    return;
}
```

2. **Use guard clauses** for validation

```php
if (! $this->client->isAuthenticated()) {
    throw new AuthenticationException('ELD API client not authenticated');
}

return $this->client->request($endpoint);
```

3. **Use match expressions** (PHP 8+) for value returns

```php
$status = match ($eldResponse['status']) {
    'ON_DUTY'     => DriverStatus::ON_DUTY,
    'OFF_DUTY'    => DriverStatus::OFF_DUTY,
    'DRIVING'     => DriverStatus::DRIVING,
    default       => DriverStatus::UNKNOWN,
};
```

4. **Use ternary operators** for simple conditional assignments

```php
return $this->timezone ? $date->setTimezone('UTC') : $date;
```

5. **Use null coalescing** for defaults

```php
$apiUrl = $config['api_url'] ?? $this->defaultApiUrl;
```

**❌ NEVER:**
- Don't use `else` statements - refactor to early returns
- Don't nest `if` statements more than 2 levels deep
- Don't use `if/else` for simple value assignment - use ternary or match

---

### Naming Conventions

**Variables:**
- **MUST** use camelCase: `$driverLogs`, `$hosStatus`, `$vehicleData`

**Methods:**

```php
// Boolean Checkers
public function isConnected(): bool
public function hasValidCredentials(): bool
public function canFetchLogs(): bool

// Getters
public function getDriverLogs(): array
public function getHosData(): HosData

// Setters (fluent)
public function setApiKey(string $apiKey): self
public function setTimeout(int $seconds): self

// Actions
public function connect(): void
public function fetchLogs(): LogCollection
public function syncData(): bool
```

**Classes:**
- Clients: `{Provider}Client` (e.g., `SamsaraClient`)
- DTOs: `{Domain}Data` (e.g., `HosData`, `DriverLogData`)
- Exceptions: `{Domain}Exception` (e.g., `ApiConnectionException`)
- Resources: `{Entity}Resource` (e.g., `DriverResource`)

**Constants & Backed Enums Keys:**
- **MUST** use SCREAMING_SNAKE_CASE

```php
public const DEFAULT_TIMEOUT = 30;
public const API_VERSION = 'v1';
```

---

### Type Declarations - MANDATORY

**✅ REQUIRED:**

1. **Always declare return types** on ALL methods (no exceptions)

```php
public function getDriverLogs(): array
public function isConnected(): bool
public function connect(): void
protected function parseResponse(array $data): HosData
```

2. **Always use type hints** for parameters

```php
public function setTimeout(int $seconds): self
public function fetchLogs(string $driverId, Carbon $startDate): LogCollection
```

3. **Use PHP 8+ constructor property promotion**

```php
public function __construct(
    public string $apiKey,
    protected HttpClient $httpClient,
    protected ?int $timeout = 30
) {}
```

4. **Use explicit nullable types**

```php
public function getLastSync(): ?Carbon
protected ?string $authToken = null;
```

5. **Use union types** when appropriate

```php
public function find(string|int $driverId): ?Driver
```

**❌ FORBIDDEN:**
- Don't omit return types (even for void)
- Don't use `mixed` when you can be more specific

---

### PHPDoc Standards

**Class Level:**

```php
/**
 * Samsara ELD API client.
 *
 * @author Your Name <your.email@example.com>
 */
class SamsaraClient
```

**Array Shape Definitions:**

```php
/**
 * @return array<string, mixed>
 */

/**
 * @return Collection<int, DriverLog>
 */

/**
 * @return array{driver_id: string, status: string, logs: array<int, array{timestamp: int, event: string}>}
 */
```

**Property Types:**

```php
/**
 * The API endpoints configuration.
 *
 * @var array<string, string>
 */
protected array $endpoints;
```

**Don't:**
- Don't use generic `@return array` - be specific
- Don't write long prose - be concise

---

### Collections

```php
// Use Laravel Collections
use \Illuminate\Support\Collection;
$logs = new Collection();

// Chain collection methods
$driverLogs->filter(...)->map(...)->values();

// Use arrow functions for simple transformations
->map(fn (DriverLog $log) => ['id' => $log->id, 'status' => $log->status])

// Collection patterns
$response->collect('data')->each(function ($item) {
    // Process each item
});
```

**❌ Don't:**
- Don't use raw PHP array functions when collections work
- Don't use `foreach` when collection methods are cleaner

---

### Comments

**✅ Use Comments For:**

```php
// Convert API timestamp to Carbon instance
$timestamp = Carbon::createFromTimestamp($data['timestamp']);

// API rate limit: 100 requests per minute
$this->rateLimiter->throttle(100, 60);

// Map API status codes to internal enum values
return match ($apiStatus) { ... };
```

**❌ Don't:**
- Don't comment obvious code
- Don't write essay-length comments
- Don't leave commented-out code
- Don't use block comments except for PHPDoc

---

## Testing Standards

### Test-Driven Development (TDD)

**Non-Negotiable Objective:**
All feature work MUST follow **Red → Green → Refactor** to achieve **total and complete code coverage**.

No feature is considered "done" until tests comprehensively cover:
- Expected behavior
- Edge cases
- Failure modes

### Test Types

**Unit Tests:**
Tests that do **NOT** require additional layers:
- No database access
- No external network calls (mock HTTP responses)
- Use fakes/mocks/stubs for external dependencies
- Fast execution

```php
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SamsaraClientTest extends TestCase
{
    #[Test]
    public function it_parses_driver_log_response_correctly(): void
    {
        $client = new SamsaraClient('test-api-key');
        
        $response = ['driver_id' => '123', 'status' => 'ON_DUTY'];
        $result = $client->parseDriverLog($response);
        
        $this->assertEquals('123', $result->driverId);
        $this->assertEquals(DriverStatus::ON_DUTY, $result->status);
    }
}
```

**Feature/Integration Tests:**
Tests that **CAN** involve additional layers:
- HTTP client with mocked responses
- Cache operations
- Configuration loading
- Real integration with Laravel framework

```php
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Http;

class EldServiceTest extends TestCase
{
    #[Test]
    public function it_fetches_driver_logs_from_api(): void
    {
        Http::fake([
            'api.samsara.com/*' => Http::response(['data' => [...]], 200)
        ]);
        
        $service = app(EldService::class);
        $logs = $service->fetchDriverLogs('driver-123');
        
        $this->assertNotEmpty($logs);
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.samsara.com/v1/driver-logs/driver-123';
        });
    }
}
```

### TDD Workflow (Enforced)

**1. RED — Write Tests First**
```bash
git add tests/
git commit -m "test: add coverage for driver log parsing"
```

**2. GREEN — Implement to Make Tests Pass**
```bash
git add src/
git commit -m "feat: implement driver log parsing"
```

**3. REFACTOR — Clean Up**
```bash
git add src/
git commit -m "refactor: extract log parsing to dedicated class"
```

### General Testing Rules

- Use PHPUnit
- Use `#[Test]` attribute
- Name tests: `it_{does_something}()`
- Test happy path, failure cases, and edge cases
- Mock external HTTP calls
- Test API error handling (rate limits, timeouts, invalid responses)

**When to Use Which Type:**

**Use Unit Tests for:**
- Response parsing logic
- Data transformation methods
- DTO/Value object construction
- Utility/helper functions

**Use Integration Tests for:**
- HTTP client integration
- Cache behavior
- Laravel service provider registration
- End-to-end API workflows

---

## Code Organization

### Code Formatting

**Before Committing:**

```bash
vendor/bin/pint --dirty
```

**Key Rules:**
- Align array arrows with single space
- Snake case for test methods
- Imports sorted by length
- No unused imports

---

## Anti-Patterns to Avoid

**❌ NEVER DO:**

1. Missing return types
2. Generic variable names (`$data`, `$result`, `$temp`)
3. Using `else` statements
4. Nesting more than 2 levels deep
5. Not mocking external HTTP calls in tests
6. Forgetting to handle API rate limits
7. Not validating API responses before parsing
8. Storing credentials in code (use config files)
9. Not implementing retry logic for transient failures
10. Omitting author tags in class PHPDoc
11. Not logging API errors for debugging
12. Tight coupling to specific ELD provider (use interfaces/contracts)

---

## Quality Checklist

Before submitting code:

- [ ] All methods have return type declarations
- [ ] No `else` statements (use early returns)
- [ ] PHPDoc includes array shapes for complex arrays
- [ ] Tests written BEFORE implementation (TDD)
- [ ] Unit tests for parsing/transformation logic
- [ ] Integration tests for API calls (with mocked HTTP)
- [ ] Error handling for all API failure scenarios
- [ ] Rate limiting implemented and tested
- [ ] Ran `vendor/bin/pint --dirty`
- [ ] No unused imports
- [ ] Author tag in class PHPDoc
- [ ] API credentials read from config, not hardcoded
- [ ] Retry logic for transient API failures
- [ ] All external HTTP calls are mocked in tests

---
