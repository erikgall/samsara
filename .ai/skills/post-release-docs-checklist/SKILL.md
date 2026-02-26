---
name: post-release-docs-checklist
description: Post-release checklist for auditing and updating user-facing documentation after code changes are released.
---

# Post-Release Documentation Checklist

## When to Use This Skill

Use this skill after releasing code changes (new version tag, merged PR, or deployment) to ensure all user-facing documentation stays in sync with the codebase.

## Checklist

Run through each section below. Use subagents in parallel to audit all areas simultaneously.

### 1. Webhook Documentation (`docs/resources/webhooks.md`)

- [ ] Event type count matches `WebhookEvent` enum (currently 27)
- [ ] All listed event types actually exist in the enum
- [ ] No valid enum values are missing from the docs
- [ ] Code examples reference real event type values
- [ ] Middleware usage examples are accurate
- [ ] Config keys match `config/samsara.php`

### 2. Resource Documentation (`docs/resources/*.md`)

- [ ] Any new resource methods are documented
- [ ] Any removed/deprecated methods are marked or removed
- [ ] Methods that throw `UnsupportedOperationException` are documented as such (not shown as working)
- [ ] Code examples use correct method signatures
- [ ] Related resource links are not broken

### 3. README (`README.md`)

- [ ] Features list includes any new features
- [ ] Configuration example includes all config keys from `config/samsara.php`
- [ ] Environment variable examples include any new env vars
- [ ] Code examples are accurate with current API
- [ ] Installation instructions are still correct (PHP/Laravel version requirements)

### 4. Configuration Docs (`docs/configuration.md`)

- [ ] "Complete Configuration File" section matches actual `config/samsara.php` exactly
- [ ] All config keys are documented with descriptions
- [ ] Comment paths match actual file (e.g., Settings -> Organization -> API Tokens)
- [ ] Default values documented match actual defaults

### 5. Getting Started (`docs/getting-started.md`)

- [ ] Setup steps cover any new required configuration
- [ ] Optional configuration steps mentioned for new features
- [ ] Links to detailed docs are not broken

### 6. Error Handling (`docs/error-handling.md`)

- [ ] Exception hierarchy includes all exception classes in `src/Exceptions/`
- [ ] New exceptions have their own detail section
- [ ] Code examples show correct catch patterns
- [ ] HTTP status code mappings are accurate

### 7. Query Builder (`docs/query-builder.md`)

- [ ] Any new builder methods are documented
- [ ] Method signatures match actual implementation
- [ ] Examples show correct usage patterns

### 8. Jekyll Site (`docs/_config.yml`)

- [ ] `version` field matches the released version
- [ ] Navigation includes any new documentation pages
- [ ] No broken permalinks

### 9. CHANGELOG (`CHANGELOG.md`)

- [ ] New version entry exists with correct date
- [ ] All user-facing changes are listed (features, fixes, breaking changes)
- [ ] Breaking changes are clearly marked
- [ ] Entries are categorized (Added, Changed, Fixed, Removed)

### 10. Composer Package (`composer.json`)

- [ ] Version constraints are correct
- [ ] PHP/Laravel version requirements are still accurate
- [ ] Any new dependencies are listed

## How to Run This Audit

```
Use 4-5 parallel subagents:

Agent 1: Webhook docs vs WebhookEvent enum + middleware source
Agent 2: Resource docs vs Resource classes (check for new/removed methods, exceptions)
Agent 3: README + getting-started + config docs vs config/samsara.php
Agent 4: Error handling docs vs src/Exceptions/*.php
Agent 5: CHANGELOG + Jekyll config + composer.json version checks
```

Each agent should:
1. Read the documentation file(s)
2. Read the corresponding source file(s)
3. Diff the two and report discrepancies
4. Fix any issues found

## Common Pitfalls

These are issues that have been caught in past audits:

- **Fictional event types in webhook docs** - Always cross-reference against the actual `WebhookEvent` enum
- **Showing unsupported operations as working** - Check if any resource methods throw `UnsupportedOperationException`
- **Stale config examples** - The "Complete Configuration File" section drifts from the actual config file
- **Broken cross-links** - Resource docs linking to wrong pages (e.g., `../query-builder.md` instead of `vehicle-locations.md`)
- **Overstated counts** - Documentation claiming "70+ event types" when only 27 exist
- **Missing env vars** - New config keys added without documenting the corresponding env variable
