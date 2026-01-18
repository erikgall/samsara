<?php

namespace Samsara\Tests\Unit\Data\Alert;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Alert\AlertIncident;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the AlertIncident entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AlertIncidentTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $incident = new AlertIncident([
            'id'                   => '12345',
            'alertConfigurationId' => 'config-1',
            'status'               => 'triggered',
            'triggeredAtTime'      => '2024-04-10T07:06:25Z',
        ]);

        $this->assertSame('12345', $incident->id);
        $this->assertSame('config-1', $incident->alertConfigurationId);
        $this->assertSame('triggered', $incident->status);
        $this->assertSame('2024-04-10T07:06:25Z', $incident->triggeredAtTime);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $incident = AlertIncident::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(AlertIncident::class, $incident);
        $this->assertSame('12345', $incident->getId());
    }

    #[Test]
    public function it_can_check_if_dismissed(): void
    {
        $incident = new AlertIncident([
            'status' => 'dismissed',
        ]);

        $this->assertTrue($incident->isDismissed());
        $this->assertFalse($incident->isTriggered());
    }

    #[Test]
    public function it_can_check_if_resolved(): void
    {
        $incident = new AlertIncident([
            'status' => 'resolved',
        ]);

        $this->assertTrue($incident->isResolved());
    }

    #[Test]
    public function it_can_check_if_triggered(): void
    {
        $incident = new AlertIncident([
            'status' => 'triggered',
        ]);

        $this->assertTrue($incident->isTriggered());
        $this->assertFalse($incident->isResolved());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'     => '12345',
            'status' => 'triggered',
        ];

        $incident = new AlertIncident($data);

        $this->assertSame($data, $incident->toArray());
    }

    #[Test]
    public function it_can_have_driver(): void
    {
        $incident = new AlertIncident([
            'driver' => [
                'id'   => 'driver-1',
                'name' => 'John Smith',
            ],
        ]);

        $this->assertSame('driver-1', $incident->driver['id']);
    }

    #[Test]
    public function it_can_have_location(): void
    {
        $incident = new AlertIncident([
            'location' => [
                'latitude'  => 37.7749,
                'longitude' => -122.4194,
            ],
        ]);

        $this->assertSame(37.7749, $incident->location['latitude']);
    }

    #[Test]
    public function it_can_have_timestamps(): void
    {
        $incident = new AlertIncident([
            'triggeredAtTime' => '2024-04-10T07:06:25Z',
            'resolvedAtTime'  => '2024-04-10T08:00:00Z',
        ]);

        $this->assertSame('2024-04-10T07:06:25Z', $incident->triggeredAtTime);
        $this->assertSame('2024-04-10T08:00:00Z', $incident->resolvedAtTime);
    }

    #[Test]
    public function it_can_have_vehicle(): void
    {
        $incident = new AlertIncident([
            'vehicle' => [
                'id'   => 'vehicle-1',
                'name' => 'Truck 42',
            ],
        ]);

        $this->assertSame('vehicle-1', $incident->vehicle['id']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $incident = new AlertIncident;

        $this->assertInstanceOf(Entity::class, $incident);
    }
}
