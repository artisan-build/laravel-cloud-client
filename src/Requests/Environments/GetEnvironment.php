<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Environments;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Get a specific environment by ID.
 *
 * ── THE INCLUDE IS NOT OPTIONAL ─────────────────────────────────────────────
 *
 * Asked without one, Cloud returns the environment's attributes and NO
 * `relationships` object whatsoever — not the key with empty data, the key is
 * simply absent. Anything reading `relationships.database` off a bare response
 * therefore sees null on every environment in existence, and reports a
 * perfectly well configured production environment as having no database, no
 * cache and no object storage.
 *
 * With the include, each requested relationship is always present: `data` is
 * the identifier when something is attached and null (or an empty list) when
 * nothing is. That is what makes "attached", "not attached" and "we did not
 * read it" three distinguishable answers instead of one.
 *
 * Cloud publishes the permitted names in its own 400: application, branch,
 * deployments, currentDeployment, primaryDomain, instances, database, cache,
 * buckets, websocketApplication. Nesting (`database.database`) is refused, so
 * the cluster behind an attached schema still costs a second request.
 */
final class GetEnvironment extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $environmentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('environments', $this->environmentId);
    }

    /**
     * @return array<string, string>
     */
    protected function defaultQuery(): array
    {
        // The three attachments an environment can carry. Instances are
        // deliberately NOT here: they come from their own listing, which
        // reports the size and scheduler flag this one would not.
        return ['include' => 'database,cache,buckets'];
    }
}
