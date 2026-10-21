<?php

namespace Samsara\Data\Industrial;

use Samsara\Data\Entity;

/**
 * DataInput entity.
 *
 * Represents a data input for industrial monitoring.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Data input ID
 * @property-read string|null $name Data input name
 * @property-read string|null $assetId Associated industrial asset ID
 * @property-read string|null $dataGroup Data group
 * @property-read string|null $units Unit label
 * @property-read DataPoint|null $fftSpectraPoint FFT spectra point
 * @property-read DataPoint|null $j1939D1StatusPoint J1939 status point
 * @property-read DataPoint|null $locationPoint Location point
 * @property-read DataPoint|null $numberPoint Numeric point
 * @property-read DataPoint|null $stringPoint String point
 * @property-read array<int, DataPoint>|null $fftSpectraPoints FFT spectra points
 * @property-read array<int, DataPoint>|null $j1939D1StatusPoints J1939 status points
 * @property-read array<int, DataPoint>|null $locationPoints Location points
 * @property-read array<int, DataPoint>|null $numberPoints Numeric points
 * @property-read array<int, DataPoint>|null $stringPoints String points
 */
class DataInput extends Entity
{
    /**
     * Hydrate the point envelopes used by current industrial data endpoints.
     *
     * @param  iterable<string, mixed>  $attributes
     */
    public function __construct(mixed $attributes = [])
    {
        parent::__construct($attributes);

        foreach (['fftSpectraPoint', 'j1939D1StatusPoint', 'locationPoint', 'numberPoint', 'stringPoint'] as $key) {
            if (is_array($this->attributes[$key] ?? null)) {
                $this->attributes[$key] = DataPoint::make($this->attributes[$key]);
            }
        }

        foreach (['fftSpectraPoints', 'j1939D1StatusPoints', 'locationPoints', 'numberPoints', 'stringPoints'] as $key) {
            if (is_array($this->attributes[$key] ?? null)) {
                $this->attributes[$key] = array_map(
                    static fn (mixed $point): mixed => is_array($point) ? DataPoint::make($point) : $point,
                    $this->attributes[$key]
                );
            }
        }
    }

    /**
     * Check whether this response contains any data points.
     */
    public function hasDataPoints(): bool
    {
        foreach (['fftSpectraPoint', 'j1939D1StatusPoint', 'locationPoint', 'numberPoint', 'stringPoint'] as $key) {
            if ($this->get($key) !== null) {
                return true;
            }
        }

        foreach (['fftSpectraPoints', 'j1939D1StatusPoints', 'locationPoints', 'numberPoints', 'stringPoints'] as $key) {
            $points = $this->get($key);

            if (is_array($points) && $points !== []) {
                return true;
            }
        }

        return false;
    }

    /**
     * Export point envelopes without retaining mutable point objects.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $attributes = parent::toArray();

        foreach (['fftSpectraPoint', 'j1939D1StatusPoint', 'locationPoint', 'numberPoint', 'stringPoint'] as $key) {
            if (($attributes[$key] ?? null) instanceof DataPoint) {
                $attributes[$key] = $attributes[$key]->toArray();
            }
        }

        foreach (['fftSpectraPoints', 'j1939D1StatusPoints', 'locationPoints', 'numberPoints', 'stringPoints'] as $key) {
            if (is_array($attributes[$key] ?? null)) {
                $attributes[$key] = array_map(
                    static fn (mixed $point): mixed => $point instanceof DataPoint ? $point->toArray() : $point,
                    $attributes[$key]
                );
            }
        }

        return $attributes;
    }
}
