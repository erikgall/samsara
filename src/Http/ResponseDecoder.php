<?php

namespace Samsara\Http;

use Illuminate\Support\Arr;
use Illuminate\Http\Client\Response;
use Samsara\Exceptions\SamsaraException;

/**
 * Decodes successful Samsara JSON responses at their public envelope boundaries.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
final class ResponseDecoder
{
    /**
     * @return array<mixed>
     */
    public function decodeArray(Response $response, string $endpoint): array
    {
        $payload = $response->json();

        if (is_array($payload)) {
            return $payload;
        }

        throw $this->unexpectedResponse(
            $response,
            $endpoint,
            "Expected a JSON object or array from successful response for [{$endpoint}].",
        );
    }

    /**
     * @return array<mixed>
     */
    public function decodeData(
        Response $response,
        string $endpoint,
        bool $fallbackToPayload = true,
    ): array {
        $payload = $this->decodeArray($response, $endpoint);

        if (! array_key_exists('data', $payload)) {
            return $fallbackToPayload ? $payload : [];
        }

        $data = $payload['data'];

        if (is_array($data)) {
            return $data;
        }

        throw $this->unexpectedResponse(
            $response,
            $endpoint,
            "Expected response data to be a JSON object or array for [{$endpoint}].",
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function decodeList(
        Response $response,
        string $endpoint,
        string $collectionKey = 'data',
    ): array {
        $payload = $this->decodeArray($response, $endpoint);

        if (! Arr::has($payload, $collectionKey)) {
            return [];
        }

        $data = Arr::get($payload, $collectionKey);

        if (is_array($data) && array_is_list($data)) {
            return $data;
        }

        throw $this->unexpectedResponse(
            $response,
            $endpoint,
            "Expected response data to be a JSON list for [{$endpoint}].",
        );
    }

    private function unexpectedResponse(
        Response $response,
        string $endpoint,
        string $message,
    ): SamsaraException {
        return new SamsaraException(
            $message,
            $response->status(),
            null,
            [
                'status'   => $response->status(),
                'endpoint' => $endpoint,
                'body'     => $response->body(),
            ],
        );
    }
}
