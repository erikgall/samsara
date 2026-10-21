<?php

namespace Samsara\Tests\Unit\Query;

use Samsara\Data\Entity;
use Samsara\Query\Builder;

use function PHPStan\Testing\assertType;

use Samsara\Data\Vehicle\VehicleLocationSeries;

/**
 * @param  Builder<Entity>  $builder
 */
function assertBuilderInference(Builder $builder): void
{
    $locations = $builder->usingEntity(VehicleLocationSeries::class);

    assertType('Samsara\Data\Vehicle\VehicleLocationSeries|null', $locations->first());
    assertType('Samsara\Data\EntityCollection<int, Samsara\Data\Vehicle\VehicleLocationSeries>', $locations->get());
    assertType('Illuminate\Support\LazyCollection<int, Samsara\Data\Vehicle\VehicleLocationSeries>', $locations->lazy());
    assertType('Samsara\Query\Pagination\CursorPaginator<Samsara\Data\Vehicle\VehicleLocationSeries>', $locations->paginate());
    assertType('Samsara\Data\Vehicle\VehicleLocationSeries|null', $locations->paginate()->nextPage()?->items()->first());

    foreach ($locations->lazy() as $location) {
        assertType('Samsara\Data\Vehicle\VehicleLocationSeries', $location);
    }

    foreach ($locations->paginate() as $location) {
        assertType('Samsara\Data\Vehicle\VehicleLocationSeries', $location);
    }

}

/**
 * @param  Builder<Entity>  $builder
 */
function assertMapperInference(Builder $builder): void
{
    $mapped = $builder->mapUsing(fn (array $attributes): VehicleLocationSeries => VehicleLocationSeries::make($attributes));
    assertType('Samsara\Query\Builder<Samsara\Data\Vehicle\VehicleLocationSeries>', $mapped);
    assertType('Samsara\Data\Vehicle\VehicleLocationSeries|null', $mapped->first());
    assertType('Samsara\Data\Vehicle\VehicleLocationSeries|null', $builder->first());
}
