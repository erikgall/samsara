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
 */
class BetaResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/beta';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Entity>
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

        return $response->json();
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
        return $response->json('data', $response->json());
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

        return $response->json();
    }

    /**
     * Get detections stream.
     */
    public function detectionsStream(): Builder
    {
        return $this->createBuilderWithEndpoint('/detections/stream')
            ->withoutDefaultPageSize();
    }

    /**
     * Get devices.
     */
    public function devices(): Builder
    {
        return $this->createBuilderWithEndpoint('/devices');
    }

    /**
     * Get HOS ELD events.
     */
    public function hosEldEvents(): Builder
    {
        return $this->createBuilderWithEndpoint('/beta/fleet/hos/drivers/eld-events')
            ->limit(25);
    }

    /**
     * Get industrial jobs.
     */
    public function industrialJobs(): Builder
    {
        return $this->createBuilderWithEndpoint('/beta/industrial/jobs')
            ->withoutDefaultPageSize()
            ->repeatQueryParameters('industrialAssetIds', 'fleetDeviceIds');
    }

    /**
     * Get qualification records.
     */
    public function qualificationsRecords(): Builder
    {
        return $this->createBuilderWithEndpoint('/qualification-records')
            ->withoutDefaultPageSize();
    }

    /**
     * Get qualification types.
     */
    public function qualificationsTypes(): Builder
    {
        return $this->createBuilderWithEndpoint('/qualification-types')
            ->withoutDefaultPageSize();
    }

    /**
     * Get readings definitions.
     */
    public function readingsDefinitions(): Builder
    {
        return $this->createBuilderWithEndpoint('/readings/definitions')
            ->withoutDefaultPageSize();
    }

    /**
     * Get readings history.
     */
    public function readingsHistory(): Builder
    {
        return $this->createBuilderWithEndpoint('/readings/history')
            ->withoutDefaultPageSize();
    }

    /**
     * Get latest readings.
     */
    public function readingsLatest(): Builder
    {
        return $this->createBuilderWithEndpoint('/readings/latest')
            ->withoutDefaultPageSize();
    }

    /**
     * Get reports configs.
     */
    public function reportsConfigs(): Builder
    {
        return $this->createBuilderWithEndpoint('/reports/configs');
    }

    /**
     * Get reports datasets.
     */
    public function reportsDatasets(): Builder
    {
        return $this->createBuilderWithEndpoint('/reports/datasets');
    }

    /**
     * Get reports runs.
     */
    public function reportsRuns(): Builder
    {
        return $this->createBuilderWithEndpoint('/reports/runs');
    }

    /**
     * Get safety scores for drivers.
     */
    public function safetyScoresDrivers(): Builder
    {
        return $this->createBuilderWithEndpoint('/safety-scores/drivers')
            ->withoutDefaultPageSize()
            ->usingEntity(SafetyScore::class);
    }

    /**
     * Get safety scores for vehicles.
     */
    public function safetyScoresVehicles(): Builder
    {
        return $this->createBuilderWithEndpoint('/safety-scores/vehicles')
            ->withoutDefaultPageSize();
    }

    /**
     * Get trailer stats current.
     */
    public function trailerStatsCurrent(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/trailers/stats')
            ->withoutDefaultPageSize()
            ->usingEntity(TrailerStats::class);
    }

    /**
     * Get trailer stats feed.
     */
    public function trailerStatsFeed(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/trailers/stats/feed')
            ->withoutDefaultPageSize();
    }

    /**
     * Get trailer stats history.
     */
    public function trailerStatsHistory(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/trailers/stats/history')
            ->withoutDefaultPageSize();
    }

    /**
     * Get training assignments.
     */
    public function trainingAssignments(): Builder
    {
        return $this->createBuilderWithEndpoint('/training-assignments/stream')
            ->withoutDefaultPageSize();
    }

    /**
     * Get training courses.
     */
    public function trainingCourses(): Builder
    {
        return $this->createBuilderWithEndpoint('/training-courses')
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
        return $response->json('data', $response->json());
    }

    /**
     * Get vehicle immobilizer stream.
     */
    public function vehicleImmobilizerStream(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/vehicles/immobilizer/stream')
            ->withoutDefaultPageSize();
    }

    /**
     * {@inheritDoc}
     */
    protected function supportedGenericOperations(): array
    {
        return [];
    }
}
