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
            'configurationId' => 'config-1',
            'isResolved'      => false,
            'happenedAtTime'  => '2024-04-10T07:06:25Z',
            'incidentUrl'     => 'https://cloud.samsara.com/incidents/12345',
        ]);

        $this->assertSame('config-1', $incident->configurationId);
        $this->assertFalse($incident->isResolved);
        $this->assertSame('2024-04-10T07:06:25Z', $incident->happenedAtTime);
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
    public function it_can_check_if_resolved(): void
    {
        $incident = new AlertIncident([
            'isResolved' => true,
        ]);

        $this->assertTrue($incident->isResolved());
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
    public function it_can_have_an_incident_url(): void
    {
        $incident = new AlertIncident([
            'incidentUrl' => 'https://cloud.samsara.com/o/1/fleet/workflows/123',
        ]);

        $this->assertSame('https://cloud.samsara.com/o/1/fleet/workflows/123', $incident->incidentUrl);
    }

    #[Test]
    public function it_can_have_conditions(): void
    {
        $incident = new AlertIncident([
            'conditions' => [
                ['description' => 'Vehicle speed exceeded threshold'],
            ],
        ]);

        $this->assertCount(1, $incident->conditions);
    }

    #[Test]
    public function it_can_have_resolution_timestamps(): void
    {
        $incident = new AlertIncident([
            'resolvedAtTime' => '2024-04-10T08:00:00Z',
            'updatedAtTime'  => '2024-04-10T08:01:00Z',
        ]);

        $this->assertSame('2024-04-10T08:00:00Z', $incident->resolvedAtTime);
        $this->assertSame('2024-04-10T08:01:00Z', $incident->updatedAtTime);
    }

    #[Test]
    public function it_can_have_timestamps(): void
    {
        $incident = new AlertIncident([
            'happenedAtTime' => '2024-04-10T07:06:25Z',
            'resolvedAtTime' => '2024-04-10T08:00:00Z',
        ]);

        $this->assertSame('2024-04-10T07:06:25Z', $incident->happenedAtTime);
        $this->assertSame('2024-04-10T08:00:00Z', $incident->resolvedAtTime);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $incident = new AlertIncident;

        $this->assertInstanceOf(Entity::class, $incident);
    }

    #[Test]
    public function legacy_dismissed_status_check_remains_backwards_compatible(): void
    {
        $incident = new AlertIncident([
            'status' => 'dismissed',
        ]);

        $this->assertTrue($incident->isDismissed());
        $this->assertFalse($incident->isTriggered());
    }

    #[Test]
    public function legacy_triggered_status_check_remains_backwards_compatible(): void
    {
        $incident = new AlertIncident([
            'status' => 'triggered',
        ]);

        $this->assertTrue($incident->isTriggered());
        $this->assertFalse($incident->isResolved());
    }
}
