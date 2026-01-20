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
    public function it_converts_from_unix_milliseconds(): void
    {
        $class = new TimeTestClass;
        // 2024-01-15 14:30:00 UTC in milliseconds
        $milliseconds = 1705329000000;

        $result = $class->testFromUnixMilliseconds($milliseconds);

        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertSame('2024-01-15 14:30:00', $result->utc()->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function it_converts_to_unix_milliseconds_from_carbon(): void
    {
        $class = new TimeTestClass;
        $carbon = Carbon::parse('2024-01-15 14:30:00', 'UTC');

        $result = $class->testToUnixMilliseconds($carbon);

        $this->assertSame(1705329000000, $result);
    }

    #[Test]
    public function it_converts_to_unix_milliseconds_from_string(): void
    {
        $class = new TimeTestClass;

        $result = $class->testToUnixMilliseconds('2024-01-15T14:30:00Z');

        $this->assertSame(1705329000000, $result);
    }

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
        $datetime = new DateTimeImmutable('2024-01-15 06:30:00', new \DateTimeZone('America/Los_Angeles'));

        $result = $class->testFormatTime($datetime);

        $this->assertSame('2024-01-15T14:30:00Z', $result);
    }

    #[Test]
    public function it_formats_datetime_immutable_to_utc_with_z_suffix(): void
    {
        $class = new TimeTestClass;
        $datetime = new DateTimeImmutable('2024-01-15 14:30:00', new \DateTimeZone('UTC'));

        $result = $class->testFormatTime($datetime);

        $this->assertSame('2024-01-15T14:30:00Z', $result);
    }

    #[Test]
    public function it_parses_datetime_immutable_to_carbon(): void
    {
        $class = new TimeTestClass;
        $datetime = new DateTimeImmutable('2024-01-15 14:30:00', new \DateTimeZone('UTC'));

        $result = $class->testParseTime($datetime);

        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertSame('2024-01-15 14:30:00', $result->format('Y-m-d H:i:s'));
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

    public function testFromUnixMilliseconds(int $milliseconds): Carbon
    {
        return $this->fromUnixMilliseconds($milliseconds);
    }

    public function testParseTime(\DateTimeInterface|string $time): Carbon
    {
        return $this->parseTime($time);
    }

    public function testToUnixMilliseconds(\DateTimeInterface|string $time): int
    {
        return $this->toUnixMilliseconds($time);
    }

    public function testToUtcString(\DateTimeInterface $time): string
    {
        return $this->toUtcString($time);
    }
}
