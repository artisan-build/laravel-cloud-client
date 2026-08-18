<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient;

use ArtisanBuild\LaravelCloudClient\Exceptions\ApiException;
use ArtisanBuild\LaravelCloudClient\Exceptions\AuthenticationException;
use ArtisanBuild\LaravelCloudClient\Exceptions\NotFoundException;
use ArtisanBuild\LaravelCloudClient\Exceptions\RateLimitException;
use ArtisanBuild\LaravelCloudClient\Exceptions\ValidationException;
use Saloon\Http\Request;
use Saloon\Http\Response;

/**
 * Abstract base class for all API resources.
 */
abstract class Resource
{
    public function __construct(
        protected LaravelCloudClient $client,
    ) {}

    /**
     * Send a request through the client with automatic retry logic.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    protected function send(Request $request): Response
    {
        return $this->client->sendWithRetry($request);
    }
}
