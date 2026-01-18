<?php

namespace Samsara\Tests\Unit\Concerns;

use Carbon\Carbon;
use DateTimeImmutable;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Concerns\InteractsWithTime;

/**
 * Unit tests for the InteractsWithTime trait.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class InteractsWithTimeTest extends TestCase
{
    #[Test]
    public function it_formats_carbon_instance_to_rfc3339(): void
    {
        $class = new TimeTestClass;
        $carbon = Carbon::parse('2024-01-15 14:30:00', 'UTC');

        $result = $class->testFormatTime($carbon);

        $this->assertSame('2024-01-15T14:30:00+00:00', $result);
    }

    #[Test]
    public function it_formats_datetime_immutable_to_rfc3339(): void
    {
        $class = new TimeTestClass;
        $datetime = new DateTimeImmutable('2024-01-15 14:30:00', new \DateTimeZone('UTC'));

        $result = $class->testFormatTime($datetime);

        $this->assertSame('2024-01-15T14:30:00+00:00', $result);
    }

    #[Test]
    public function it_parses_string_to_carbon(): void
    {
        $class = new TimeTestClass;

        $result = $class->testParseTime('2024-01-15T14:30:00Z');

        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertSame('2024-01-15 14:30:00', $result->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function it_returns_carbon_as_is(): void
    {
        $class = new TimeTestClass;
        $carbon = Carbon::parse('2024-01-15 14:30:00');

        $result = $class->testParseTime($carbon);

        $this->assertSame($carbon, $result);
    }

    #[Test]
    public function it_returns_string_as_is(): void
    {
        $class = new TimeTestClass;
        $timeString = '2024-01-15T14:30:00Z';

        $result = $class->testFormatTime($timeString);

        $this->assertSame('2024-01-15T14:30:00Z', $result);
    }
}

/**
 * Test class for InteractsWithTime trait.
 */
class TimeTestClass
{
    use InteractsWithTime;

    public function testFormatTime(\DateTimeInterface|string $time): string
    {
        return $this->formatTime($time);
    }

    public function testParseTime(\DateTimeInterface|string $time): Carbon
    {
        return $this->parseTime($time);
    }
}
