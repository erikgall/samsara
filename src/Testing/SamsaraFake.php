<?php

namespace ErikGall\Samsara\Testing;

use ErikGall\Samsara\Samsara;
use PHPUnit\Framework\Assert;
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
     * The faked responses keyed by endpoint pattern.
     *
     * @var array<string, array{data: array<mixed>, status: int}>
     */
    protected array $responses = [];

    /**
     * Create a new SamsaraFake instance.
     */
    public function __construct()
    {
        parent::__construct('fake-token');

        $this->fakeHttp = new HttpFactory;
        $this->setupFake();
    }

    /**
     * Assert nothing was requested.
     */
    public function assertNothingRequested(): static
    {
        $recorded = $this->getRecordedRequests();

        Assert::assertEmpty(
            $recorded,
            'Expected no requests to be made, but found '
            .count($recorded).' request(s).'
        );

        return $this;
    }

    /**
     * Assert a request was made to the given endpoint.
     */
    public function assertRequested(string $endpoint): static
    {
        $found = false;

        foreach ($this->getRecordedRequests() as $request) {
            if (str_contains($request->url(), $endpoint)) {
                $found = true;
                break;
            }
        }

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
        $found = false;

        foreach ($this->getRecordedRequests() as $request) {
            if (str_contains($request->url(), $endpoint)) {
                $requestData = $request->data();
                $allParamsMatch = true;

                foreach ($params as $key => $value) {
                    if (! isset($requestData[$key]) || $requestData[$key] !== $value) {
                        $allParamsMatch = false;
                        break;
                    }
                }

                if ($allParamsMatch) {
                    $found = true;
                    break;
                }
            }
        }

        Assert::assertTrue(
            $found,
            "Expected a request to [{$endpoint}] with params ".json_encode($params).' but it was not made.'
        );

        return $this;
    }

    /**
     * Fake a drivers response.
     *
     * @param  array<int, array<string, mixed>>  $drivers
     */
    public function fakeDrivers(array $drivers): static
    {
        return $this->fakeResponse('/fleet/drivers', $drivers);
    }

    /**
     * Fake a response for an endpoint.
     *
     * @param  array<mixed>  $data
     */
    public function fakeResponse(string $endpoint, array $data, int $status = 200): static
    {
        $this->responses[$endpoint] = ['data' => $data, 'status' => $status];

        // Reset the HTTP factory and set up new fakes
        $this->fakeHttp = new HttpFactory;
        $this->setupFake();

        return $this;
    }

    /**
     * Fake a vehicles response.
     *
     * @param  array<int, array<string, mixed>>  $vehicles
     */
    public function fakeVehicles(array $vehicles): static
    {
        return $this->fakeResponse('/fleet/vehicles', $vehicles);
    }

    /**
     * Fake a vehicle stats response.
     *
     * @param  array<int, array<string, mixed>>  $stats
     */
    public function fakeVehicleStats(array $stats): static
    {
        return $this->fakeResponse('/fleet/vehicles/stats', $stats);
    }

    /**
     * Get the recorded requests.
     *
     * @return array<int, \Illuminate\Http\Client\Request>
     */
    public function getRecordedRequests(): array
    {
        return $this->fakeHttp->recorded()->map(function ($pair) {
            return $pair[0];
        })->all();
    }

    /**
     * Create a new SamsaraFake instance.
     */
    public static function create(): static
    {
        /** @phpstan-ignore new.static */
        return new static;
    }

    /**
     * Set up the fake HTTP client with recorded responses.
     */
    protected function setupFake(): void
    {
        $callbacks = [];

        foreach ($this->responses as $endpoint => $response) {
            $callbacks['*'.$endpoint.'*'] = $this->fakeHttp->response(
                ['data' => $response['data']],
                $response['status']
            );
        }

        // Default fallback response
        if (empty($callbacks)) {
            $callbacks['*'] = $this->fakeHttp->response(['data' => []], 200);
        }

        $this->fakeHttp->fake($callbacks);

        $this->setHttpFactory($this->fakeHttp);
    }
}
