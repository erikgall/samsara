<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Messages\V1getMessages;
use ErikGall\Samsara\Requests\Messages\V1createMessages;

class Messages extends Resource
{
    public function v1createMessages(): Response
    {
        return $this->connector->send(new V1createMessages);
    }

    /**
     * @param  int  $endMs  Time in unix milliseconds that represents the end of time range of messages to return. Used in combination with durationMs. Defaults to now.
     * @param  int  $durationMs  Time in milliseconds that represents the duration before endMs to query. Defaults to 24 hours.
     */
    public function v1getMessages(?int $endMs = null, ?int $durationMs = null): Response
    {
        return $this->connector->send(new V1getMessages($endMs, $durationMs));
    }
}
