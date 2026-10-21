<?php

namespace Samsara\Tests\Unit\Data\Industrial;

use DateTimeImmutable;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Industrial\DataPoint;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Unit tests for the DataPoint entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DataPointTest extends TestCase
{
    #[Test]
    public function it_can_have_variant_specific_data(): void
    {
        $point = new DataPoint([
            'fftSpectra'  => ['frequencies' => [100, 200]],
            'gpsLocation' => ['latitude' => 37.7749, 'longitude' => -122.4194],
        ]);

        $this->assertSame([100, 200], $point->fftSpectra['frequencies']);
        $this->assertSame(37.7749, $point->gpsLocation['latitude']);
    }

    #[Test]
    #[DataProvider('recordedTimes')]
    public function it_parses_the_recorded_time(string $timestamp, string $formatted): void
    {
        $point = new DataPoint(['time' => $timestamp]);

        $recordedAt = $point->getRecordedAt();

        $this->assertInstanceOf(DateTimeImmutable::class, $recordedAt);
        $this->assertSame($formatted, $recordedAt->format('Y-m-d\TH:i:s.vP'));
        $this->assertSame(['time' => $timestamp], $point->toArray());
    }

    #[Test]
    public function it_returns_null_without_a_recorded_time(): void
    {
        foreach ([new DataPoint, new DataPoint(['time' => null]), new DataPoint(['time' => ''])] as $point) {
            $this->assertNull($point->getRecordedAt());
        }

        $this->assertSame(['time' => null], (new DataPoint(['time' => null]))->toArray());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function recordedTimes(): iterable
    {
        yield 'negative offset' => ['2024-04-10T07:06:25.123-04:00', '2024-04-10T07:06:25.123-04:00'];
        yield 'positive offset' => ['2024-04-10T07:06:25.456+05:30', '2024-04-10T07:06:25.456+05:30'];
        yield 'UTC' => ['2024-04-10T07:06:25Z', '2024-04-10T07:06:25.000+00:00'];
    }
}
