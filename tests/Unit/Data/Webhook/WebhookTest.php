<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Webhook;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Webhook\Webhook;

/**
 * Unit tests for the Webhook entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class WebhookTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $webhook = new Webhook([
            'id'         => '12345',
            'name'       => 'Fleet Alerts Webhook',
            'url'        => 'https://example.com/webhooks/samsara',
            'secret'     => 'webhook-secret-key',
            'eventTypes' => ['vehicleCreated', 'vehicleUpdated'],
        ]);

        $this->assertSame('12345', $webhook->id);
        $this->assertSame('Fleet Alerts Webhook', $webhook->name);
        $this->assertSame('https://example.com/webhooks/samsara', $webhook->url);
        $this->assertSame('webhook-secret-key', $webhook->secret);
        $this->assertCount(2, $webhook->eventTypes);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $webhook = Webhook::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertSame('12345', $webhook->getId());
    }

    #[Test]
    public function it_can_check_if_active(): void
    {
        $webhook = new Webhook([
            'status' => 'active',
        ]);

        $this->assertTrue($webhook->isActive());
        $this->assertFalse($webhook->isPaused());
    }

    #[Test]
    public function it_can_check_if_has_event_type(): void
    {
        $webhook = new Webhook([
            'eventTypes' => ['vehicleCreated', 'vehicleUpdated'],
        ]);

        $this->assertTrue($webhook->hasEventType('vehicleCreated'));
        $this->assertFalse($webhook->hasEventType('driverCreated'));
    }

    #[Test]
    public function it_can_check_if_paused(): void
    {
        $webhook = new Webhook([
            'status' => 'paused',
        ]);

        $this->assertTrue($webhook->isPaused());
        $this->assertFalse($webhook->isActive());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '12345',
            'name' => 'Fleet Alerts Webhook',
            'url'  => 'https://example.com/webhooks/samsara',
        ];

        $webhook = new Webhook($data);

        $this->assertSame($data, $webhook->toArray());
    }

    #[Test]
    public function it_can_have_custom_headers(): void
    {
        $webhook = new Webhook([
            'customHeaders' => [
                ['name' => 'X-Custom-Header', 'value' => 'custom-value'],
            ],
        ]);

        $this->assertCount(1, $webhook->customHeaders);
        $this->assertSame('X-Custom-Header', $webhook->customHeaders[0]['name']);
    }

    #[Test]
    public function it_can_have_timestamps(): void
    {
        $webhook = new Webhook([
            'createdAtTime' => '2024-04-10T07:06:25Z',
            'updatedAtTime' => '2024-04-11T10:00:00Z',
        ]);

        $this->assertSame('2024-04-10T07:06:25Z', $webhook->createdAtTime);
        $this->assertSame('2024-04-11T10:00:00Z', $webhook->updatedAtTime);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $webhook = new Webhook;

        $this->assertInstanceOf(Entity::class, $webhook);
    }

    #[Test]
    public function it_returns_false_for_has_event_type_when_not_set(): void
    {
        $webhook = new Webhook;

        $this->assertFalse($webhook->hasEventType('vehicleCreated'));
    }
}
