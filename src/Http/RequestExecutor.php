<?php

namespace Samsara\Http;

use Closure;
use Illuminate\Http\Client\Response;
use Samsara\Exceptions\SamsaraException;
use Illuminate\Http\Client\RequestException;

/**
 * Execute SDK requests across Laravel's returned and thrown response paths.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
final class RequestExecutor
{
    /**
     * Execute a request and route unsuccessful responses through the SDK mapper.
     *
     * @param  Closure(): Response  $request
     * @param  Closure(Response): void  $throwIfError
     * @param  array<int, int>  $ignoredStatuses
     */
    public function execute(
        Closure $request,
        Closure $throwIfError,
        string $endpoint,
        array $ignoredStatuses = []
    ): Response {
        try {
            $response = $request();
        } catch (RequestException $exception) {
            $response = $exception->response;

            if (in_array($response->status(), $ignoredStatuses, true)) {
                return $response;
            }

            $this->mapError($response, $throwIfError, $endpoint);

            throw $exception;
        }

        if (! in_array($response->status(), $ignoredStatuses, true)) {
            $this->mapError($response, $throwIfError, $endpoint);
        }

        return $response;
    }

    /**
     * Map an error and preserve the effective request endpoint in its context.
     *
     * @param  Closure(Response): void  $throwIfError
     */
    private function mapError(Response $response, Closure $throwIfError, string $endpoint): void
    {
        try {
            $throwIfError($response);
        } catch (SamsaraException $exception) {
            $exception->setContext([
                ...$exception->getContext(),
                'endpoint' => $endpoint,
            ]);

            throw $exception;
        }
    }
}
