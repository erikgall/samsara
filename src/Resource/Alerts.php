<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Alerts\GetIncidents;
use ErikGall\Samsara\Requests\Alerts\GetConfigurations;
use ErikGall\Samsara\Requests\Alerts\PostConfigurations;
use ErikGall\Samsara\Requests\Alerts\PatchConfigurations;
use ErikGall\Samsara\Requests\Alerts\DeleteConfigurations;

class Alerts extends Resource
{
    /**
     * Create a new alert configuration.
     *
     * @param  array  $payload  The data to create the alert configuration.
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new PostConfigurations($payload));
    }

    /**
     * Delete an alert configuration.
     *
     * @param  string  $id  The unique Samsara id of the alert configuration.
     * @return Response
     */
    public function delete(string $id): Response
    {
        return $this->connector->send(new DeleteConfigurations($id));
    }

    /**
     * Get alert configurations.
     *
     * @param  array|null  $ids  Filter by the IDs. Returns all if no ids are provided.
     * @param  string|null  $status  The status of the alert configuration. Valid values: `all`, `enabled`, `disabled`.
     * @param  bool|null  $includeExternalIds  Whether to return external IDs on supported entities.
     * @return Response
     */
    public function get(
        ?array $ids = null,
        ?string $status = null,
        ?bool $includeExternalIds = null
    ): Response {
        return $this->connector->send(new GetConfigurations($ids, $status, $includeExternalIds));
    }

    /**
     * Get alert incidents.
     *
     * @param  string  $startTime  Required RFC 3339 timestamp that indicates when to begin receiving data.
     * @param  array  $configurationIds  Required array of alert configuration ids to return incident data for.
     * @param  string|null  $endTime  Optional RFC 3339 timestamp to stop receiving data. Defaults to now if not provided.
     * @return Response
     */
    public function getIncidents(
        string $startTime,
        array $configurationIds,
        ?string $endTime = null
    ): Response {
        return $this->connector->send(new GetIncidents($startTime, $configurationIds, $endTime));
    }

    /**
     * Update alert configurations.
     *
     * @param  array  $payload  The data to update the alert configuration.
     * @return Response
     */
    public function update(array $payload = []): Response
    {
        return $this->connector->send(new PatchConfigurations($payload));
    }
}
