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
    public function createRoute(): Response
    {
        return $this->connector->send(new CreateRoute);
    }

    /**
     * @param  string  $id  ID of the route. This can either be the Samsara-specified ID, or an external ID. External IDs are customer specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `payrollId:ABFS18600`
     */
    public function deleteRoute(string $id): Response
    {
        return $this->connector->send(new DeleteRoute($id));
    }

    /**
     * @param  string  $id  ID of the route. This can either be the Samsara-specified ID, or an external ID. External IDs are customer specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `payrollId:ABFS18600`
     */
    public function fetchRoute(string $id): Response
    {
        return $this->connector->send(new FetchRoute($id));
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     */
    public function fetchRoutes(string $startTime, string $endTime, ?int $limit): Response
    {
        return $this->connector->send(new FetchRoutes($startTime, $endTime, $limit));
    }

    /**
     * @param  string  $expand  Expands the specified value(s) in the response object. Expansion populates additional fields in an object, if supported. Unsupported fields are ignored. To expand multiple fields, input a comma-separated list.
     *
     * Valid value: `route`  Valid values: `route`
     */
    public function getRoutesFeed(?string $expand): Response
    {
        return $this->connector->send(new GetRoutesFeed($expand));
    }

    /**
     * @param  string  $id  ID of the route. This can either be the Samsara-specified ID, or an external ID. External IDs are customer specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `payrollId:ABFS18600`
     */
    public function patchRoute(string $id): Response
    {
        return $this->connector->send(new PatchRoute($id));
    }

    /**
     * @param  string  $routeIdOrExternalId  ID of the route. This can either be the Samsara-specified ID, or an external ID. External IDs are customer specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `payrollId:ABFS18600`
     */
    public function v1deleteDispatchRouteById(string $routeIdOrExternalId): Response
    {
        return $this->connector->send(new V1deleteDispatchRouteById($routeIdOrExternalId));
    }
}
