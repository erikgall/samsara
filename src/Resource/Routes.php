<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Routes\FetchRoute;
use ErikGall\Samsara\Requests\Routes\PatchRoute;
use ErikGall\Samsara\Requests\Routes\CreateRoute;
use ErikGall\Samsara\Requests\Routes\DeleteRoute;
use ErikGall\Samsara\Requests\Routes\FetchRoutes;
use ErikGall\Samsara\Requests\Routes\GetRoutesFeed;
use ErikGall\Samsara\Requests\Routes\V1deleteDispatchRouteById;

class Routes extends Resource
{
    /**
     * Create a new Route resource.
     *
     * @param  array  $payload  The data to create the route.
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new CreateRoute($payload));
    }

    /**
     * Delete a Route resource.
     *
     * @param  string  $id  ID of the route. This can either be the Samsara-specified ID, or an external ID. Use `key:value` for external IDs.
     * @return Response
     */
    public function delete(string $id): Response
    {
        return $this->connector->send(new DeleteRoute($id));
    }

    /**
     * Get a feed of Route resources.
     *
     * @param  string|null  $expand  Expands the specified value(s) in the response object.
     * @return Response
     */
    public function feed(?string $expand = null): Response
    {
        return $this->connector->send(new GetRoutesFeed($expand));
    }

    /**
     * Find a Route resource by ID.
     *
     * @param  string  $id  ID of the route. This can either be the Samsara-specified ID, or an external ID. Use `key:value` for external IDs.
     * @return Response
     */
    public function find(string $id): Response
    {
        return $this->connector->send(new FetchRoute($id));
    }

    /**
     * Get a list of Route resources.
     *
     * @param  string  $startTime  Start time (RFC 3339).
     * @param  string  $endTime  End time (RFC 3339).
     * @param  int|null  $limit  The limit for how many objects will be in the response.
     * @return Response
     */
    public function get(string $startTime, string $endTime, ?int $limit = null): Response
    {
        return $this->connector->send(new FetchRoutes($startTime, $endTime, $limit));
    }

    /**
     * Update a Route resource.
     *
     * @param  string  $id  ID of the route. This can either be the Samsara-specified ID, or an external ID. Use `key:value` for external IDs.
     * @param  array  $payload  The data to update the route.
     * @return Response
     */
    public function update(string $id, array $payload = []): Response
    {
        return $this->connector->send(new PatchRoute($id, $payload));
    }

    /**
     * @param  string  $routeIdOrExternalId  ID of the route. This can either be the Samsara-specified ID, or an external ID. External IDs are customer specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `payrollId:ABFS18600`
     */
    public function v1deleteDispatchRouteById(string $routeIdOrExternalId): Response
    {
        return $this->connector->send(new V1deleteDispatchRouteById($routeIdOrExternalId));
    }
}
