<?php

namespace Samsara\Tests\Unit\Data\Webhook;

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
    public function it_can_check_if_has_event_type(): void
    {
        $webhook = new Webhook([
            'eventTypes' => ['VehicleCreated', 'VehicleUpdated'],
        ]);

        $this->assertTrue($webhook->hasEventType('VehicleCreated'));
        $this->assertFalse($webhook->hasEventType('vehicleCreated'));
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
