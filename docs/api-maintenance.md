---
title: API Maintenance
layout: default
nav_order: 9
description: Repeatable OpenAPI refresh, SDK drift review, and release gates.
permalink: /api-maintenance
---

# API Maintenance

Use this workflow to refresh the Samsara contract, review SDK and dependency drift, and produce release evidence. It preserves upstream bytes and separates machine-checkable facts from human contract judgments.

## Authority and proof boundaries

Use four distinct authorities:

1. The confirmed first-party Samsara download URL governs claims about the intended published OpenAPI contract. Confirm Samsara ownership on every refresh; do not promote a third-party generated specification to authority.
2. A dated raw snapshot, its metadata sidecar, and a matching SHA-256 record exactly what was retrieved at one time. They do not prove live-service behavior.
3. `src/`, its tests, and `composer.json` govern what this package actually exposes and supports.
4. `README.md`, `docs/`, `CHANGELOG.md`, `DEVELOPMENT_CHANGELOG.md`, and `BREAKING_CHANGES.md` govern consumer-facing claims and migration guidance.

The root `samsara.json` is a historical package baseline. Its JSON is readable, but the repository does not prove its exact source URL or retrieval timestamp. Do not treat its `info.version` as a capture date or use it as current-upstream proof.

As of 2026-08-10, Samsara's OpenAPI guide and machine-readable API catalog identify `https://developers.samsara.com/openapi/samsara-api.json` as the current first-party service description. Reconfirm both authority pages before each later retrieval; this recorded URL is evidence, not a permanent allowlist.

## Snapshot layout

Store each capture as an append-only pair:

```text
contracts/samsara/
├── README.md
├── samsara-api-YYYY-MM-DD.json
└── samsara-api-YYYY-MM-DD.json.metadata.json
```

Never reformat or overwrite a raw snapshot. If two captures are required on one UTC date, give them distinct names and pass `--snapshot` explicitly during review; automatic discovery intentionally rejects a newest-date tie.

`contracts/samsara/README.md` should summarize the provenance history. The sidecar remains the machine-readable authority for each snapshot.

## Required provenance metadata

The current sidecar shape follows the acquisition workstream's proven capture. Record unavailable upstream identifiers as `null`; never invent values. Use an RFC 3339 retrieval timestamp with its timezone, and prefer UTC timestamps ending in `Z` for future captures.

```json
{
  "snapshot": "contracts/samsara/samsara-api-2026-08-10.json",
  "sha256": "<64 lowercase hexadecimal characters>",
  "source_url": "https://developers.samsara.com/openapi/samsara-api.json",
  "retrieved_at": "2026-08-10T17:00:00Z",
  "retrieval_timezone": "UTC",
  "raw_bytes": 123,
  "http": {
    "status": 200,
    "date": "<HTTP Date header>",
    "content_type": "application/json",
    "etag": null,
    "last_modified": null
  },
  "upstream": {
    "openapi": "<top-level openapi value>",
    "info_version": "<info.version value>",
    "commit": null
  },
  "authority": {
    "guide_url": "https://developers.samsara.com/docs/openapi-spec",
    "api_catalog_url": "https://developers.samsara.com/.well-known/api-catalog"
  },
  "license": {
    "declared_in_contract": null,
    "terms_of_service_in_contract": null,
    "website_terms_url": "https://www.samsara.com/legal/website-terms-of-service",
    "note": "<factual rights and use note>"
  },
  "validator": {
    "name": "<validator package>",
    "version": "<exact validator version>",
    "command": "<full OpenAPI validator command and version>",
    "result": "valid",
    "exit_code": 0
  }
}
```

For later captures, add an optional `predecessor` object containing the prior `snapshot` and raw-byte `sha256`; use no predecessor field for the first proven snapshot. Record HTTP `ETag` and `Last-Modified` only when the retrieval response actually supplied them. Record the exact retrieval command and response details in `contracts/samsara/README.md`. Never put credentials, tokens, signed URLs, or private headers in metadata or commands.

The local validator checks the fields shown above, metadata shape, and file consistency; a human must verify that `source_url` is truly first-party. It also recognizes optional `schema_version: 1`, optional reference-resolution evidence, and the earlier `<snapshot-stem>.metadata.json` sidecar name.

The 2026-08-10 contract contains no `info.license` or `info.termsOfService`. The acquisition record therefore points to Samsara's website terms and states that the SDK's MIT license does not cover the contract. Keep that rights note with the snapshot, and confirm authorization before committing or publishing contract bytes.

