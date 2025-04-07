<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Assets\V1getAllAssets;
use ErikGall\Samsara\Requests\Assets\V1getAssetReefer;
use ErikGall\Samsara\Requests\Assets\V1getAssetLocation;
use ErikGall\Samsara\Requests\Assets\V1getAssetsReefers;
use ErikGall\Samsara\Requests\Assets\V1getAllAssetCurrentLocations;

class Assets extends Resource
{
    /**
     * @param  string  $startingAfter  Pagination parameter indicating the cursor position to continue returning results after. Used in conjunction with the 'limit' parameter. Mutually exclusive with 'endingBefore' parameter.
     * @param  string  $endingBefore  Pagination parameter indicating the cursor position to return results before. Used in conjunction with the 'limit' parameter. Mutually exclusive with 'startingAfter' parameter.
     * @param  float|int  $limit  Pagination parameter indicating the number of results to return in this request. Used in conjunction with either 'startingAfter' or 'endingBefore'.
     */
    public function v1getAllAssetCurrentLocations(
        ?string $startingAfter = null,
        ?string $endingBefore = null,
        float|int|null $limit = null,
    ): Response {
        return $this->connector->send(new V1getAllAssetCurrentLocations($startingAfter, $endingBefore, $limit));
    }

    public function v1getAllAssets(): Response
    {
        return $this->connector->send(new V1getAllAssets);
    }

    /**
     * @param  int  $assetId  ID of the asset. Must contain only digits 0-9.
     * @param  int  $startMs  Timestamp in milliseconds representing the start of the period to fetch, inclusive. Used in combination with endMs.
     * @param  int  $endMs  Timestamp in milliseconds representing the end of the period to fetch, inclusive. Used in combination with startMs.
     */
    public function v1getAssetLocation(int $assetId, int $startMs, int $endMs): Response
    {
        return $this->connector->send(new V1getAssetLocation($assetId, $startMs, $endMs));
    }

    /**
     * @param  int  $assetId  ID of the asset. Must contain only digits 0-9.
     * @param  int  $startMs  Timestamp in milliseconds representing the start of the period to fetch, inclusive. Used in combination with endMs.
     * @param  int  $endMs  Timestamp in milliseconds representing the end of the period to fetch, inclusive. Used in combination with startMs.
     */
    public function v1getAssetReefer(int $assetId, int $startMs, int $endMs): Response
    {
        return $this->connector->send(new V1getAssetReefer($assetId, $startMs, $endMs));
    }

    /**
     * @param  int  $startMs  Timestamp in milliseconds representing the start of the period to fetch, inclusive. Used in combination with endMs.
     * @param  int  $endMs  Timestamp in milliseconds representing the end of the period to fetch, inclusive. Used in combination with startMs.
     * @param  string  $startingAfter  Pagination parameter indicating the cursor position to continue returning results after. Used in conjunction with the 'limit' parameter. Mutually exclusive with 'endingBefore' parameter.
     * @param  string  $endingBefore  Pagination parameter indicating the cursor position to return results before. Used in conjunction with the 'limit' parameter. Mutually exclusive with 'startingAfter' parameter.
     * @param  float|int  $limit  Pagination parameter indicating the number of results to return in this request. Used in conjunction with either 'startingAfter' or 'endingBefore'.
     */
    public function v1getAssetsReefers(
        int $startMs,
        int $endMs,
        ?string $startingAfter = null,
        ?string $endingBefore = null,
        float|int|null $limit = null,
    ): Response {
        return $this->connector->send(new V1getAssetsReefers($startMs, $endMs, $startingAfter, $endingBefore, $limit));
    }
}
