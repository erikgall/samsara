<?php

namespace Samsara\Resources\Beta;

use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\EntityCollection;

/**
 * Beta resource for the Samsara API.
 *
 * Provides access to beta/preview endpoints that are not yet stable.
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
     * Get AEMP fleet data.
     */
    public function aempFleet(): Builder
    {
        return $this->createBuilderWithEndpoint('/aemp/fleet');
    }

    /**
     * Create an industrial job.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createIndustrialJob(array $data): array
    {
        $response = $this->client()->post('/industrial/jobs', $data);

        $this->handleError($response);

        return $response->json('data', $response->json());
    }

    /**
     * Create a reading.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createReading(array $data): array
    {
        $response = $this->client()->post('/readings', $data);

        $this->handleError($response);

        return $response->json('data', $response->json());
    }

    /**
     * Delete an industrial job.
     */
    public function deleteIndustrialJob(string $id): bool
    {
        $response = $this->client()->delete("/industrial/jobs/{$id}");

        $this->handleError($response);

        return $response->successful();
    }

    /**
     * Get detections stream.
     */
    public function detectionsStream(): Builder
    {
        return $this->createBuilderWithEndpoint('/detections/stream');
    }

    /**
     * Get devices.
     *
     * @return EntityCollection<int, Entity>
     */
    public function devices(): EntityCollection
    {
        $response = $this->client()->get('/devices');

        $this->handleError($response);

        return $this->mapToEntities($response->json('data', []));
    }

    /**
     * Get HOS ELD events.
     */
    public function hosEldEvents(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/hos/eld-events');
    }

    /**
     * Get industrial jobs.
     */
    public function industrialJobs(): Builder
    {
        return $this->createBuilderWithEndpoint('/industrial/jobs');
    }

    /**
     * Get qualification records.
     */
    public function qualificationsRecords(): Builder
    {
        return $this->createBuilderWithEndpoint('/qualifications/records');
    }

    /**
     * Get qualification types.
     *
     * @return EntityCollection<int, Entity>
     */
    public function qualificationsTypes(): EntityCollection
    {
        $response = $this->client()->get('/qualifications/types');

        $this->handleError($response);

        return $this->mapToEntities($response->json('data', []));
    }

    /**
     * Get readings definitions.
     *
     * @return EntityCollection<int, Entity>
     */
    public function readingsDefinitions(): EntityCollection
    {
        $response = $this->client()->get('/readings/definitions');

        $this->handleError($response);

        return $this->mapToEntities($response->json('data', []));
    }

    /**
     * Get readings history.
     */
    public function readingsHistory(): Builder
    {
        return $this->createBuilderWithEndpoint('/readings/history');
    }

    /**
     * Get latest readings.
     */
    public function readingsLatest(): Builder
    {
        return $this->createBuilderWithEndpoint('/readings/latest');
    }

    /**
     * Get reports configs.
     *
     * @return EntityCollection<int, Entity>
     */
    public function reportsConfigs(): EntityCollection
    {
        $response = $this->client()->get('/reports/configs');

        $this->handleError($response);

        return $this->mapToEntities($response->json('data', []));
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
        return $this->createBuilderWithEndpoint('/safety-scores/drivers');
    }

    /**
     * Get safety scores for vehicles.
     */
    public function safetyScoresVehicles(): Builder
    {
        return $this->createBuilderWithEndpoint('/safety-scores/vehicles');
    }

    /**
     * Get trailer stats current.
     */
    public function trailerStatsCurrent(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/trailers/stats');
    }

    /**
     * Get trailer stats feed.
     */
    public function trailerStatsFeed(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/trailers/stats/feed');
    }

    /**
     * Get trailer stats history.
     */
    public function trailerStatsHistory(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/trailers/stats/history');
    }

    /**
     * Get training assignments.
     */
    public function trainingAssignments(): Builder
    {
        return $this->createBuilderWithEndpoint('/training/assignments');
    }

    /**
     * Get training courses.
     *
     * @return EntityCollection<int, Entity>
     */
    public function trainingCourses(): EntityCollection
    {
        $response = $this->client()->get('/training/courses');

        $this->handleError($response);

        return $this->mapToEntities($response->json('data', []));
    }

    /**
     * Update an industrial job.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function updateIndustrialJob(string $id, array $data): array
    {
        $response = $this->client()->patch("/industrial/jobs/{$id}", $data);

        $this->handleError($response);

        return $response->json('data', $response->json());
    }

    /**
     * Get vehicle immobilizer stream.
     */
    public function vehicleImmobilizerStream(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/vehicles/immobilizer/stream');
    }
}
