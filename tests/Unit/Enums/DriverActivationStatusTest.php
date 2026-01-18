<?php

namespace ErikGall\Samsara\Tests\Unit\Enums;

use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Enums\DriverActivationStatus;

/**
 * Unit tests for the DriverActivationStatus enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DriverActivationStatusTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string(): void
    {
        $status = DriverActivationStatus::from('active');

        $this->assertSame(DriverActivationStatus::ACTIVE, $status);
    }

    #[Test]
    public function it_can_try_from_string(): void
    {
        $status = DriverActivationStatus::tryFrom('deactivated');

        $this->assertSame(DriverActivationStatus::DEACTIVATED, $status);
    }

    #[Test]
    public function it_has_active_case(): void
    {
        $this->assertSame('active', DriverActivationStatus::ACTIVE->value);
    }

    #[Test]
    public function it_has_deactivated_case(): void
    {
        $this->assertSame('deactivated', DriverActivationStatus::DEACTIVATED->value);
    }

    #[Test]
    public function it_returns_null_for_invalid_value(): void
    {
        $status = DriverActivationStatus::tryFrom('invalid');

        $this->assertNull($status);
    }
}
