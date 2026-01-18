<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Safety;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Safety\CoachingSession;

/**
 * Unit tests for the CoachingSession entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class CoachingSessionTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $session = new CoachingSession([
            'id'            => 'f5271458-21f9-4a9f-a290-780c6d8840ff',
            'sessionStatus' => 'completed',
            'coachingType'  => 'withManager',
            'dueAtTime'     => '2024-04-15T19:08:25Z',
        ]);

        $this->assertSame('f5271458-21f9-4a9f-a290-780c6d8840ff', $session->id);
        $this->assertSame('completed', $session->sessionStatus);
        $this->assertSame('withManager', $session->coachingType);
        $this->assertSame('2024-04-15T19:08:25Z', $session->dueAtTime);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $session = CoachingSession::make([
            'id' => 'f5271458-21f9-4a9f-a290-780c6d8840ff',
        ]);

        $this->assertInstanceOf(CoachingSession::class, $session);
        $this->assertSame('f5271458-21f9-4a9f-a290-780c6d8840ff', $session->getId());
    }

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
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'            => 'f5271458-21f9-4a9f-a290-780c6d8840ff',
            'sessionStatus' => 'completed',
        ];

        $session = new CoachingSession($data);

        $this->assertSame($data, $session->toArray());
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
                ['type' => 'harshBrake'],
                ['type' => 'speeding'],
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
    public function it_extends_entity(): void
    {
        $session = new CoachingSession;

        $this->assertInstanceOf(Entity::class, $session);
    }
}
