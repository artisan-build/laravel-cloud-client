<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient;

use ArtisanBuild\LaravelCloudClient\Exceptions\ApiException;
use ArtisanBuild\LaravelCloudClient\Exceptions\AuthenticationException;
use ArtisanBuild\LaravelCloudClient\Exceptions\NotFoundException;
use ArtisanBuild\LaravelCloudClient\Exceptions\RateLimitException;
use ArtisanBuild\LaravelCloudClient\Exceptions\ValidationException;
use ArtisanBuild\LaravelCloudClient\Resource\ApplicationsResource;
use ArtisanBuild\LaravelCloudClient\Resource\BackgroundProcessesResource;
use ArtisanBuild\LaravelCloudClient\Resource\CachesResource;
use ArtisanBuild\LaravelCloudClient\Resource\CommandsResource;
use ArtisanBuild\LaravelCloudClient\Resource\DatabaseClustersResource;
use ArtisanBuild\LaravelCloudClient\Resource\DeploymentsResource;
use ArtisanBuild\LaravelCloudClient\Resource\DomainsResource;
use ArtisanBuild\LaravelCloudClient\Resource\EnvironmentsResource;
use ArtisanBuild\LaravelCloudClient\Resource\InstancesResource;
use ArtisanBuild\LaravelCloudClient\Resource\MetaResource;
use ArtisanBuild\LaravelCloudClient\Resource\ObjectStorageResource;
use Saloon\Http\Connector;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AcceptsJson;

/**
 * Laravel Cloud API Client
 *
 * A complete PHP SDK for interacting with the Laravel Cloud API.
 */
final class LaravelCloudClient extends Connector
{
    use AcceptsJson;

    private const DEFAULT_BASE_URL = 'https://cloud.laravel.com/api';

    private string $apiToken;

    private string $baseUrl;

    private bool $retryOnRateLimit;

    private int $maxRetries;

    private int $timeout;

    /**
     * Create a new Laravel Cloud client instance.
     *
     * @throws AuthenticationException When no API token is available
     */
    public function __construct(
        ?string $apiToken = null,
        ?string $baseUrl = null,
        ?bool $retryOnRateLimit = null,
        ?int $maxRetries = null,
        ?int $timeout = null,
    ) {
        $this->apiToken = $this->resolveApiToken($apiToken);
        $this->baseUrl = $baseUrl ?? $this->resolveStringConfig('base_url', self::DEFAULT_BASE_URL);
        $this->retryOnRateLimit = $retryOnRateLimit ?? $this->resolveBoolConfig('retry_on_rate_limit', true);
        $this->maxRetries = $maxRetries ?? $this->resolveIntConfig('max_retries', 3);
        $this->timeout = $timeout ?? $this->resolveIntConfig('timeout', 30);
    }

    public function resolveBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @return array<string, string>
     */
    protected function defaultHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->apiToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/vnd.api+json, application/json',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultConfig(): array
    {
        return [
            'timeout' => $this->timeout,
        ];
    }

    /**
     * Handle the response and throw appropriate exceptions.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function handleResponse(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        $status = $response->status();
        $jsonMessage = $response->json('message');
        $message = is_string($jsonMessage) ? $jsonMessage : 'An error occurred';

        match ($status) {
            401 => throw new AuthenticationException($message, $response),
            404 => throw new NotFoundException($message, $response),
            422 => throw new ValidationException($message, $response),
            429 => throw new RateLimitException($message, $response),
            default => throw new ApiException($message, $response),
        };
    }

    /**
     * Send a request with automatic retry logic for rate limits.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function sendWithRetry(\Saloon\Http\Request $request): Response
    {
        $attempts = 0;

        while (true) {
            $response = $this->send($request);

            if ($response->status() !== 429 || ! $this->retryOnRateLimit || $attempts >= $this->maxRetries) {
                $this->handleResponse($response);

                return $response;
            }

            $retryAfter = (int) $response->header('Retry-After') ?: 1;
            sleep($retryAfter);
            $attempts++;
        }
    }

    /**
     * Get the Applications resource.
     */
    public function applications(): ApplicationsResource
    {
        return new ApplicationsResource($this);
    }

    /**
     * Get the Environments resource.
     */
    public function environments(): EnvironmentsResource
    {
        return new EnvironmentsResource($this);
    }

    /**
     * Get the Deployments resource.
     */
    public function deployments(): DeploymentsResource
    {
        return new DeploymentsResource($this);
    }

    /**
     * Get the Instances resource.
     */
    public function instances(): InstancesResource
    {
        return new InstancesResource($this);
    }

    /**
     * Get the Domains resource.
     */
    public function domains(): DomainsResource
    {
        return new DomainsResource($this);
    }

    /**
     * Get the Database Clusters resource.
     */
    public function databaseClusters(): DatabaseClustersResource
    {
        return new DatabaseClustersResource($this);
    }

    /**
     * Get the Background Processes resource.
     */
    public function backgroundProcesses(): BackgroundProcessesResource
    {
        return new BackgroundProcessesResource($this);
    }

    /**
     * Get the Commands resource.
     */
    public function commands(): CommandsResource
    {
        return new CommandsResource($this);
    }

    /**
     * Get the Object Storage resource.
     */
    public function objectStorage(): ObjectStorageResource
    {
        return new ObjectStorageResource($this);
    }

    /**
     * Get the Caches resource.
     */
    public function caches(): CachesResource
    {
        return new CachesResource($this);
    }

    /**
     * Get the Meta resource.
     */
    public function meta(): MetaResource
    {
        return new MetaResource($this);
    }

    /**
     * Resolve the API token from constructor parameter or config.
     *
     * @throws AuthenticationException
     */
    private function resolveApiToken(?string $apiToken): string
    {
        if ($apiToken !== null && $apiToken !== '') {
            return $apiToken;
        }

        $configToken = config('laravel-cloud-client.api_token');

        if (is_string($configToken) && $configToken !== '') {
            return $configToken;
        }

        throw new AuthenticationException(
            'No API token provided. Set LARAVEL_CLOUD_API_TOKEN environment variable or pass token to constructor.'
        );
    }

    private function resolveStringConfig(string $key, string $default): string
    {
        $value = config('laravel-cloud-client.'.$key, $default);

        return is_string($value) ? $value : $default;
    }

    private function resolveBoolConfig(string $key, bool $default): bool
    {
        $value = config('laravel-cloud-client.'.$key, $default);

        return is_bool($value) ? $value : $default;
    }

    private function resolveIntConfig(string $key, int $default): int
    {
        $value = config('laravel-cloud-client.'.$key, $default);

        return is_int($value) ? $value : $default;
    }
}
