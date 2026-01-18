<?php

namespace ErikGall\Samsara;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Samsara API client.
 *
 * Main entry point for interacting with the Samsara Fleet Management API.
 * Provides a fluent, Laravel-style interface for API operations.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class Samsara
{
    /**
     * The base URL for the EU region API.
     */
    protected const EU_BASE_URL = 'https://api.eu.samsara.com';

    /**
     * The base URL for the US region API.
     */
    protected const US_BASE_URL = 'https://api.samsara.com';

    /**
     * The API base URL.
     */
    protected string $baseUrl = self::US_BASE_URL;

    /**
     * The configuration array.
     *
     * @var array<string, mixed>
     */
    protected array $config = [];

    /**
     * The HTTP client factory.
     */
    protected HttpFactory $http;

    /**
     * Cached resource instances.
     *
     * @var array<string, object>
     */
    protected array $resources = [];

    /**
     * The API token.
     */
    protected ?string $token = null;

    /**
     * Create a new Samsara client instance.
     *
     * @param  string|null  $token  The API token
     * @param  array<string, mixed>  $config  Configuration options
     */
    public function __construct(?string $token = null, array $config = [])
    {
        $this->token = $token;
        $this->config = $config;
        $this->http = new HttpFactory;
    }

    /**
     * Get a configured HTTP client.
     */
    public function client(): PendingRequest
    {
        return $this->http->baseUrl($this->baseUrl)
            ->withToken($this->token ?? '')
            ->acceptJson()
            ->asJson()
            ->timeout($this->getConfig('timeout', 30))
            ->retry($this->getConfig('retry', 3), 100);
    }

    /**
     * Get the current base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get a configuration value.
     *
     * @param  string  $key  The configuration key
     * @param  mixed  $default  The default value if key doesn't exist
     * @return mixed
     */
    public function getConfig(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Get the HTTP factory instance.
     */
    public function getHttpFactory(): HttpFactory
    {
        return $this->http;
    }

    /**
     * Determine if a token has been set.
     */
    public function hasToken(): bool
    {
        return $this->token !== null;
    }

    /**
     * Set the HTTP factory instance.
     *
     * Useful for testing.
     *
     * @param  HttpFactory  $http  The HTTP factory
     */
    public function setHttpFactory(HttpFactory $http): static
    {
        $this->http = $http;

        return $this;
    }

    /**
     * Switch to the EU API endpoint.
     */
    public function useEuEndpoint(): static
    {
        $this->baseUrl = self::EU_BASE_URL;

        return $this;
    }

    /**
     * Switch to the US API endpoint.
     */
    public function useUsEndpoint(): static
    {
        $this->baseUrl = self::US_BASE_URL;

        return $this;
    }

    /**
     * Set the API token.
     *
     * @param  string  $token  The API token
     */
    public function withToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Create a new Samsara client instance.
     *
     * @param  string|null  $token  The API token
     * @param  array<string, mixed>  $config  Configuration options
     */
    public static function make(?string $token = null, array $config = []): static
    {
        /** @phpstan-ignore new.static */
        return new static($token, $config);
    }

    /**
     * Get or create a cached resource instance.
     *
     * @template T of object
     *
     * @param  class-string<T>  $class  The resource class
     * @return T
     */
    protected function resource(string $class): object
    {
        if (! isset($this->resources[$class])) {
            $this->resources[$class] = new $class($this);
        }

        /** @phpstan-ignore return.type */
        return $this->resources[$class];
    }
}