## Refresh procedure

1. Start from the most recent proven sidecar. Reconfirm that its source is still Samsara-owned and that it still represents the intended API. If the source is ambiguous or requires unavailable authentication, stop and record the blocker.
2. Choose a UTC date and refuse to overwrite an existing path.
3. Download to a temporary file with redirects and HTTP failures handled explicitly. Preserve the response body byte-for-byte.
4. Check that the temporary file is a JSON object with top-level `openapi`, object-valued `info`, and object-valued `paths`. Run a full OpenAPI standards validator as a separate check.
5. Record the exact retrieval facts, raw-byte digest, byte size, document versions, validator command/version, and predecessor in the sidecar.
6. Place the untouched snapshot and sidecar under `contracts/samsara/`, then run the repository validator.
7. Update `contracts/samsara/README.md` without rewriting earlier provenance.

Example shell setup, requiring the maintainer to re-confirm the recorded source URL:

```bash
set -euo pipefail

confirmed_source_url='https://developers.samsara.com/openapi/samsara-api.json'
snapshot_date="$(date -u +%F)"
snapshot_path="contracts/samsara/samsara-api-${snapshot_date}.json"
metadata_path="${snapshot_path}.metadata.json"
download_path="$(mktemp)"
header_path="$(mktemp)"
receipt_path="$(mktemp)"
trap 'rm -f "$download_path" "$header_path" "$receipt_path"' EXIT

mkdir -p contracts/samsara
test ! -e "$snapshot_path"
test ! -e "$metadata_path"
curl --fail --location --silent --show-error \
  --dump-header "$header_path" \
  --output "$download_path" \
  --write-out 'final_url=%{url_effective}\nhttp_status=%{http_code}\ncontent_type=%{content_type}\n' \
  "$confirmed_source_url" > "$receipt_path"
retrieved_at="$(date -u +%Y-%m-%dT%H:%M:%SZ)"
php -r '$document=json_decode(file_get_contents($argv[1]), false, 512, JSON_THROW_ON_ERROR); if (!is_object($document) || !isset($document->openapi) || !is_object($document->info ?? null) || !is_object($document->paths ?? null)) { fwrite(STDERR, "Not a minimal OpenAPI object.\n"); exit(1); }' "$download_path"
shasum -a 256 "$download_path"
wc -c "$download_path"
php -r '$input=fopen($argv[1], "rb"); if ($input === false) { exit(1); } $output=@fopen($argv[2], "xb"); if ($output === false) { fwrite(STDERR, "Could not create snapshot without overwriting an existing file.\n"); exit(1); } $expected=filesize($argv[1]); $copied=stream_copy_to_stream($input, $output); fclose($input); fclose($output); if ($expected === false || $copied !== $expected) { unlink($argv[2]); exit(1); }' "$download_path" "$snapshot_path"
cmp -s "$download_path" "$snapshot_path"
printf 'retrieved_at=%s\n' "$retrieved_at"
sed -n '/^HTTP\//p; /^[Dd]ate:/p; /^[Cc]ontent-[Tt]ype:/p; /^ETag:/p; /^[Ll]ast-[Mm]odified:/p' "$header_path"
cat "$receipt_path"
```

The block creates the destination with exclusive mode, so it cannot overwrite an existing snapshot, then byte-compares the stored copy. Create the sidecar from the printed digest, byte count, UTC time, response headers, and receipt before running the repository validator. The URL above was confirmed on 2026-08-10, but re-check its first-party authority before using it again.

## Digest and structural verification

Validate the uniquely newest dated snapshot:

```bash
php scripts/validate-samsara-snapshot.php
```

Validate a specific pair, including same-date captures:

```bash
php scripts/validate-samsara-snapshot.php \
  --snapshot "$snapshot_path" \
  --metadata "$metadata_path"
```

Independently compare the raw digest with the sidecar:

```bash
shasum -a 256 "$snapshot_path"
```

Run the dependency-free behavior tests:

```bash
php tests/Maintenance/ApiSnapshotValidatorTest.php
```

The 2026-08-10 acquisition also passed this pinned, standards-aware validation command:

```bash
SAMSARA_NPX_CACHE=/tmp/samsara-openapi-npx-cache \
  npm_config_cache=/tmp/samsara-openapi-npx-cache \
  npx --yes --package=@apidevtools/swagger-cli@4.0.4 \
  swagger-cli validate "$snapshot_path"
```

