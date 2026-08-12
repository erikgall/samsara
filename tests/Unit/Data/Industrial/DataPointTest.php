<?php

namespace Samsara\Tests\Unit\Data\Industrial;

use DateTimeImmutable;
use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Industrial\DataPoint;

/**
 * Unit tests for the DataPoint entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DataPointTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $point = new DataPoint([
            'time'  => '2024-04-10T07:06:25Z',
            'value' => 98.6,
        ]);

        $this->assertSame('2024-04-10T07:06:25Z', $point->time);
        $this->assertSame(98.6, $point->value);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $point = DataPoint::make([
            'time' => '2024-04-10T07:06:25Z',
        ]);

        $this->assertInstanceOf(DataPoint::class, $point);
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'time'  => '2024-04-10T07:06:25Z',
            'value' => 98.6,
        ];

        $point = new DataPoint($data);

        $this->assertSame($data, $point->toArray());
    }

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
    public function it_extends_entity(): void
    {
        $point = new DataPoint;

        $this->assertInstanceOf(Entity::class, $point);
    }

    #[Test]
    public function it_parses_the_recorded_time(): void
    {
        $point = new DataPoint(['time' => '2024-04-10T07:06:25.123-04:00']);

        $recordedAt = $point->getRecordedAt();

        $this->assertInstanceOf(DateTimeImmutable::class, $recordedAt);
        $this->assertSame('2024-04-10T07:06:25.123-04:00', $recordedAt->format('Y-m-d\TH:i:s.vP'));
    }

    #[Test]
    public function it_returns_null_without_a_recorded_time(): void
    {
        foreach ([new DataPoint, new DataPoint(['time' => null]), new DataPoint(['time' => ''])] as $point) {
            $this->assertNull($point->getRecordedAt());
        }
    }
}
