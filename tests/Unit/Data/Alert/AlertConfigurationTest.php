<?php

namespace Samsara\Tests\Unit\Data\Alert;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Alert\AlertConfiguration;

/**
 * Unit tests for the AlertConfiguration entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AlertConfigurationTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $alert = new AlertConfiguration([
            'id'            => '12345',
            'name'          => 'Speed Alert',
            'isEnabled'     => true,
            'createdAtTime' => '2024-04-10T07:06:25Z',
            'actions'       => [],
            'scope'         => [],
            'triggers'      => [],
        ]);

        $this->assertSame('12345', $alert->id);
        $this->assertSame('Speed Alert', $alert->name);
        $this->assertTrue($alert->isEnabled);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $alert = AlertConfiguration::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(AlertConfiguration::class, $alert);
        $this->assertSame('12345', $alert->getId());
    }

    #[Test]
    public function it_can_check_if_enabled(): void
    {
        $alert = new AlertConfiguration([
            'isEnabled' => true,
        ]);

        $this->assertTrue($alert->isEnabled());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'        => '12345',
            'name'      => 'Speed Alert',
            'alertType' => 'speed',
        ];

        $alert = new AlertConfiguration($data);

        $this->assertSame($data, $alert->toArray());
    }

    #[Test]
    public function it_can_get_an_external_id(): void
    {
        $alert = new AlertConfiguration([
            'externalIds' => ['maintenanceId' => 'alert-123', 'empty' => ''],
        ]);

        $this->assertSame('alert-123', $alert->getExternalId('maintenanceId'));
        $this->assertSame('', $alert->getExternalId('empty'));
        $this->assertNull($alert->getExternalId('missing'));
        $this->assertNull((new AlertConfiguration(['externalIds' => null]))->getExternalId('maintenanceId'));
    }

    #[Test]
    public function it_can_have_operational_settings(): void
    {
        $alert = new AlertConfiguration([
            'operationalSettings' => [
                'timeRangeType' => 'activeBetween',
            ],
        ]);

        $this->assertSame('activeBetween', $alert->operationalSettings['timeRangeType']);
    }

    #[Test]
    public function it_can_have_triggers(): void
    {
        $alert = new AlertConfiguration([
            'triggers' => [
                ['triggerType' => 'deviceLocationInsideGeofence'],
                ['triggerType' => 'deviceSpeedAbove'],
            ],
        ]);

        $this->assertCount(2, $alert->triggers);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $alert = new AlertConfiguration;

        $this->assertInstanceOf(Entity::class, $alert);
    }

    #[Test]
    public function it_returns_false_when_enabled_not_set(): void
    {
        $alert = new AlertConfiguration;

        $this->assertFalse($alert->isEnabled());
    }

    #[Test]
    public function it_returns_false_when_not_enabled(): void
    {
        $alert = new AlertConfiguration([
            'isEnabled' => false,
        ]);

        $this->assertFalse($alert->isEnabled());
    }

    #[Test]
    public function legacy_fuel_level_type_check_remains_backwards_compatible(): void
    {
        $alert = new AlertConfiguration([
            'alertType' => 'fuelLevel',
        ]);

        $this->assertTrue($alert->isFuelLevelType());
    }

    #[Test]
    public function legacy_geofence_type_check_remains_backwards_compatible(): void
    {
        $alert = new AlertConfiguration([
            'alertType' => 'geofence',
        ]);

        $this->assertTrue($alert->isGeofenceType());
    }

    #[Test]
    public function legacy_idle_type_check_remains_backwards_compatible(): void
    {
        $alert = new AlertConfiguration([
            'alertType' => 'idle',
        ]);

        $this->assertTrue($alert->isIdleType());
    }

    #[Test]
    public function legacy_speed_type_check_remains_backwards_compatible(): void
    {
        $alert = new AlertConfiguration([
            'alertType' => 'speed',
        ]);

        $this->assertTrue($alert->isSpeedType());
        $this->assertFalse($alert->isGeofenceType());
    }
}
