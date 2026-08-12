# Testing and Webhooks

## Fake SDK Calls

`Samsara::fake()` installs the same `SamsaraFake` instance as the facade root and both container bindings:

```php
use Samsara\Facades\Samsara;

$fake = Samsara::fake();
$fake->fakeDrivers([
    ['id' => 'driver-1', 'name' => 'Taylor'],
]);

$drivers = Samsara::drivers()->all();

$this->assertSame('Taylor', $drivers->first()->name);
$fake->assertRequested('/fleet/drivers');
```

Use:

- `fakeDrivers()`, `fakeVehicles()`, and `fakeVehicleStats()` for common responses.
- `fakeResponse($endpoint, $data, $status)` for other top-level `data` responses.
- `assertRequested()` for the endpoint.
- `assertRequestedWithParams()` for important filters and query encoding.
- `assertNothingRequested()` when a path must fail or short-circuit locally.
- `getRecordedRequests()` for method, URL, header, or body assertions the helpers do not cover.

Package fixtures return full response envelopes. Pass the inner `data` list to fake helpers:

```php
$payload = Samsara\Testing\Fixtures::drivers();
$fake->fakeDrivers($payload['data']);
```

For arbitrary error envelopes, headers, response sequences, or conditional matching, use `Samsara::getHttpFactory()` and Laravel's HTTP factory directly.

Every used SDK operation should have a focused success-path test. Add mapped failure, pagination, or exact-encoding coverage when the application depends on that behavior.

## Verify Incoming Webhooks

Set `SAMSARA_WEBHOOK_SECRET` to the Base64-encoded Samsara webhook secret. Protect webhook routes with `Samsara\Webhooks\VerifyWebhookSignature` or call `Samsara\Webhooks\WebhookSignatureVerifier` directly when middleware is not the right boundary.

The verifier checks the Samsara signature and timestamp headers and enforces the configured tolerance. Do not decode, reformat, or parse the body before verification; signature computation depends on the original request payload.

Test webhook handling with the verifier's signature-generation support or the package's documented request construction. Cover at least:

- a valid signature;
- an invalid signature;
- an expired timestamp; and
- a missing secret or required header when that path is possible.

After verification, parse the event and handle only documented `WebhookEvent` values. Return success only after the application has durably accepted or intentionally ignored the event according to its own delivery policy.
