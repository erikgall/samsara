<?php

namespace Samsara\Tests\Unit\Data\Route;

use Samsara\Tests\TestCase;
use Samsara\Data\Route\RouteSettings;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the RouteSettings entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class RouteSettingsTest extends TestCase
{
    #[Test]
    public function it_can_check_if_completes_on_arrive_last_stop(): void
    {
        $settings = new RouteSettings([
            'routeCompletionCondition' => 'arriveLastStop',
        ]);

        $this->assertTrue($settings->completesOnArriveLastStop());
        $this->assertFalse($settings->completesOnDepartLastStop());
    }

    #[Test]
    public function it_can_check_if_completes_on_depart_last_stop(): void
    {
        $settings = new RouteSettings([
            'routeCompletionCondition' => 'departLastStop',
        ]);

        $this->assertTrue($settings->completesOnDepartLastStop());
        $this->assertFalse($settings->completesOnArriveLastStop());
    }

    #[Test]
    public function it_can_check_if_sequencing_is_by_scheduled_time(): void
    {
        $settings = new RouteSettings([
            'sequencingMethod' => 'scheduledArrivalTime',
        ]);

        $this->assertTrue($settings->isSequencedByScheduledTime());
        $this->assertFalse($settings->isSequencedManually());
    }

    #[Test]
    public function it_can_check_if_sequencing_is_manual(): void
    {
        $settings = new RouteSettings([
            'sequencingMethod' => 'manual',
        ]);

        $this->assertTrue($settings->isSequencedManually());
        $this->assertFalse($settings->isSequencedByScheduledTime());
    }

    #[Test]
    public function it_can_check_if_starts_on_arrive_first_stop(): void
    {
        $settings = new RouteSettings([
            'routeStartingCondition' => 'arriveFirstStop',
        ]);

        $this->assertTrue($settings->startsOnArriveFirstStop());
        $this->assertFalse($settings->startsOnDepartFirstStop());
    }

    #[Test]
    public function it_can_check_if_starts_on_depart_first_stop(): void
    {
        $settings = new RouteSettings([
            'routeStartingCondition' => 'departFirstStop',
        ]);

        $this->assertTrue($settings->startsOnDepartFirstStop());
        $this->assertFalse($settings->startsOnArriveFirstStop());
    }
}
