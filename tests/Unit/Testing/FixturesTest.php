<?php

namespace ErikGall\Samsara\Tests\Unit\Testing;

use RuntimeException;
use ErikGall\Samsara\Tests\TestCase;
use ErikGall\Samsara\Testing\Fixtures;
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
    }

    #[Test]
    public function it_can_load_dvirs_fixture(): void
    {
        $dvirs = Fixtures::dvirs();

        $this->assertArrayHasKey('data', $dvirs);
        $this->assertIsArray($dvirs['data']);
        $this->assertNotEmpty($dvirs['data']);
        $this->assertArrayHasKey('inspectionType', $dvirs['data'][0]);
    }

    #[Test]
    public function it_can_load_equipment_fixture(): void
    {
        $equipment = Fixtures::equipment();

        $this->assertArrayHasKey('data', $equipment);
        $this->assertIsArray($equipment['data']);
        $this->assertNotEmpty($equipment['data']);
        $this->assertSame('equipment-001', $equipment['data'][0]['id']);
    }

    #[Test]
    public function it_can_load_hos_logs_fixture(): void
    {
        $logs = Fixtures::hosLogs();

        $this->assertArrayHasKey('data', $logs);
        $this->assertIsArray($logs['data']);
        $this->assertNotEmpty($logs['data']);
        $this->assertArrayHasKey('hosStatusType', $logs['data'][0]);
    }

    #[Test]
    public function it_can_load_routes_fixture(): void
    {
        $routes = Fixtures::routes();

        $this->assertArrayHasKey('data', $routes);
        $this->assertIsArray($routes['data']);
        $this->assertNotEmpty($routes['data']);
        $this->assertArrayHasKey('stops', $routes['data'][0]);
    }

    #[Test]
    public function it_can_load_safety_events_fixture(): void
    {
        $events = Fixtures::safetyEvents();

        $this->assertArrayHasKey('data', $events);
        $this->assertIsArray($events['data']);
        $this->assertNotEmpty($events['data']);
        $this->assertArrayHasKey('behaviorLabel', $events['data'][0]);
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
    }

    #[Test]
    public function it_can_load_users_fixture(): void
    {
        $users = Fixtures::users();

        $this->assertArrayHasKey('data', $users);
        $this->assertIsArray($users['data']);
        $this->assertNotEmpty($users['data']);
        $this->assertArrayHasKey('email', $users['data'][0]);
    }

    #[Test]
    public function it_can_load_vehicle_stats_fixture(): void
    {
        $stats = Fixtures::vehicleStats();

        $this->assertArrayHasKey('data', $stats);
        $this->assertIsArray($stats['data']);
        $this->assertNotEmpty($stats['data']);
        $this->assertArrayHasKey('gps', $stats['data'][0]);
    }

    #[Test]
    public function it_can_load_vehicles_fixture(): void
    {
        $vehicles = Fixtures::vehicles();

        $this->assertArrayHasKey('data', $vehicles);
        $this->assertIsArray($vehicles['data']);
        $this->assertNotEmpty($vehicles['data']);
        $this->assertArrayHasKey('id', $vehicles['data'][0]);
        $this->assertSame('vehicle-001', $vehicles['data'][0]['id']);
    }

    #[Test]
    public function it_can_load_webhooks_fixture(): void
    {
        $webhooks = Fixtures::webhooks();

        $this->assertArrayHasKey('data', $webhooks);
        $this->assertIsArray($webhooks['data']);
        $this->assertNotEmpty($webhooks['data']);
        $this->assertArrayHasKey('eventTypes', $webhooks['data'][0]);
    }

    #[Test]
    public function it_throws_exception_for_missing_fixture(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Fixture file not found: nonexistent.json');

        Fixtures::load('nonexistent.json');
    }
}
