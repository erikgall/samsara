<?php

namespace Samsara\Tests\Unit\Concerns;

use DateTime;
use DateTimeZone;
use DateTimeImmutable;
use Samsara\Tests\TestCase;
use Illuminate\Support\Carbon;
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
    public function it_converts_to_utc_string(): void
    {
        $class = new TimeTestClass;
        $carbon = Carbon::parse('2024-01-15 14:30:00', 'UTC');

        $result = $class->testToUtcString($carbon);

        $this->assertSame('2024-01-15T14:30:00Z', $result);
    }

    #[Test]
    public function it_converts_to_utc_string_from_different_timezone(): void
    {
        $class = new TimeTestClass;
        // 2024-01-15 06:30:00 PST = 2024-01-15 14:30:00 UTC
        $carbon = Carbon::parse('2024-01-15 06:30:00', 'America/Los_Angeles');

        $result = $class->testToUtcString($carbon);

        $this->assertSame('2024-01-15T14:30:00Z', $result);
        $this->assertSame('America/Los_Angeles', $carbon->getTimezone()->getName());
        $this->assertSame('2024-01-15 06:30:00', $carbon->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function it_formats_carbon_instance_to_utc_with_z_suffix(): void
    {
        $class = new TimeTestClass;
        $carbon = Carbon::parse('2024-01-15 14:30:00', 'UTC');

        $result = $class->testFormatTime($carbon);

        $this->assertSame('2024-01-15T14:30:00Z', $result);
    }

    #[Test]
    public function it_formats_datetime_from_different_timezone_to_utc(): void
    {
        $class = new TimeTestClass;
        // 2024-01-15 06:30:00 PST = 2024-01-15 14:30:00 UTC
        $datetime = new DateTimeImmutable('2024-01-15 06:30:00', new DateTimeZone('America/Los_Angeles'));

        $result = $class->testFormatTime($datetime);

        $this->assertSame('2024-01-15T14:30:00Z', $result);
    }

    #[Test]
    public function it_formats_datetime_immutable_to_utc_with_z_suffix(): void
    {
        $class = new TimeTestClass;
        $datetime = new DateTimeImmutable('2024-01-15 14:30:00', new DateTimeZone('UTC'));

        $result = $class->testFormatTime($datetime);

        $this->assertSame('2024-01-15T14:30:00Z', $result);
    }

    #[Test]
    public function it_formats_the_laravel_clock_as_rfc3339_in_utc(): void
    {
        Carbon::setTestNow(Carbon::parse('2024-06-15T10:30:45.123456-05:00'));

        try {
            $this->assertSame('2024-06-15T15:30:45Z', (new TimeTestClass)->testFormatTime(Carbon::now()));
        } finally {
            Carbon::setTestNow();
        }
    }

    #[Test]
    public function it_preserves_mutable_datetime_inputs_when_converting_fractional_timezone_offsets(): void
    {
        $time = new DateTime('2024-06-15 10:30:45.123456', new DateTimeZone('Asia/Kolkata'));

        $this->assertSame('2024-06-15T05:00:45Z', (new TimeTestClass)->testFormatTime($time));
        $this->assertSame('2024-06-15T10:30:45.123456+05:30', $time->format('Y-m-d\TH:i:s.uP'));
        $this->assertSame('Asia/Kolkata', $time->getTimezone()->getName());
    }

    #[Test]
    public function it_preserves_rfc3339_strings_with_fractional_seconds_and_offsets(): void
    {
        $timestamp = '2024-06-15T10:30:45.123456+05:30';

        $this->assertSame($timestamp, (new TimeTestClass)->testFormatTime($timestamp));
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
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TimeTestClass
{
    use InteractsWithTime;

    public function testFormatTime(\DateTimeInterface|string $time): string
    {
        return $this->formatTime($time);
    }

    public function testToUtcString(\DateTimeInterface $time): string
    {
        return $this->toUtcString($time);
    }
}
