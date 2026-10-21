<?php

namespace Samsara\Tests\Unit\Data\Maintenance;

use Samsara\Tests\TestCase;
use Samsara\Data\Maintenance\Defect;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the Defect entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DefectTest extends TestCase
{
    #[Test]
    public function it_can_check_if_resolved(): void
    {
        $defect = new Defect([
            'isResolved'     => true,
            'resolvedAtTime' => '2024-04-11T10:00:00Z',
        ]);

        $this->assertTrue($defect->isResolved());
        $this->assertSame('2024-04-11T10:00:00Z', $defect->resolvedAtTime);
    }

    #[Test]
    public function it_can_check_if_safe(): void
    {
        $defect = new Defect([
            'defectSafetyStatus' => 'safe',
        ]);

        $this->assertTrue($defect->isSafe());
        $this->assertFalse($defect->isUnsafe());
    }

    #[Test]
    public function it_can_check_if_unsafe(): void
    {
        $defect = new Defect([
            'defectSafetyStatus' => 'unsafe',
        ]);

        $this->assertTrue($defect->isUnsafe());
        $this->assertFalse($defect->isSafe());
    }

    #[Test]
    public function it_can_have_mechanic_notes(): void
    {
        $defect = new Defect([
            'mechanicNotes'              => 'Fixed the air compressor',
            'mechanicNotesUpdatedAtTime' => '2024-04-11T10:00:00Z',
        ]);

        $this->assertSame('Fixed the air compressor', $defect->mechanicNotes);
        $this->assertSame('2024-04-11T10:00:00Z', $defect->mechanicNotesUpdatedAtTime);
    }

    #[Test]
    public function it_can_have_resolved_by(): void
    {
        $defect = new Defect([
            'resolvedBy' => [
                'id'   => 'user-1',
                'name' => 'John Mechanic',
            ],
        ]);

        $this->assertSame('user-1', $defect->resolvedBy['id']);
    }

    #[Test]
    public function it_can_have_trailer(): void
    {
        $defect = new Defect([
            'trailer' => [
                'id'   => 'trailer-1',
                'name' => 'Trailer A',
            ],
        ]);

        $this->assertSame('trailer-1', $defect->trailer['id']);
    }

    #[Test]
    public function it_can_have_vehicle(): void
    {
        $defect = new Defect([
            'vehicle' => [
                'id'   => 'vehicle-1',
                'name' => 'Truck 42',
            ],
        ]);

        $this->assertSame('vehicle-1', $defect->vehicle['id']);
        $this->assertSame('Truck 42', $defect->vehicle['name']);
    }

    #[Test]
    public function it_returns_false_for_unrecognized_or_missing_safety_statuses(): void
    {
        foreach ([null, '', 'unknown'] as $status) {
            $defect = new Defect([
                'defectSafetyStatus' => $status,
            ]);

            $this->assertFalse($defect->isSafe());
            $this->assertFalse($defect->isUnsafe());
        }

        $defect = new Defect;

        $this->assertFalse($defect->isSafe());
        $this->assertFalse($defect->isUnsafe());
    }

    #[Test]
    public function it_returns_false_when_not_resolved(): void
    {
        $defect = new Defect([
            'isResolved' => false,
        ]);

        $this->assertFalse($defect->isResolved());
    }

    #[Test]
    public function it_returns_false_when_resolved_not_set(): void
    {
        $defect = new Defect;

        $this->assertFalse($defect->isResolved());
    }
}
