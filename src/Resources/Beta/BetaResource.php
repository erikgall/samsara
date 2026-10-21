<?php

namespace Samsara\Resources\Beta;

use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Safety\SafetyScore;
use Samsara\Data\Trailer\TrailerStats;

/**
 * Beta resource for the Samsara API.
 *
 * Provides access to beta endpoints that are not yet stable.
 * These endpoints may change without notice.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Entity>
 */
class BetaResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/beta';

    /**
     * The entity class for this resource.
     */
    protected string $entity = Entity::class;

    /**
     * Get AEMP fleet data for a page.
     *
     * @return array<string, mixed>
     */
    public function aempFleet(string $pageNumber): array
    {
        $endpoint = '/beta/aemp/Fleet/'.rawurlencode($pageNumber);
        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint),
            $endpoint
        );

        return $this->decodeJsonArray($response, $endpoint);
    }

    /**
     * Create an industrial job.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createIndustrialJob(array $data): array
    {
        $endpoint = '/beta/industrial/jobs';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, $data),
            $endpoint
        );

        /** @var array<string, mixed> */
        return $this->decodeJsonData($response, $endpoint);
    }

    /**
     * Create readings. The current contract returns no response body.
     *
     * @param  array<string, mixed>  $data
     */
    public function createReading(array $data): bool
    {
        $endpoint = '/readings';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, $data),
            $endpoint
        );

        return $response->successful();
    }

    /**
     * Delete an industrial job.
     *
     * @return array<string, mixed>
     */
    public function deleteIndustrialJob(string $id): array
    {
        $endpoint = '/beta/industrial/jobs';
        $errorEndpoint = $endpoint.'?id='.rawurlencode($id);
        $response = $this->executeSdkRequest(
            fn () => $this->client()
                ->withQueryParameters(['id' => $id])
                ->delete($endpoint),
            $errorEndpoint
        );

        return $this->decodeJsonArray($response, $errorEndpoint);
    }

    /**
     * Get detections stream.
     *
     * @return Builder<Entity>
     */
    public function detectionsStream(): Builder
    {
        return $this->newQuery('/detections/stream')
            ->withoutDefaultPageSize();
    }

    /**
     * Get devices.
     *
     * @return Builder<Entity>
     */
    public function devices(): Builder
    {
        return $this->newQuery('/devices');
    }

    /**
     * Get HOS ELD events.
     *
     * @return Builder<Entity>
     */
    public function hosEldEvents(): Builder
    {
        return $this->newQuery('/beta/fleet/hos/drivers/eld-events')
            ->limit(25);
    }

    /**
     * Get industrial jobs.
     *
     * @return Builder<Entity>
     */
    public function industrialJobs(): Builder
    {
        return $this->newQuery('/beta/industrial/jobs')
            ->withoutDefaultPageSize()
            ->repeatQueryParameters('industrialAssetIds', 'fleetDeviceIds');
    }

    /**
     * Get qualification records.
     *
     * @return Builder<Entity>
     */
    public function qualificationsRecords(): Builder
    {
        return $this->newQuery('/qualification-records')
            ->withoutDefaultPageSize();
    }

    /**
     * Get qualification types.
     *
     * @return Builder<Entity>
     */
    public function qualificationsTypes(): Builder
    {
        return $this->newQuery('/qualification-types')
            ->withoutDefaultPageSize();
    }

    /**
     * Get readings definitions.
     *
     * @return Builder<Entity>
     */
    public function readingsDefinitions(): Builder
    {
        return $this->newQuery('/readings/definitions')
            ->withoutDefaultPageSize();
    }

    /**
     * Get readings history.
     *
     * @return Builder<Entity>
     */
    public function readingsHistory(): Builder
    {
        return $this->newQuery('/readings/history')
            ->withoutDefaultPageSize();
    }

    /**
     * Get latest readings.
     *
     * @return Builder<Entity>
     */
    public function readingsLatest(): Builder
    {
        return $this->newQuery('/readings/latest')
            ->withoutDefaultPageSize();
    }

    /**
     * Get reports configs.
     *
     * @return Builder<Entity>
     */
    public function reportsConfigs(): Builder
    {
        return $this->newQuery('/reports/configs');
    }

    /**
     * Get reports datasets.
     *
     * @return Builder<Entity>
     */
    public function reportsDatasets(): Builder
    {
        return $this->newQuery('/reports/datasets');
    }

    /**
     * Get reports runs.
     *
     * @return Builder<Entity>
     */
    public function reportsRuns(): Builder
    {
        return $this->newQuery('/reports/runs');
    }

    /**
     * Get safety scores for drivers.
     *
     * @return Builder<SafetyScore>
     */
    public function safetyScoresDrivers(): Builder
    {
        return $this->newQuery('/safety-scores/drivers')
            ->withoutDefaultPageSize()
            ->usingEntity(SafetyScore::class);
    }

    /**
     * Get safety scores for vehicles.
     *
     * @return Builder<SafetyScore>
     */
    public function safetyScoresVehicles(): Builder
    {
        return $this->newQuery('/safety-scores/vehicles')
            ->withoutDefaultPageSize()
            ->usingEntity(SafetyScore::class);
    }

    /**
     * Get trailer stats current.
     *
     * @return Builder<TrailerStats>
     */
    public function trailerStatsCurrent(): Builder
    {
        return $this->newQuery('/fleet/trailers/stats')
            ->withoutDefaultPageSize()
            ->usingEntity(TrailerStats::class);
    }

    /**
     * Get trailer stats feed.
     *
     * @return Builder<Entity>
     */
    public function trailerStatsFeed(): Builder
    {
        return $this->newQuery('/fleet/trailers/stats/feed')
            ->withoutDefaultPageSize();
    }

    /**
     * Get trailer stats history.
     *
     * @return Builder<Entity>
     */
    public function trailerStatsHistory(): Builder
    {
        return $this->newQuery('/fleet/trailers/stats/history')
            ->withoutDefaultPageSize();
    }

    /**
     * Get training assignments.
     *
     * @return Builder<Entity>
     */
    public function trainingAssignments(): Builder
    {
        return $this->newQuery('/training-assignments/stream')
            ->withoutDefaultPageSize();
    }

    /**
     * Get training courses.
     *
     * @return Builder<Entity>
     */
    public function trainingCourses(): Builder
    {
        return $this->newQuery('/training-courses')
            ->withoutDefaultPageSize();
    }

    /**
     * Update an industrial job.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function updateIndustrialJob(string $id, array $data): array
    {
        $endpoint = '/beta/industrial/jobs';
        $errorEndpoint = $endpoint.'?id='.rawurlencode($id);
        $response = $this->executeSdkRequest(
            fn () => $this->client()
                ->withQueryParameters(['id' => $id])
                ->patch($endpoint, $data),
            $errorEndpoint
        );

        /** @var array<string, mixed> */
        return $this->decodeJsonData($response, $errorEndpoint);
    }

    /**
     * Get vehicle immobilizer stream.
     *
     * @return Builder<Entity>
     */
    public function vehicleImmobilizerStream(): Builder
    {
        return $this->newQuery('/fleet/vehicles/immobilizer/stream')
            ->withoutDefaultPageSize();
    }
}
