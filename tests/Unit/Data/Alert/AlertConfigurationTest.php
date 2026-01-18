<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Alert;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Alert\AlertConfiguration;

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
            'id'          => '12345',
            'name'        => 'Speed Alert',
            'alertType'   => 'speed',
            'enabled'     => true,
            'description' => 'Alerts when vehicle exceeds speed limit',
        ]);

        $this->assertSame('12345', $alert->id);
        $this->assertSame('Speed Alert', $alert->name);
        $this->assertSame('speed', $alert->alertType);
        $this->assertTrue($alert->enabled);
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
            'enabled' => true,
        ]);

        $this->assertTrue($alert->isEnabled());
    }

    #[Test]
    public function it_can_check_if_fuel_level_type(): void
    {
        $alert = new AlertConfiguration([
            'alertType' => 'fuelLevel',
        ]);

        $this->assertTrue($alert->isFuelLevelType());
    }

    #[Test]
    public function it_can_check_if_geofence_type(): void
    {
        $alert = new AlertConfiguration([
            'alertType' => 'geofence',
        ]);

        $this->assertTrue($alert->isGeofenceType());
    }

    #[Test]
    public function it_can_check_if_idle_type(): void
    {
        $alert = new AlertConfiguration([
            'alertType' => 'idle',
        ]);

        $this->assertTrue($alert->isIdleType());
    }

    #[Test]
    public function it_can_check_if_speed_type(): void
    {
        $alert = new AlertConfiguration([
            'alertType' => 'speed',
        ]);

        $this->assertTrue($alert->isSpeedType());
        $this->assertFalse($alert->isGeofenceType());
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
    public function it_can_have_notification_settings(): void
    {
        $alert = new AlertConfiguration([
            'notificationSettings' => [
                'email' => true,
                'sms'   => false,
            ],
        ]);

        $this->assertTrue($alert->notificationSettings['email']);
        $this->assertFalse($alert->notificationSettings['sms']);
    }

    #[Test]
    public function it_can_have_tags(): void
    {
        $alert = new AlertConfiguration([
            'tagIds' => ['tag-1', 'tag-2'],
        ]);

        $this->assertCount(2, $alert->tagIds);
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
            'enabled' => false,
        ]);

        $this->assertFalse($alert->isEnabled());
    }
}
