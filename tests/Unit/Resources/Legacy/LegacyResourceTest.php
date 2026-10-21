<?php

namespace Samsara\Tests\Unit\Resources\Legacy;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Legacy\LegacyResource;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Unit tests for the LegacyResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class LegacyResourceTest extends TestCase
{
    protected HttpFactory $http;

    protected Samsara $samsara;

    protected function setUp(): void
    {
        parent::setUp();

        $this->http = new HttpFactory;
        $this->http->preventStrayRequests();
        $this->samsara = new Samsara('test-token');
        $this->samsara->setHttpFactory($this->http);
    }

    #[Test]
    public function it_can_send_a_message(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['messageId' => 123]),
        ]);
        $payload = ['driverIds' => [111, 222], 'text' => 'Dispatch update'];

        $result = (new LegacyResource($this->samsara))->sendMessage($payload);

        $this->assertSame(['messageId' => 123], $result);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'POST'
            && parse_url($request->url(), PHP_URL_PATH) === '/v1/fleet/messages'
            && $request->data() === $payload);
    }

    /**
     * @param  array<int, mixed>  $arguments
     * @param  array<string, mixed>  $payload
     */
    #[Test]
    #[DataProvider('legacyRequestContracts')]
    public function it_routes_legacy_requests_through_the_exact_wire_contract(
        string $method,
        array $arguments,
        string $httpMethod,
        string $path,
        array $payload
    ): void {
        $this->http->fake([
            '*' => $this->http->response(['ok' => true]),
        ]);
        $resource = new LegacyResource($this->samsara);

        $result = $resource->{$method}(...$arguments);

        $this->assertSame(['ok' => true], $result);
        $this->http->assertSent(function ($request) use ($httpMethod, $path, $payload): bool {
            if ($request->method() !== $httpMethod || parse_url($request->url(), PHP_URL_PATH) !== $path) {
                return false;
            }

            return $httpMethod === 'GET'
                ? $this->queryFor($request->url()) === $this->stringify($payload)
                : $request->data() === $payload;
        });
    }

    /**
     * @return array<string, array{string, array<int, mixed>, string, string, array<string, mixed>}>
     */
    public static function legacyRequestContracts(): array
    {
        return [
            'asset locations'     => ['assetLocations', [['groupId' => 123]], 'GET', '/v1/fleet/assets/locations', ['groupId' => 123]],
            'asset reefers'       => ['assetReefers', [['groupId' => 123]], 'GET', '/v1/fleet/assets/reefers', ['groupId' => 123]],
            'driver safety score' => [
                'driverSafetyScore',
                ['driver-123', ['startMs' => 1000, 'endMs' => 2000]],
                'GET',
                '/v1/fleet/drivers/driver-123/safety/score',
                ['startMs' => 1000, 'endMs' => 2000],
            ],
            'fleet assets'            => ['fleetAssets', [['groupId' => 123]], 'GET', '/v1/fleet/assets', ['groupId' => 123]],
            'hos authentication logs' => [
                'hosAuthenticationLogs',
                [['driverId' => 123]],
                'GET',
                '/v1/fleet/hos_authentication_logs',
                ['driverId' => 123],
            ],
            'machines'         => ['machines', [['groupId' => 123]], 'POST', '/v1/machines/list', ['groupId' => 123]],
            'maintenance list' => ['maintenanceList', [['groupId' => 123]], 'GET', '/v1/fleet/maintenance/list', ['groupId' => 123]],
            'messages'         => ['messages', [['groupId' => 123]], 'GET', '/v1/fleet/messages', ['groupId' => 123]],
            'trips'            => [
                'trips',
                [['vehicleId' => 123, 'startMs' => 1000, 'endMs' => 2000]],
                'GET',
                '/v1/fleet/trips',
                ['vehicleId' => 123, 'startMs' => 1000, 'endMs' => 2000],
            ],
            'vehicle harsh event' => [
                'vehicleHarshEvent',
                ['vehicle-123', ['timestamp' => 1000]],
                'GET',
                '/v1/fleet/vehicles/vehicle-123/safety/harsh_event',
                ['timestamp' => 1000],
            ],
            'vehicle safety score' => [
                'vehicleSafetyScore',
                ['vehicle-123', ['startMs' => 1000, 'endMs' => 2000]],
                'GET',
                '/v1/fleet/vehicles/vehicle-123/safety/score',
                ['startMs' => 1000, 'endMs' => 2000],
            ],
            'vision cameras' => ['visionCameras', [['groupId' => 123]], 'GET', '/v1/industrial/vision/cameras', ['groupId' => 123]],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function queryFor(string $url): array
    {
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        /** @var array<string, string> $query */
        return $query;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, string>
     */
    private function stringify(array $payload): array
    {
        return array_map(static fn (mixed $value): string => (string) $value, $payload);
    }
}
