<?php

namespace ErikGall\Samsara\Contracts;

use DateTimeInterface;
use Illuminate\Support\LazyCollection;
use ErikGall\Samsara\Data\EntityCollection;

/**
 * Interface for query builder implementations.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
interface QueryBuilderInterface
{
    /**
     * Filter by time range.
     */
    public function between(DateTimeInterface|string $start, DateTimeInterface|string $end): static;

    /**
     * Build the query parameters array.
     *
     * @return array<string, mixed>
     */
    public function buildQuery(): array;

    /**
     * Get the first result.
     */
    public function first(): ?object;

    /**
     * Execute the query and get results.
     *
     * @return EntityCollection<int, \ErikGall\Samsara\Data\Entity>
     */
    public function get(): EntityCollection;

    /**
     * Get results as a lazy collection.
     *
     * @return LazyCollection<int, \ErikGall\Samsara\Data\Entity>
     */
    public function lazy(?int $chunkSize = null): LazyCollection;

    /**
     * Limit the number of results.
     */
    public function limit(int $limit): static;

    /**
     * Filter by updated after timestamp.
     */
    public function updatedAfter(DateTimeInterface|string $time): static;

    /**
     * Filter by driver IDs.
     *
     * @param  array<string>|string  $driverIds
     */
    public function whereDriver(array|string $driverIds): static;

    /**
     * Filter by tag IDs.
     *
     * @param  array<string>|string  $tagIds
     */
    public function whereTag(array|string $tagIds): static;

    /**
     * Filter by vehicle IDs.
     *
     * @param  array<string>|string  $vehicleIds
     */
    public function whereVehicle(array|string $vehicleIds): static;
}
