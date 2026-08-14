# Samsara OpenAPI contract snapshots

This directory contains byte-for-byte snapshots of Samsara's first-party OpenAPI contract for provenance and compatibility review. Do not format, normalize, or otherwise rewrite a snapshot file. Derived metadata belongs in a sidecar file.

## Current snapshot

- Snapshot: `samsara-api-2026-08-10.json`
- Metadata: `samsara-api-2026-08-10.json.metadata.json`
- Official source: <https://developers.samsara.com/openapi/samsara-api.json>
- Retrieval completed: `2026-08-10T11:47:09-05:00` (`America/Chicago`, CDT)
- HTTP response date: `Mon, 10 Aug 2026 16:47:04 GMT`
- HTTP status: `200`
- Content-Type: `application/json; charset=utf-8`
- Raw size: `4,206,874` bytes
- SHA-256: `a7255e2d5b7a94eea97e11f47db1c7b969931f639366bcae49e4922b02ba3fc6`
- ETag: `W/"40311a-FNzOZFT6SXU1AULxdChC0l9RNhU"`
- Last-Modified: `2026-08-10T14:51:20.878Z`
- OpenAPI version: `3.0.1`
- Contract `info.version`: `2025-10-23`

The live endpoint does not expose an upstream Git commit. The ETag, Last-Modified value, contract `info.version`, retrieval time, byte count, and digest are therefore the available upstream and snapshot identifiers.

## Authority and selection

Samsara's [OpenAPI guide](https://developers.samsara.com/docs/openapi-spec) links to the source above and says that the contract powers its documentation and is kept up to date. Samsara's machine-readable [API catalog](https://developers.samsara.com/.well-known/api-catalog) identifies the same URL as its `service-desc` with media type `application/vnd.oai.openapi+json`. Samsara's [corporate developer page](https://www.samsara.com/support/developers) links to the developer portal, completing the first-party ownership chain.

The older Samsara-owned [`samsarahq/api-docs`](https://github.com/samsarahq/api-docs) repository was not used. At retrieval time its latest contract commit was `263d92d9209b174c3d7409db31b45470dbda002a` from 2022-02-22, and its Swagger 2.0 contract reported `info.version` `2021-06-09`. It is materially older than the live contract.

## License and use

No `info.license` or `info.termsOfService` field is present in the downloaded contract, and no separate open-source license for the live contract was found. Samsara's [Website Terms and Conditions of Use](https://www.samsara.com/legal/website-terms-of-service), section 4.2 (last updated May 2026 when checked), treats site content as proprietary. It permits limited, unmodified downloading of material specifically made available for download for personal, informational, noncommercial purposes, while broader copying, publishing, or distribution requires Samsara's prior written authorization.

The SDK package's MIT license does not apply to Samsara's contract. Keep the snapshot unmodified, preserve this provenance notice, and confirm applicable authorization before committing or otherwise publishing the snapshot.

Composer and Git release archives exclude `contracts/` and the historical root `samsara.json`. These files are repository-only maintenance inputs and are not part of the distributable SDK package.

## Verification performed

JSON parsing passed:

```sh
jq empty contracts/samsara/samsara-api-2026-08-10.json
```

OpenAPI validation passed with the pinned `@apidevtools/swagger-cli` 4.0.4 validator:

```sh
SAMSARA_NPX_CACHE=/tmp/samsara-openapi-npx-cache \
  npm_config_cache=/tmp/samsara-openapi-npx-cache \
  npx --yes --package=@apidevtools/swagger-cli@4.0.4 \
  swagger-cli validate contracts/samsara/samsara-api-2026-08-10.json
```

Result:

```text
contracts/samsara/samsara-api-2026-08-10.json is valid
```

`@apidevtools/swagger-cli` 4.0.4 emits an abandonment notice recommending Redocly CLI. Its pinned result is recorded here for reproducibility; future refreshes should also use the repository's maintained contract validator when one is available.

Offline parsing and internal `$ref` resolution also passed with the already-installed `swagger-client` 3.37.4 dependency:

```text
swagger-client resolve=PASS
errors=0
```

The stored digest was independently re-read from disk:

```sh
shasum -a 256 contracts/samsara/samsara-api-2026-08-10.json
```

Expected output:

```text
a7255e2d5b7a94eea97e11f47db1c7b969931f639366bcae49e4922b02ba3fc6  contracts/samsara/samsara-api-2026-08-10.json
```

## Refresh procedure

1. Re-check the official OpenAPI guide and API catalog above. Stop if they no longer identify the same public first-party contract.
2. Review the current website terms and any license embedded in the contract before storing or publishing a new snapshot.
3. Choose a dated filename. If more than one snapshot is taken on the same date, include the UTC retrieval time or a digest prefix so filenames stay unambiguous.
4. Download the response directly without a formatter or parser:

    ```sh
    curl --fail --silent --show-error --location \
      --dump-header /tmp/samsara-openapi.headers \
      --output contracts/samsara/samsara-api-YYYY-MM-DD.json \
      https://developers.samsara.com/openapi/samsara-api.json
    ```

5. Record the retrieval timestamp with timezone, HTTP status, Content-Type, ETag, Last-Modified value, and raw byte count. Preserve the response body exactly.
6. Parse, hash, and validate the stored file:

    ```sh
    jq empty contracts/samsara/samsara-api-YYYY-MM-DD.json
    shasum -a 256 contracts/samsara/samsara-api-YYYY-MM-DD.json
    wc -c contracts/samsara/samsara-api-YYYY-MM-DD.json
    ```

7. Add or update the adjacent `.metadata.json` sidecar and this README. Do not insert metadata into the snapshot itself.
8. Run `composer check:maintenance` plus a standards-aware OpenAPI validator. Record the exact tool version, command, exit status, and result.