That command is networked and downloads an ephemeral tool without changing package manifests. Version 4.0.4 emits an abandonment notice recommending Redocly CLI. Preserve the recorded result for reproducibility, but approve and pin a maintained replacement before making it the long-term repository standard.

The validator reads only. It verifies a dated filename, rejects symlinked evidence and ambiguous selection, binds a repository-relative metadata path to the selected snapshot, checks readable nonempty JSON objects, requires an OpenAPI 3 semantic-version marker without pinning an exact minor or patch version, checks minimal structure and provenance fields, and verifies byte size plus the SHA-256 of exact snapshot bytes. It does not validate every OpenAPI rule, authenticate the source, compare endpoint semantics, or call Samsara.

## Contract diff review

Compare the new snapshot with the sidecar's predecessor. Keep any normalized or pretty-printed copies outside `contracts/samsara/`; they are review aids, never replacement evidence.

Classify every difference in these areas:

- paths and HTTP methods;
- parameters, locations, types, formats, and requiredness;
- request bodies, response codes, response schemas, and error shapes;
- schemas, properties, required arrays, nullable values, and enums;
- authentication, server, pagination, beta, preview, legacy, and deprecation markers.

For each difference, record the old contract, new contract, package `file:line` consumer, tests, documentation, classification, and action. Classify it as additive, breaking, docs-only, package-only, intentionally unsupported, or no package impact. The release inventory must contain zero unclassified differences.

A changed digest means only that bytes differ. It does not identify the semantic change or prove that package code must change.

## Package and dependency drift review

Trace accepted contract changes through resource methods, request construction, query parameters, entity mapping, enums, pagination, exceptions, fakes, and fixtures. Add regression coverage before implementation changes and keep the public behavior owner clear.

Review direct dependency drift without changing manifests or locks:

```bash
composer outdated --direct --strict
```

This command is networked and may return nonzero when updates exist. Record the date, resolved package versions, available versions, security or compatibility implications, and the decision to update or defer. Never treat “newer exists” as permission to update. Re-run lowest-supported dependency coverage whenever constraints change.

## Documentation and changelogs

Before release:

- update resource pages and examples for every accepted public behavior change;
- reconcile `README.md`, getting started, configuration, query builder, error handling, and testing guidance;
- update navigation and the Jekyll version when the package version changes;
- add detailed evidence and deferred work to `DEVELOPMENT_CHANGELOG.md`;
- add every proven consumer migration to `BREAKING_CHANGES.md`, or retain an explicitly provisional none-yet state while review is incomplete;
- summarize released consumer-facing changes in the existing Keep a Changelog `CHANGELOG.md`.

Do not claim full parity from a passing structural check, unit test, static analysis run, or documentation build.

## Release gates

### Acceptance criteria

- [ ] The source is confirmed first-party and the exact URL and retrieval time are recorded.
- [ ] Rights to commit or publish the upstream contract bytes are confirmed; the package's MIT license is not assumed to cover them.
- [ ] The dated snapshot is immutable, its sidecar is complete, and independent SHA-256 checks agree.
- [ ] The local validator and a full OpenAPI standards validator pass with exact commands recorded.
- [ ] Every contract difference is classified; the unclassified count is zero.
- [ ] Every accepted package change has regression coverage and matching consumer docs.
- [ ] `BREAKING_CHANGES.md` contains migration actions for every proven break and the SemVer decision matches the impact.
- [ ] Direct dependency drift is reviewed without an automatic update.
- [ ] All commands below pass in the supported compatibility matrix.

```bash
php scripts/validate-samsara-snapshot.php
php tests/Maintenance/ApiSnapshotValidatorTest.php
composer validate --strict --no-check-publish
vendor/bin/phpunit
vendor/bin/phpstan analyse --no-progress
vendor/bin/pint --test
```

When the documentation bundle is installed, also run:

```bash
bundle exec jekyll build --source docs --destination /tmp/samsara-docs-site
```

### Definition of done

- [ ] Snapshot, metadata, inventory, package changes, tests, docs, and all three changelogs agree.
- [ ] CI passes for PHP 8.2–8.4, Laravel 12–13, and the lowest-supported dependency cell.
- [ ] Exact commands, results, digests, counts, assumptions, and deferrals are recorded.
- [ ] A reviewer approves all breaking-change classifications and the release version.
- [ ] No unproven claim is presented as current Samsara behavior.
