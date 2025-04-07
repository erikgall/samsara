<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\TrailerAssignments\V1getAllTrailerAssignments;
use ErikGall\Samsara\Requests\TrailerAssignments\V1getFleetTrailerAssignments;

class TrailerAssignments extends Resource
{
    /**
     * @param  int  $startMs  Timestamp in Unix epoch miliseconds representing the start of the period to fetch. Omitting both startMs and endMs only returns current assignments.
     * @param  int  $endMs  Timestamp in Unix epoch miliseconds representing the end of the period to fetch. Omitting endMs sets endMs as the current time
     * @param  float|int  $limit  Pagination parameter indicating the number of results to return in this request. Used in conjunction with either 'startingAfter' or 'endingBefore'.
     * @param  string  $startingAfter  Pagination parameter indicating the cursor position to continue returning results after. Used in conjunction with the 'limit' parameter. Mutually exclusive with 'endingBefore' parameter.
     * @param  string  $endingBefore  Pagination parameter indicating the cursor position to return results before. Used in conjunction with the 'limit' parameter. Mutually exclusive with 'startingAfter' parameter.
     */
    public function v1getAllTrailerAssignments(
        ?int $startMs,
        ?int $endMs,
        float|int|null $limit,
        ?string $startingAfter = null,
        ?string $endingBefore = null
    ): Response {
        return $this->connector->send(
            new V1getAllTrailerAssignments($startMs, $endMs, $limit, $startingAfter, $endingBefore)
        );
    }

    /**
     * @param  int  $trailerId  ID of trailer. Must contain only digits 0-9.
     * @param  int  $startMs  Timestamp in Unix epoch milliseconds representing the start of the period to fetch. Omitting both startMs and endMs only returns current assignments.
     * @param  int  $endMs  Timestamp in Unix epoch milliseconds representing the end of the period to fetch. Omitting endMs sets endMs as the current time
     */
    public function v1getFleetTrailerAssignments(
        int $trailerId,
        ?int $startMs = null,
        ?int $endMs = null
    ): Response {
        return $this->connector->send(
            new V1getFleetTrailerAssignments($trailerId, $startMs, $endMs)
        );
    }
}
