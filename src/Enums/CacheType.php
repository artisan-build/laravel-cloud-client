<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

/**
 * Cache engines Laravel Cloud can provision.
 *
 * `StoreCacheRequest` REQUIRES this alongside the size, and it is not
 * derivable from the size: `aws_elasticache_redis` and `aws_elasticache_valkey`
 * share the entire `elasticache.*` size range, so a size on its own does not
 * identify a cache.
 *
 * Values are taken verbatim from the bundled schema at
 * `resources/api-spec/api.json` (`components.schemas.CacheType`).
 */
enum CacheType: string
{
    case UpstashRedis = 'upstash_redis';
    case LaravelValkey = 'laravel_valkey';
    case AwsElastiCacheRedis = 'aws_elasticache_redis';
    case AwsElastiCacheValkey = 'aws_elasticache_valkey';

    public function label(): string
    {
        return match ($this) {
            self::UpstashRedis => 'Upstash Redis',
            self::LaravelValkey => 'Laravel Valkey',
            self::AwsElastiCacheRedis => 'AWS ElastiCache Redis',
            self::AwsElastiCacheValkey => 'AWS ElastiCache Valkey',
        };
    }

    /**
     * Whether this engine offers the given size.
     *
     * The API schema does not express the pairing, but the size vocabulary is
     * partitioned by engine family and the prefix is what distinguishes them:
     * Upstash sizes are bare (`1gb`), Laravel Valkey sizes carry a `valkey-`
     * prefix, and both ElastiCache engines share the `elasticache.` range.
     */
    public function supports(CacheSize $size): bool
    {
        return match ($this) {
            self::UpstashRedis => ! str_starts_with($size->value, 'valkey-')
                && ! str_starts_with($size->value, 'elasticache.'),
            self::LaravelValkey => str_starts_with($size->value, 'valkey-'),
            self::AwsElastiCacheRedis, self::AwsElastiCacheValkey => str_starts_with($size->value, 'elasticache.'),
        };
    }

    /**
     * The sizes this engine offers.
     *
     * @return list<CacheSize>
     */
    public function sizes(): array
    {
        return array_values(array_filter(CacheSize::cases(), $this->supports(...)));
    }
}
