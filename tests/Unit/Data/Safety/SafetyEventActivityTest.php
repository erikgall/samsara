<?php

namespace Samsara\Tests\Unit\Data\Safety;

use Samsara\Tests\TestCase;
use Samsara\Data\Safety\SafetyEvent;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Safety\SafetyEventActivity;

class SafetyEventActivityTest extends TestCase
{
    #[Test]
    public function it_checks_each_documented_activity_type(): void
    {
        $typeMethods = [
            'BehaviorLabelActivityType'     => 'isBehaviorLabelActivity',
            'CoachingStateActivityType'     => 'isCoachingStateActivity',
            'CreateSafetyEventActivityType' => 'isCreateSafetyEventActivity',
        ];

        foreach ($typeMethods as $type => $expectedMethod) {
            $activity = new SafetyEventActivity(['type' => $type]);

            foreach ($typeMethods as $method) {
                $this->assertSame($method === $expectedMethod, $activity->{$method}());
            }
        }

        foreach ([new SafetyEventActivity, new SafetyEventActivity(['type' => null]), new SafetyEventActivity(['type' => 'unknown'])] as $activity) {
            foreach ($typeMethods as $method) {
                $this->assertFalse($activity->{$method}());
            }
        }
    }

    #[Test]
    public function it_gets_the_nested_safety_event(): void
    {
        $activity = new SafetyEventActivity([
            'safetyEvent' => ['id' => 'event-1'],
        ]);

        $event = $activity->getSafetyEvent();

        $this->assertInstanceOf(SafetyEvent::class, $event);
        $this->assertSame('event-1', $event->getId());
    }
}
