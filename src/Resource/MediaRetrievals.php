<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\MediaRetrievals\GetMediaRetrieval;
use ErikGall\Samsara\Requests\MediaRetrievals\PostMediaRetrieval;

class MediaRetrievals extends Resource
{
    /**
     * @param  string  $retrievalId  Retrieval ID associated with this media capture request. Examples: 2308cec4-82e0-46f1-8b3c-a3592e5cc21e
     */
    public function getMediaRetrieval(string $retrievalId): Response
    {
        return $this->connector->send(new GetMediaRetrieval($retrievalId));
    }

    public function postMediaRetrieval(array $payload = []): Response
    {
        return $this->connector->send(new PostMediaRetrieval($payload));
    }
}
