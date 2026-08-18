<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Resource;

use ArtisanBuild\LaravelCloudClient\Exceptions\ApiException;
use ArtisanBuild\LaravelCloudClient\Exceptions\AuthenticationException;
use ArtisanBuild\LaravelCloudClient\Exceptions\NotFoundException;
use ArtisanBuild\LaravelCloudClient\Exceptions\RateLimitException;
use ArtisanBuild\LaravelCloudClient\Exceptions\ValidationException;
use ArtisanBuild\LaravelCloudClient\Requests\Meta\GetOrganization;
use ArtisanBuild\LaravelCloudClient\Requests\Meta\ListRegions;
use ArtisanBuild\LaravelCloudClient\Resource;
use Saloon\Http\Response;

/**
 * Resource for retrieving Laravel Cloud metadata.
 */
final class MetaResource extends Resource
{
    /**
     * Get organization metadata.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function organization(): Response
    {
        return $this->send(new GetOrganization);
    }

    /**
     * List available regions.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function regions(): Response
    {
        return $this->send(new ListRegions);
    }
}
