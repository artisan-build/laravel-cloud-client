<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Environments;

use ArtisanBuild\LaravelCloudClient\Enums\PhpVersion;
use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Update an existing environment.
 */
final class UpdateEnvironment extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    private const UNSET = '__laravel_cloud_unset__';

    public function __construct(
        protected string $environmentId,
        protected string $name = self::UNSET,
        protected string $branch = self::UNSET,
        protected ?string $databaseSchemaId = self::UNSET,
        protected ?string $cacheId = self::UNSET,
        /** @var array<int, array{id: string, disk: string, is_default_disk: bool}>|null */
        protected ?array $filesystemKeys = null,
        // The command Cloud runs to BUILD the deployment. It takes the UNSET
        // sentinel rather than plain null because null is a meaningful value
        // here — it clears an override and restores Cloud's own default build
        // — and that is a different instruction from "leave it alone".
        protected ?string $buildCommand = self::UNSET,
        // The runtime the environment builds and runs on. Null leaves it at
        // whatever Cloud defaults to, which is why this is nullable rather than
        // using the UNSET sentinel the string fields need.
        protected ?PhpVersion $phpVersion = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('environments', $this->environmentId);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        $body = [];

        if ($this->name !== self::UNSET) {
            $body['name'] = $this->name;
        }

        if ($this->branch !== self::UNSET) {
            $body['branch'] = $this->branch;
        }

        if ($this->databaseSchemaId !== self::UNSET) {
            $body['database_schema_id'] = $this->databaseSchemaId === '' ? null : $this->databaseSchemaId;
        }

        if ($this->cacheId !== self::UNSET) {
            $body['cache_id'] = $this->cacheId === '' ? null : $this->cacheId;
        }

        if ($this->buildCommand !== self::UNSET) {
            $body['build_command'] = $this->buildCommand === '' ? null : $this->buildCommand;
        }

        if ($this->phpVersion instanceof PhpVersion) {
            $body['php_version'] = $this->phpVersion->value;
        }

        if ($this->filesystemKeys !== null) {
            // Empty arrays are sent deliberately so callers can clear attached filesystems.
            $body['filesystem_keys'] = $this->filesystemKeys;
        }

        return $body;
    }
}
