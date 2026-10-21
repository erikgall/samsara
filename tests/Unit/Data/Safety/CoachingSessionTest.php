<?php

namespace Samsara\Tests\Unit\Data\Safety;

use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Safety\CoachingSession;

/**
 * Unit tests for the CoachingSession entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class CoachingSessionTest extends TestCase
{
    #[Test]
    public function it_can_check_if_completed(): void
    {
        $session = new CoachingSession([
            'sessionStatus' => 'completed',
        ]);

        $this->assertTrue($session->isCompleted());
        $this->assertFalse($session->isUpcoming());
        $this->assertFalse($session->isDeleted());
    }

    #[Test]
    public function it_can_check_if_deleted(): void
    {
        $session = new CoachingSession([
            'sessionStatus' => 'deleted',
        ]);

        $this->assertTrue($session->isDeleted());
        $this->assertFalse($session->isCompleted());
    }

    #[Test]
    public function it_can_check_if_self_coaching(): void
    {
        $session = new CoachingSession([
            'coachingType' => 'selfCoaching',
        ]);

        $this->assertTrue($session->isSelfCoaching());
        $this->assertFalse($session->isWithManager());
    }

    #[Test]
    public function it_can_check_if_upcoming(): void
    {
        $session = new CoachingSession([
            'sessionStatus' => 'upcoming',
        ]);

        $this->assertTrue($session->isUpcoming());
        $this->assertFalse($session->isCompleted());
    }

    #[Test]
    public function it_can_check_if_with_manager(): void
    {
        $session = new CoachingSession([
            'coachingType' => 'withManager',
        ]);

        $this->assertTrue($session->isWithManager());
        $this->assertFalse($session->isSelfCoaching());
    }

    #[Test]
    public function it_can_have_assigned_coach(): void
    {
        $session = new CoachingSession([
            'assignedCoachId' => '45646',
        ]);

        $this->assertSame('45646', $session->assignedCoachId);
    }

    #[Test]
    public function it_can_have_behaviors(): void
    {
        $session = new CoachingSession([
            'behaviors' => [
                ['coachableBehaviorType' => 'braking'],
                ['coachableBehaviorType' => 'speeding'],
            ],
        ]);

        $this->assertCount(2, $session->behaviors);
    }

    #[Test]
    public function it_can_have_completed_coach(): void
    {
        $session = new CoachingSession([
            'completedCoachId' => '45646',
            'completedAtTime'  => '2024-04-14T10:30:00Z',
        ]);

        $this->assertSame('45646', $session->completedCoachId);
        $this->assertSame('2024-04-14T10:30:00Z', $session->completedAtTime);
    }

    #[Test]
    public function it_can_have_driver(): void
    {
        $session = new CoachingSession([
            'driver' => [
                'id'   => 'driver-1',
                'name' => 'John Doe',
            ],
        ]);

        $this->assertSame('driver-1', $session->driver['id']);
        $this->assertSame('John Doe', $session->driver['name']);
    }

    #[Test]
    public function it_can_have_session_note(): void
    {
        $session = new CoachingSession([
            'sessionNote' => 'Need to wear seatbelt even for short trips.',
        ]);

        $this->assertSame('Need to wear seatbelt even for short trips.', $session->sessionNote);
    }

    #[Test]
    public function it_matches_an_exact_coachable_behavior_type(): void
    {
        $session = new CoachingSession([
            'behaviors' => [
                ['coachableBehaviorType' => 'braking'],
                ['coachableBehaviorType' => 'speeding'],
            ],
        ]);

        $this->assertTrue($session->hasCoachableBehavior('speeding'));
        $this->assertFalse($session->hasCoachableBehavior('Speeding'));
        $this->assertFalse($session->hasCoachableBehavior('mobileUsage'));

        foreach ([new CoachingSession, new CoachingSession(['behaviors' => null]), new CoachingSession(['behaviors' => []])] as $empty) {
            $this->assertFalse($empty->hasCoachableBehavior('speeding'));
        }
    }
}
