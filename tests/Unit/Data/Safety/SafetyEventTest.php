<?php

namespace Samsara\Tests\Unit\Data\Safety;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Safety\SafetyEvent;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the SafetyEvent entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SafetyEventTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $event = new SafetyEvent([
            'id'   => '212014918174029-1550954461759',
            'time' => '2024-04-10T19:08:25.455Z',
        ]);

        $this->assertSame('212014918174029-1550954461759', $event->id);
        $this->assertSame('2024-04-10T19:08:25.455Z', $event->time);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $event = SafetyEvent::make([
            'id' => '212014918174029-1550954461759',
        ]);

        $this->assertInstanceOf(SafetyEvent::class, $event);
        $this->assertSame('212014918174029-1550954461759', $event->getId());
    }

    #[Test]
    public function it_can_check_if_has_video(): void
    {
        $event = new SafetyEvent([
            'downloadForwardVideoUrl' => 'https://example.com/forward.mp4',
        ]);

        $this->assertTrue($event->hasVideo());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '212014918174029-1550954461759',
            'time' => '2024-04-10T19:08:25.455Z',
        ];

        $event = new SafetyEvent($data);

        $this->assertSame($data, $event->toArray());
    }

    #[Test]
    public function it_can_have_behavior_labels(): void
    {
        $event = new SafetyEvent([
            'behaviorLabels' => [
                ['label' => 'harshBrake', 'source' => 'system'],
                ['label' => 'speeding', 'source' => 'system'],
            ],
        ]);

        $this->assertCount(2, $event->behaviorLabels);
    }

    #[Test]
    public function it_can_have_coaching_state(): void
    {
        $event = new SafetyEvent([
            'coachingState' => 'needsReview',
        ]);

        $this->assertSame('needsReview', $event->coachingState);
    }

    #[Test]
    public function it_can_have_driver(): void
    {
        $event = new SafetyEvent([
            'driver' => [
                'id'   => 'driver-1',
                'name' => 'John Doe',
            ],
        ]);

        $this->assertSame('driver-1', $event->driver['id']);
        $this->assertSame('John Doe', $event->driver['name']);
    }

    #[Test]
    public function it_can_have_location(): void
    {
        $event = new SafetyEvent([
            'location' => [
                'latitude'  => 37.7749,
                'longitude' => -122.4194,
            ],
        ]);

        $this->assertSame(37.7749, $event->location['latitude']);
        $this->assertSame(-122.4194, $event->location['longitude']);
    }

    #[Test]
    public function it_can_have_max_acceleration(): void
    {
        $event = new SafetyEvent([
            'maxAccelerationGForce' => 0.85,
        ]);

        $this->assertSame(0.85, $event->maxAccelerationGForce);
    }

    #[Test]
    public function it_can_have_vehicle(): void
    {
        $event = new SafetyEvent([
            'vehicle' => [
                'id'   => 'vehicle-1',
                'name' => 'Truck 42',
            ],
        ]);

        $this->assertSame('vehicle-1', $event->vehicle['id']);
        $this->assertSame('Truck 42', $event->vehicle['name']);
    }

    #[Test]
    public function it_can_have_video_urls(): void
    {
        $event = new SafetyEvent([
            'downloadForwardVideoUrl' => 'https://example.com/forward.mp4',
            'downloadInwardVideoUrl'  => 'https://example.com/inward.mp4',
        ]);

        $this->assertSame('https://example.com/forward.mp4', $event->downloadForwardVideoUrl);
        $this->assertSame('https://example.com/inward.mp4', $event->downloadInwardVideoUrl);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $event = new SafetyEvent;

        $this->assertInstanceOf(Entity::class, $event);
    }

    #[Test]
    public function it_returns_false_when_no_video(): void
    {
        $event = new SafetyEvent;

        $this->assertFalse($event->hasVideo());
    }
}
