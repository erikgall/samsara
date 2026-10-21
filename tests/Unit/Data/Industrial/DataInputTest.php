<?php

namespace Samsara\Tests\Unit\Data\Industrial;

use ArrayIterator;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Industrial\DataInput;
use Samsara\Data\Industrial\DataPoint;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Unit tests for the DataInput entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DataInputTest extends TestCase
{
    #[Test]
    public function it_accepts_already_hydrated_points(): void
    {
        $attributes = ['time' => '2026-08-10T12:00:00Z', 'value' => 98.6];
        $point = new DataPoint($attributes);
        $input = new DataInput(['numberPoint' => $point, 'numberPoints' => [$point]]);

        $this->assertSame($point, $input->numberPoint);
        $this->assertSame($point, $input->numberPoints[0]);
        $this->assertSame(['numberPoint' => $attributes, 'numberPoints' => [$attributes]], $input->toArray());
    }

    #[Test]
    public function it_detects_singular_and_plural_data_points(): void
    {
        $this->assertTrue(DataInput::make([
            'numberPoint' => ['time' => '2026-08-10T12:00:00Z', 'value' => 98.6],
        ])->hasDataPoints());
        $this->assertTrue(DataInput::make([
            'stringPoints' => [['time' => '2026-08-10T12:00:01Z', 'value' => 'running']],
        ])->hasDataPoints());

        foreach ([
            [],
            ['numberPoint'  => null, 'stringPoints' => null],
            ['stringPoints' => []],
        ] as $attributes) {
            $this->assertFalse(DataInput::make($attributes)->hasDataPoints());
        }
    }

    /**
     * @param  array<string, mixed>  $point
     */
    #[Test]
    #[DataProvider('pointEnvelopes')]
    public function it_does_not_share_points_between_round_tripped_inputs(string $key, array $point): void
    {
        $original = DataInput::make([$key => $point, $key.'s' => [$point]]);
        $copy = DataInput::make($original->toArray());

        $copy->get($key)->offsetSet('value', 'changed singular');
        $copy->get($key.'s')[0]->offsetSet('value', 'changed plural');

        $this->assertSame($point, $original->get($key)->toArray());
        $this->assertSame($point, $original->get($key.'s')[0]->toArray());
        $this->assertNotSame($original->get($key), $copy->get($key));
        $this->assertNotSame($original->get($key.'s')[0], $copy->get($key.'s')[0]);
    }

    /**
     * @param  array<string, mixed>  $point
     */
    #[Test]
    #[DataProvider('pointEnvelopes')]
    public function it_exports_nested_points_as_arrays(string $key, array $point): void
    {
        $attributes = ['id' => 'input-1', $key => $point, $key.'s' => [$point]];
        $input = DataInput::make($attributes);

        $this->assertInstanceOf(DataPoint::class, $input->get($key));
        $this->assertInstanceOf(DataPoint::class, $input->get($key.'s')[0]);
        $this->assertSame($attributes, $input->toArray());
        $this->assertSame($attributes, json_decode($input->toJson(), true, flags: JSON_THROW_ON_ERROR));
    }

    #[Test]
    public function it_hydrates_current_singular_and_plural_data_points(): void
    {
        $input = DataInput::make([
            'id'          => 'input-1',
            'numberPoint' => [
                'time'  => '2026-08-10T12:00:00Z',
                'value' => 98.6,
            ],
            'stringPoints' => [
                ['time' => '2026-08-10T12:00:01Z', 'value' => 'running'],
            ],
        ]);

        $this->assertInstanceOf(DataPoint::class, $input->numberPoint);
        $this->assertSame(98.6, $input->numberPoint->value);
        $this->assertInstanceOf(DataPoint::class, $input->stringPoints[0]);
        $this->assertSame('running', $input->stringPoints[0]->value);
    }

    #[Test]
    public function it_hydrates_points_on_every_construction_path(): void
    {
        $attributes = [
            'numberPoint'  => ['time' => '2026-08-10T12:00:00Z', 'value' => 98.6],
            'stringPoints' => [['time' => '2026-08-10T12:00:01Z', 'value' => 'running']],
        ];

        foreach ([
            new DataInput($attributes),
            new DataInput(new ArrayIterator($attributes)),
            DataInput::make($attributes),
            DataInput::make(new ArrayIterator($attributes)),
        ] as $input) {
            $this->assertInstanceOf(DataPoint::class, $input->numberPoint);
            $this->assertSame(98.6, $input->numberPoint->value);
            $this->assertInstanceOf(DataPoint::class, $input->stringPoints[0]);
            $this->assertSame('running', $input->stringPoints[0]->value);
            $this->assertSame($attributes, $input->toArray());
        }
    }

    #[Test]
    public function it_preserves_null_empty_and_unrelated_attributes(): void
    {
        $attributes = [
            'numberPoint'    => null,
            'numberPoints'   => null,
            'stringPoints'   => [],
            'locationPoints' => [null],
            'metadata'       => ['unknown' => ['value' => 0]],
        ];

        $this->assertSame($attributes, DataInput::make($attributes)->toArray());
        $this->assertSame([], DataInput::make()->toArray());
    }

    /**
     * @return iterable<string, array{string, array<string, mixed>}>
     */
    public static function pointEnvelopes(): iterable
    {
        yield 'fft spectra' => ['fftSpectraPoint', ['time' => '2026-08-10T12:00:00Z', 'fftSpectra' => ['frequencies' => [100, 200]]]];
        yield 'j1939 status' => ['j1939D1StatusPoint', ['time' => '2026-08-10T12:00:00Z', 'value' => ['status' => 'active']]];
        yield 'location' => ['locationPoint', ['time' => '2026-08-10T12:00:00Z', 'gpsLocation' => ['latitude' => 37.7, 'longitude' => -122.4]]];
        yield 'number' => ['numberPoint', ['time' => '2026-08-10T12:00:00Z', 'value' => 98.6]];
        yield 'string' => ['stringPoint', ['time' => '2026-08-10T12:00:00Z', 'value' => 'running']];
    }
}
