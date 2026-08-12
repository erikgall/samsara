<?php

namespace Samsara\Tests\Unit\Data\Webhook;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Webhook\Webhook;
use PHPUnit\Framework\Attributes\Test;

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
            'secretKey'  => 'webhook-secret-key',
            'version'    => '2021-06-09',
            'eventTypes' => ['VehicleCreated', 'VehicleUpdated'],
        ]);

        $this->assertSame('12345', $webhook->id);
        $this->assertSame('Fleet Alerts Webhook', $webhook->name);
        $this->assertSame('https://example.com/webhooks/samsara', $webhook->url);
        $this->assertSame('webhook-secret-key', $webhook->secretKey);
        $this->assertSame('2021-06-09', $webhook->version);
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
    public function it_can_check_if_has_event_type(): void
    {
        $webhook = new Webhook([
            'eventTypes' => ['VehicleCreated', 'VehicleUpdated'],
        ]);

        $this->assertTrue($webhook->hasEventType('VehicleCreated'));
        $this->assertFalse($webhook->hasEventType('vehicleCreated'));
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
                ['key' => 'X-Custom-Header', 'value' => 'custom-value'],
            ],
        ]);

        $this->assertCount(1, $webhook->customHeaders);
        $this->assertSame('X-Custom-Header', $webhook->customHeaders[0]['key']);
    }

    #[Test]
    public function it_does_not_expose_status_helpers_absent_from_the_current_contract(): void
    {
        $webhook = new Webhook;

        $this->assertFalse(method_exists($webhook, 'isActive'));
        $this->assertFalse(method_exists($webhook, 'isPaused'));
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $webhook = new Webhook;

        $this->assertInstanceOf(Entity::class, $webhook);
    }

    #[Test]
    public function it_gets_the_first_exact_custom_header_value(): void
    {
        $webhook = new Webhook([
            'customHeaders' => [
                ['key' => 'X-Region', 'value' => 'east'],
                ['key' => 'X-Region', 'value' => 'west'],
            ],
        ]);

        $this->assertSame('east', $webhook->getCustomHeader('X-Region'));
        $this->assertNull($webhook->getCustomHeader('x-region'));
        $this->assertNull($webhook->getCustomHeader('Missing'));
        $this->assertNull((new Webhook)->getCustomHeader('X-Region'));
        $this->assertNull((new Webhook(['customHeaders' => null]))->getCustomHeader('X-Region'));
    }

    #[Test]
    public function it_returns_false_for_has_event_type_when_not_set(): void
    {
        $webhook = new Webhook;

        $this->assertFalse($webhook->hasEventType('VehicleCreated'));
    }
}
