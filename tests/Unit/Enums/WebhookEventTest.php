<?php

namespace Samsara\Tests\Unit\Enums;

use Samsara\Tests\TestCase;
use Samsara\Enums\WebhookEvent;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the WebhookEvent enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class WebhookEventTest extends TestCase
{
    #[Test]
    public function it_can_collect_all_cases(): void
    {
        $collection = WebhookEvent::collect();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertContains(WebhookEvent::DRIVER_CREATED, $collection->all());
    }

    #[Test]
    public function it_can_get_all_values_as_array(): void
    {
        $all = WebhookEvent::all();

        $this->assertIsArray($all);
        $this->assertContains('DriverCreated', $all);
    }

    #[Test]
    public function it_can_get_values_as_collection(): void
    {
        $values = WebhookEvent::values();

        $this->assertInstanceOf(Collection::class, $values);
        $this->assertContains('DriverCreated', $values->all());
    }

    #[Test]
    public function it_has_all_expected_cases(): void
    {
        $cases = WebhookEvent::cases();

        $this->assertGreaterThan(50, count($cases));
    }

    #[Test]
    public function it_has_driver_created_case(): void
    {
        $this->assertSame('DriverCreated', WebhookEvent::DRIVER_CREATED->value);
    }

    #[Test]
    public function it_has_geofence_entry_case(): void
    {
        $this->assertSame('GeofenceEntry', WebhookEvent::GEOFENCE_ENTRY->value);
    }

    #[Test]
    public function it_has_vehicle_created_case(): void
    {
        $this->assertSame('VehicleCreated', WebhookEvent::VEHICLE_CREATED->value);
    }
}
