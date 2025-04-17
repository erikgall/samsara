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
     * @param  string  $id  The unqiue Samsara id of the alert configuration.
     */
    public function deleteConfigurations(string $id): Response
    {
        return $this->connector->send(new DeleteConfigurations($id));
    }

    /**
     * @param  array  $ids  Filter by the IDs. Returns all if no ids are provided.
     * @param  string  $status  The status of the alert configuration.  Valid values: `all`, `enabled`, `disabled`
     * @param  bool  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     */
    public function getConfigurations(
        ?array $ids = null,
        ?string $status = null,
        ?bool $includeExternalIds = null
    ): Response {
        return $this->connector->send(new GetConfigurations($ids, $status, $includeExternalIds));
    }

    /**
     * @param  string  $startTime  Required RFC 3339 timestamp that indicates when to begin receiving data. This will be based on updatedAtTime.
     * @param  array  $configurationIds  Required array of alert configuration ids to return incident data for.
     * @param  string  $endTime  Optional RFC 3339 timestamp to stop receiving data. Defaults to now if not provided. This will be based on updatedAtTime.
     */
    public function getIncidents(
        string $startTime,
        array $configurationIds,
        ?string $endTime = null
    ): Response {
        return $this->connector->send(new GetIncidents($startTime, $configurationIds, $endTime));
    }

    public function patchConfigurations(array $payload = []): Response
    {
        return $this->connector->send(new PatchConfigurations($payload));
    }

    public function postConfigurations(array $payload = []): Response
    {
        return $this->connector->send(new PostConfigurations($payload));
    }
}
