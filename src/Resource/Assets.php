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
     * Get all assets.
     *
     * @return Response
     */
    public function getAll(): Response
    {
        return $this->connector->send(new V1getAllAssets);
    }

    /**
     * Get all asset current locations.
     *
     * @param  string|null  $startingAfter  Pagination cursor for next page.
     * @param  string|null  $endingBefore  Pagination cursor for previous page.
     * @param  float|int|null  $limit  Number of results to return.
     * @return Response
     */
    public function getAllCurrentLocations(
        ?string $startingAfter = null,
        ?string $endingBefore = null,
        float|int|null $limit = null
    ): Response {
        return $this->connector->send(
            new V1getAllAssetCurrentLocations($startingAfter, $endingBefore, $limit)
        );
    }

    /**
     * Get assets reefers for a time range.
     *
     * @param  int  $startMs  Start timestamp (ms).
     * @param  int  $endMs  End timestamp (ms).
     * @param  string|null  $startingAfter  Pagination cursor for next page.
     * @param  string|null  $endingBefore  Pagination cursor for previous page.
     * @param  float|int|null  $limit  Number of results to return.
     * @return Response
     */
    public function getAssetsReefers(
        int $startMs,
        int $endMs,
        ?string $startingAfter = null,
        ?string $endingBefore = null,
        float|int|null $limit = null
    ): Response {
        return $this->connector->send(
            new V1getAssetsReefers($startMs, $endMs, $startingAfter, $endingBefore, $limit)
        );
    }

    /**
     * Get asset location for a specific asset and time range.
     *
     * @param  int  $assetId  Asset ID.
     * @param  int  $startMs  Start timestamp (ms).
     * @param  int  $endMs  End timestamp (ms).
     * @return Response
     */
    public function getLocation(int $assetId, int $startMs, int $endMs): Response
    {
        return $this->connector->send(new V1getAssetLocation($assetId, $startMs, $endMs));
    }

    /**
     * Get asset reefer data for a specific asset and time range.
     *
     * @param  int  $assetId  Asset ID.
     * @param  int  $startMs  Start timestamp (ms).
     * @param  int  $endMs  End timestamp (ms).
     * @return Response
     */
    public function getReefer(int $assetId, int $startMs, int $endMs): Response
    {
        return $this->connector->send(new V1getAssetReefer($assetId, $startMs, $endMs));
    }
}
