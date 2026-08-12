<?php

namespace Samsara\Tests\Unit\Testing;

use RuntimeException;
use Samsara\Tests\TestCase;
use Samsara\Testing\Fixtures;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the Fixtures class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class FixturesTest extends TestCase
{
    #[Test]
    public function fixtures_have_pagination_data(): void
    {
        $drivers = Fixtures::drivers();

        $this->assertArrayHasKey('pagination', $drivers);
        $this->assertArrayHasKey('endCursor', $drivers['pagination']);
        $this->assertArrayHasKey('hasNextPage', $drivers['pagination']);
    }

    #[Test]
    public function it_can_load_addresses_fixture(): void
    {
        $addresses = Fixtures::addresses();

        $this->assertArrayHasKey('data', $addresses);
        $this->assertIsArray($addresses['data']);
        $this->assertNotEmpty($addresses['data']);
        $this->assertArrayHasKey('formattedAddress', $addresses['data'][0]);
        $this->assertSame('tag-001', $addresses['data'][0]['tags'][0]['id']);
        $this->assertArrayDoesNotHaveKeys(['tagIds'], $addresses['data'][0]);
        $this->assertSame('Receiving', $addresses['data'][1]['contacts'][0]['firstName']);
        $this->assertSame('Manager', $addresses['data'][1]['contacts'][0]['lastName']);
        $this->assertArrayDoesNotHaveKeys(['name'], $addresses['data'][1]['contacts'][0]);
    }

    #[Test]
    public function it_can_load_custom_fixture_by_filename(): void
    {
        $data = Fixtures::load('drivers.json');

        $this->assertArrayHasKey('data', $data);
        $this->assertSame('driver-001', $data['data'][0]['id']);
    }

    #[Test]
    public function it_can_load_drivers_fixture(): void
    {
        $drivers = Fixtures::drivers();

        $this->assertArrayHasKey('data', $drivers);
        $this->assertIsArray($drivers['data']);
        $this->assertNotEmpty($drivers['data']);
        $this->assertArrayHasKey('id', $drivers['data'][0]);
        $this->assertSame('driver-001', $drivers['data'][0]['id']);
        $this->assertSame(
            'USA 70 hour / 8 day',
            $drivers['data'][0]['eldSettings']['rulesets'][0]['cycle']
        );
        $this->assertIsInt($drivers['data'][0]['carrierSettings']['dotNumber']);
        $this->assertSame('tag-001', $drivers['data'][0]['tags'][0]['id']);
        $this->assertArrayDoesNotHaveKeys(['tagIds'], $drivers['data'][0]);
    }

    #[Test]
    public function it_can_load_dvirs_fixture(): void
    {
        $dvirs = Fixtures::dvirs();

        $this->assertArrayHasKey('data', $dvirs);
        $this->assertIsArray($dvirs['data']);
        $this->assertNotEmpty($dvirs['data']);
        $this->assertFixtureRowsMatchContractSchema($dvirs, 'DvirStreamResponseDataResponseBody');
        $this->assertSame('preTrip', $dvirs['data'][0]['type']);
        $this->assertSame('safe', $dvirs['data'][0]['safetyStatus']);
        $this->assertIsString($dvirs['data'][0]['formattedAddress']);
        $this->assertArrayDoesNotHaveKeys(
            [
                'author',
                'inspectionType',
                'safeToOperate',
                'notes',
                'defects',
                'location',
                'startTime',
                'endTime',
                'vehicleDefects',
                'trailerDefects',
            ],
            $dvirs['data'][0]
        );
        $this->assertSame(['defect-001'], $dvirs['data'][1]['defectIds']);
    }

    #[Test]
    public function it_can_load_equipment_fixture(): void
    {
        $equipment = Fixtures::equipment();

        $this->assertArrayHasKey('data', $equipment);
        $this->assertIsArray($equipment['data']);
        $this->assertNotEmpty($equipment['data']);
        $this->assertSame('equipment-001', $equipment['data'][0]['id']);
        $this->assertSame('FK001', $equipment['data'][0]['assetSerial']);
        $this->assertSame('tag-001', $equipment['data'][0]['tags'][0]['id']);
        $this->assertArrayDoesNotHaveKeys(['serial', 'tagIds', 'attributes'], $equipment['data'][0]);
    }

    #[Test]
    public function it_can_load_hos_logs_fixture(): void
    {
        $logs = Fixtures::hosLogs();

        $this->assertArrayHasKey('data', $logs);
        $this->assertIsArray($logs['data']);
        $this->assertNotEmpty($logs['data']);
        $this->assertSame('driver-001', $logs['data'][0]['driver']['id']);
        $this->assertCount(3, $logs['data'][0]['hosLogs']);
        $this->assertSame('onDuty', $logs['data'][0]['hosLogs'][0]['hosStatusType']);
        $this->assertSame('vehicle-001', $logs['data'][0]['hosLogs'][0]['vehicle']['id']);
        $this->assertSame([], $logs['data'][0]['hosLogs'][0]['codrivers']);
        $this->assertSame(37.7749, $logs['data'][0]['hosLogs'][0]['logRecordedLocation']['latitude']);
        $this->assertArrayDoesNotHaveKeys(
            ['id', 'driver', 'annotations', 'codriverIds', 'location', 'logRecordedAtTime', 'origin'],
            $logs['data'][0]['hosLogs'][0]
        );
    }

    #[Test]
    public function it_can_load_routes_fixture(): void
    {
        $routes = Fixtures::routes();

        $this->assertArrayHasKey('data', $routes);
        $this->assertIsArray($routes['data']);
        $this->assertNotEmpty($routes['data']);
        $this->assertArrayHasKey('stops', $routes['data'][0]);
        $this->assertSame('2024-06-15T08:00:00Z', $routes['data'][0]['scheduledRouteStartTime']);
        $this->assertSame('driver-001', $routes['data'][0]['driver']['id']);
        $this->assertSame('vehicle-001', $routes['data'][0]['vehicle']['id']);
        $this->assertSame('address-001', $routes['data'][0]['stops'][0]['address']['id']);
        $this->assertSame('departed', $routes['data'][0]['stops'][0]['state']);
        $this->assertSame('en route', $routes['data'][0]['stops'][1]['state']);
        $this->assertArrayDoesNotHaveKeys(
            [
                'scheduledStartTime',
                'scheduledEndTime',
                'actualStartTime',
                'actualEndTime',
                'state',
                'assignedDriverId',
                'assignedVehicleId',
            ],
            $routes['data'][0]
        );
        $this->assertArrayDoesNotHaveKeys(['addressId'], $routes['data'][0]['stops'][0]);
        $this->assertArrayDoesNotHaveKeys(['routeStartEndStopOption'], $routes['data'][0]['settings']);
    }

    #[Test]
    public function it_can_load_safety_events_fixture(): void
    {
        $events = Fixtures::safetyEvents();

        $this->assertArrayHasKey('data', $events);
        $this->assertIsArray($events['data']);
        $this->assertNotEmpty($events['data']);
        $this->assertSame('braking', $events['data'][0]['behaviorLabels'][0]['label']);
        $this->assertSame('automated', $events['data'][0]['behaviorLabels'][0]['source']);
        $this->assertSame(0.45, $events['data'][0]['maxAccelerationGForce']);
        $this->assertSame('needsCoaching', $events['data'][0]['coachingState']);
        $this->assertArrayDoesNotHaveKeys(['location'], $events['data'][0]['location']);
        $this->assertArrayDoesNotHaveKeys(
            [
                'behaviorLabel',
                'maxGForce',
                'coachable',
                'downloadedForwardVideoUrl',
                'downloadedInwardVideoUrl',
            ],
            $events['data'][0]
        );
        $this->assertSame(
            'https://example.com/video1.mp4',
            $events['data'][1]['downloadForwardVideoUrl']
        );
    }

    #[Test]
    public function it_can_load_tags_fixture(): void
    {
        $tags = Fixtures::tags();

        $this->assertArrayHasKey('data', $tags);
        $this->assertIsArray($tags['data']);
        $this->assertNotEmpty($tags['data']);
        $this->assertArrayHasKey('name', $tags['data'][0]);
    }

    #[Test]
    public function it_can_load_trailers_fixture(): void
    {
        $trailers = Fixtures::trailers();

        $this->assertArrayHasKey('data', $trailers);
        $this->assertIsArray($trailers['data']);
        $this->assertNotEmpty($trailers['data']);
        $this->assertSame('trailer-001', $trailers['data'][0]['id']);
        $this->assertSame('1UYFS2480RU123456', $trailers['data'][0]['trailerSerialNumber']);
        $this->assertSame('West Coast', $trailers['data'][0]['tags'][0]['name']);
        $this->assertArrayDoesNotHaveKeys(['vin', 'tagIds', 'attributes'], $trailers['data'][0]);
    }

    #[Test]
    public function it_can_load_users_fixture(): void
    {
        $users = Fixtures::users();

        $this->assertArrayHasKey('data', $users);
        $this->assertIsArray($users['data']);
        $this->assertNotEmpty($users['data']);
        $this->assertArrayHasKey('email', $users['data'][0]);
        $this->assertSame(['default', 'default', 'saml'], array_column($users['data'], 'authType'));
        $this->assertSame('role-001', $users['data'][0]['roles'][0]['role']['id']);
        $this->assertArrayDoesNotHaveKeys(
            ['tagIds', 'createdAtTime', 'lastSignInTime'],
            $users['data'][0]
        );
    }

    #[Test]
    public function it_can_load_vehicle_stats_fixture(): void
    {
        $stats = Fixtures::vehicleStats();

        $this->assertArrayHasKey('data', $stats);
        $this->assertIsArray($stats['data']);
        $this->assertNotEmpty($stats['data']);
        $this->assertArrayHasKey('gps', $stats['data'][0]);
        $this->assertSame('On', $stats['data'][0]['engineState']['value']);
        $this->assertIsInt($stats['data'][0]['fuelPercent']['value']);
        $this->assertArrayDoesNotHaveKeys(['time', 'engineStates', 'fuelPercents'], $stats['data'][0]);
    }

    #[Test]
    public function it_can_load_vehicles_fixture(): void
    {
        $vehicles = Fixtures::vehicles();

        $this->assertArrayHasKey('data', $vehicles);
        $this->assertIsArray($vehicles['data']);
        $this->assertNotEmpty($vehicles['data']);
        $this->assertFixtureRowsMatchContractSchema($vehicles, 'VehicleResponseObjectResponseBody');
        $this->assertArrayHasKey('id', $vehicles['data'][0]);
        $this->assertSame('2022-01-15T12:00:00Z', $vehicles['data'][0]['createdAtTime']);
        $this->assertSame('vehicle-001', $vehicles['data'][0]['id']);
        $this->assertSame('2022', $vehicles['data'][0]['year']);
        $this->assertSame('West Coast', $vehicles['data'][0]['tags'][0]['name']);
        $this->assertSame(['Long Haul'], $vehicles['data'][0]['attributes'][0]['stringValues']);
        $this->assertArrayDoesNotHaveKeys(['tagIds'], $vehicles['data'][0]);
        $this->assertArrayDoesNotHaveKeys(['stringValue'], $vehicles['data'][0]['attributes'][0]);
    }

    #[Test]
    public function it_can_load_webhooks_fixture(): void
    {
        $webhooks = Fixtures::webhooks();

        $this->assertArrayHasKey('data', $webhooks);
        $this->assertIsArray($webhooks['data']);
        $this->assertNotEmpty($webhooks['data']);
        $this->assertArrayHasKey('eventTypes', $webhooks['data'][0]);
        $this->assertSame(['VehicleUpdated'], $webhooks['data'][0]['eventTypes']);
        $this->assertSame('2021-06-09', $webhooks['data'][0]['version']);
        $this->assertSame('fixture-secret-key-001', $webhooks['data'][0]['secretKey']);
        $this->assertSame('X-API-Key', $webhooks['data'][0]['customHeaders'][0]['key']);
        $this->assertSame('secret-key-123', $webhooks['data'][0]['customHeaders'][0]['value']);
        $this->assertArrayDoesNotHaveKeys(
            [
                'isActive',
                'createdAtTime',
                'lastRequestAtTime',
                'lastResponseStatusCode',
                'headers',
                'customMetadata',
            ],
            $webhooks['data'][0]
        );
    }

    #[Test]
    public function it_throws_exception_for_missing_fixture(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Fixture file not found: nonexistent.json');

        Fixtures::load('nonexistent.json');
    }

    /**
     * @param  list<string>  $keys
     * @param  array<string, mixed>  $array
     */
    private function assertArrayDoesNotHaveKeys(array $keys, array $array): void
    {
        foreach ($keys as $key) {
            $this->assertArrayNotHasKey($key, $array);
        }
    }

    /**
     * @param  array{data: list<array<string, mixed>>}  $fixture
     */
    private function assertFixtureRowsMatchContractSchema(array $fixture, string $schemaName): void
    {
        $contractPath = dirname(__DIR__, 3).'/contracts/samsara/samsara-api-2026-08-10.json';
        $contract = json_decode(
            (string) file_get_contents($contractPath),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
        $schema = $contract['components']['schemas'][$schemaName];
        $allowedKeys = array_keys($schema['properties']);
        $requiredKeys = $schema['required'] ?? [];

        foreach ($fixture['data'] as $index => $row) {
            $this->assertEmpty(
                array_diff($requiredKeys, array_keys($row)),
                "Fixture row {$index} is missing required {$schemaName} keys.",
            );
            $this->assertEmpty(
                array_diff(array_keys($row), $allowedKeys),
                "Fixture row {$index} contains keys outside {$schemaName}.",
            );
        }
    }
}
