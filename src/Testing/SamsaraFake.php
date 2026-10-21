<?php

namespace Samsara\Testing;

use ArrayObject;
use Samsara\Samsara;
use PHPUnit\Framework\Assert;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Fake Samsara client for testing.
 *
 * Provides a convenient way to mock API responses and assert requests
 * in your application tests.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SamsaraFake extends Samsara
{
    /**
     * The faked HTTP client.
     */
    protected HttpFactory $fakeHttp;

    /**
     * The staged responses shared across credential and region clones.
     *
     * @var ArrayObject<string, array{data: array<mixed>, status: int}>
     */
    protected ArrayObject $responses;

    /**
     * Create a new SamsaraFake instance.
     *
     * @param  array<string, array<mixed>>  $responses
     */
    public function __construct(array $responses = [])
    {
        parent::__construct('fake-token');

        $this->responses = new ArrayObject;

        foreach ($responses as $endpoint => $response) {
            $this->fakeResponse($endpoint, $response);
        }

        $this->fakeHttp = new HttpFactory;
        $this->setupFake();
    }

    /**
     * Assert nothing was requested.
     */
    public function assertNothingRequested(): static
    {
        $this->fakeHttp->assertNothingSent();

        return $this;
    }

    /**
     * Assert a request was made to the given endpoint.
     */
    public function assertRequested(string $endpoint): static
    {
        $found = collect($this->getRecordedRequests())->contains(
            fn (Request $request): bool => $this->requestMatchesEndpoint($request, $endpoint),
        );

        Assert::assertTrue(
            $found,
            "Expected a request to [{$endpoint}] but it was not made."
        );

        return $this;
    }

    /**
     * Assert a request was made to the given endpoint with specific params.
     *
     * @param  array<string, mixed>  $params
     */
    public function assertRequestedWithParams(string $endpoint, array $params): static
    {
        $found = collect($this->getRecordedRequests())->contains(
            function (Request $request) use ($endpoint, $params): bool {
                if (! $this->requestMatchesEndpoint($request, $endpoint)) {
                    return false;
                }

                $requestData = $request->data();

                return collect($params)->every(
                    fn (mixed $value, string $key): bool => array_key_exists($key, $requestData)
                        && $requestData[$key] === $value,
                );
            },
        );

        Assert::assertTrue(
            $found,
            "Expected a request to [{$endpoint}] with params ".json_encode($params).' but it was not made.'
        );

        return $this;
    }

    /**
     * Fake a response for an endpoint.
     *
     * @param  array<mixed>  $data
     */
    public function fakeResponse(string $endpoint, array $data, int $status = 200): static
    {
        $this->responses[$this->normalizeEndpoint($endpoint)] = [
            'data'   => $data,
            'status' => $status,
        ];

        return $this;
    }

    /**
     * Get the recorded requests.
     *
     * @return array<int, Request>
     */
    public function getRecordedRequests(): array
    {
        return $this->fakeHttp->recorded()->map(fn (array $pair): Request => $pair[0])->all();
    }

    /**
     * Create a new SamsaraFake instance.
     *
     * @param  array<string, array<mixed>>  $responses
     */
    public static function create(array $responses = []): static
    {
        /** @phpstan-ignore new.static */
        return new static($responses);
    }

    /**
     * Set up the fake HTTP client with recorded responses.
     */
    protected function setupFake(): void
    {
        $this->fakeHttp
            ->preventStrayRequests()
            ->fake(fn (Request $request): mixed => $this->responseForRequest($request));

        $this->setHttpFactory($this->fakeHttp);
    }

    /**
     * Normalize an endpoint to an exact request path.
     */
    private function normalizeEndpoint(string $endpoint): string
    {
        $path = parse_url($endpoint, PHP_URL_PATH);

        return '/'.ltrim(is_string($path) ? $path : $endpoint, '/');
    }

    /**
     * Determine whether a request targets the exact endpoint path.
     */
    private function requestMatchesEndpoint(Request $request, string $endpoint): bool
    {
        return parse_url($request->url(), PHP_URL_PATH) === $this->normalizeEndpoint($endpoint);
    }

    /**
     * Resolve the staged response for one exact request path.
     */
    private function responseForRequest(Request $request): mixed
    {
        $path = parse_url($request->url(), PHP_URL_PATH);

        if (! is_string($path) || ! isset($this->responses[$path])) {
            return null;
        }

        $response = $this->responses[$path];

        return $this->fakeHttp->response(
            ['data' => $response['data']],
            $response['status'],
        );
    }
}
