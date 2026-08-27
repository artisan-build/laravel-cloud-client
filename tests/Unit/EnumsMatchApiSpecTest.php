<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\Enums\CacheSize;
use ArtisanBuild\LaravelCloudClient\Enums\CacheStatus;
use ArtisanBuild\LaravelCloudClient\Enums\CacheType;
use ArtisanBuild\LaravelCloudClient\Enums\DatabaseStatus;
use ArtisanBuild\LaravelCloudClient\Enums\DatabaseType;
use ArtisanBuild\LaravelCloudClient\Enums\FilesystemStatus;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceScalingType;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceSize;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceType;
use ArtisanBuild\LaravelCloudClient\Enums\PhpVersion;
use ArtisanBuild\LaravelCloudClient\Enums\Region;

/*
 * The enums in this package are the API's vocabulary, and the bundled schema
 * is the only in-repo record of what that vocabulary actually is. When they
 * drift, nothing fails until a real provisioning call is rejected — which is
 * how PhpVersion came to ship bare `8.4` values the API has never accepted.
 * These tests make the drift fail here instead.
 */

/**
 * @return list<string>
 */
function apiSpecEnum(string $schema): array
{
    $spec = json_decode(
        (string) file_get_contents(__DIR__.'/../../resources/api-spec/api.json'),
        true,
        512,
        JSON_THROW_ON_ERROR,
    );

    $cases = $spec['components']['schemas'][$schema]['enum'] ?? null;

    expect($cases)->toBeArray()->not->toBeEmpty();

    return array_values($cases);
}

it('has php versions matching the bundled api spec', function (): void {
    // Values carry the API's `:1` runtime-revision suffix. A bare `8.4` here
    // would be silently rejected by Cloud at provisioning time.
    expect(array_column(PhpVersion::cases(), 'value'))
        ->toEqualCanonicalizing(apiSpecEnum('PhpVersion'));
});

it('exposes no php version the api has retired', function (): void {
    // 8.1 used to be a case and has no API counterpart at all.
    expect(PhpVersion::tryFrom('8.1'))->toBeNull()
        ->and(PhpVersion::tryFrom('8.1:1'))->toBeNull();
});

it('resolves php versions from a bare version number for display', function (): void {
    expect(PhpVersion::tryFromLabel('8.4'))->toBe(PhpVersion::Php84)
        ->and(PhpVersion::Php84->label())->toBe('8.4')
        ->and(PhpVersion::Php84->value)->toBe('8.4:1')
        ->and(PhpVersion::tryFromLabel('8.1'))->toBeNull();
});

it('has regions matching the bundled api spec', function (): void {
    // `GET /meta/regions` is the live list. This enum is only the fallback for
    // when that call cannot be made, so it still has to say what the schema
    // says — a fallback that is wrong is worse than no fallback.
    expect(array_column(Region::cases(), 'value'))
        ->toEqualCanonicalizing(apiSpecEnum('CloudRegion'));
});

it('labels every region', function (): void {
    foreach (Region::cases() as $region) {
        expect($region->label())->toContain($region->value);
    }
});

it('has cache types matching the bundled api spec', function (): void {
    expect(array_column(CacheType::cases(), 'value'))
        ->toEqualCanonicalizing(apiSpecEnum('CacheType'));
});

it('has cache sizes matching the bundled api spec', function (): void {
    expect(array_column(CacheSize::cases(), 'value'))
        ->toEqualCanonicalizing(apiSpecEnum('CacheSize'));
});

it('has database types matching the bundled api spec', function (): void {
    expect(array_column(DatabaseType::cases(), 'value'))
        ->toEqualCanonicalizing(apiSpecEnum('DatabaseType'));
});

it('has instance sizes matching the bundled api spec', function (): void {
    expect(array_column(InstanceSize::cases(), 'value'))
        ->toEqualCanonicalizing(apiSpecEnum('InstanceSize'));
});

it('has scaling types matching the bundled api spec', function (): void {
    expect(array_column(InstanceScalingType::cases(), 'value'))
        ->toEqualCanonicalizing(apiSpecEnum('InstanceScalingType'));
});

it('models no instance type the api does not define', function (): void {
    // KNOWN GAP, deliberately asserted as a subset rather than an equality:
    // the spec also lists `app` and `queue`, which this package does not
    // model. Adding them changes the vocabulary provisioning is written
    // against, which is PR-7's call, not a drift fix. This assertion still
    // fails if anyone invents a type the API has never defined.
    expect(array_column(InstanceType::cases(), 'value'))
        ->each->toBeIn(apiSpecEnum('InstanceType'));
});

it('partitions every cache size across the cache types exactly once', function (): void {
    // ElastiCache Redis and Valkey deliberately share the elasticache range —
    // that overlap is why a size alone cannot identify a cache — so the count
    // of (type, size) pairs exceeds the number of sizes, but every size must
    // belong to at least one type or the partitioning rule has gone stale.
    foreach (CacheSize::cases() as $size) {
        $owners = array_values(array_filter(
            CacheType::cases(),
            fn (CacheType $type): bool => $type->supports($size),
        ));

        expect($owners)->not->toBeEmpty();
    }

    expect(CacheType::UpstashRedis->sizes())->not->toBeEmpty()
        ->and(CacheType::LaravelValkey->sizes())->not->toBeEmpty()
        ->and(CacheType::AwsElastiCacheRedis->sizes())
        ->toBe(CacheType::AwsElastiCacheValkey->sizes());
});

/*
 * The three RESOURCE LIFECYCLES, which exist because Cloud builds a database
 * cluster, a cache and a bucket asynchronously and refuses to attach one that
 * is still `creating`. A caller waits on `isSettling()` and stops on anything
 * else, so a status the API has invented and this package has not modelled is
 * read as "unrecognised" at the seam and fails the run — loudly, but a run
 * nonetheless. These keep the vocabulary honest before it costs anyone that.
 */

it('has database statuses matching the bundled api spec', function (): void {
    expect(array_column(DatabaseStatus::cases(), 'value'))
        ->toEqualCanonicalizing(apiSpecEnum('DatabaseStatus'));
});

it('has cache statuses matching the bundled api spec', function (): void {
    expect(array_column(CacheStatus::cases(), 'value'))
        ->toEqualCanonicalizing(apiSpecEnum('CacheStatus'));
});

it('has object storage statuses matching the bundled api spec', function (): void {
    expect(array_column(FilesystemStatus::cases(), 'value'))
        ->toEqualCanonicalizing(apiSpecEnum('FilesystemStatus'));
});

it('treats exactly one status per resource as attachable', function (): void {
    // `available` and nothing else. The spec models plenty of states that
    // sound benign — `stopped`, `disabled`, `updating` — and attaching against
    // any of them is a guess about somebody's paid infrastructure.
    $ready = fn (array $cases): array => array_column(
        array_values(array_filter($cases, fn ($case): bool => $case->isReady())),
        'value',
    );

    expect($ready(DatabaseStatus::cases()))->toBe(['available'])
        ->and($ready(CacheStatus::cases()))->toBe(['available'])
        ->and($ready(FilesystemStatus::cases()))->toBe(['available']);
});

it('never counts a status as both ready and still settling', function (): void {
    // The two predicates drive a wait: ready ends it, settling continues it,
    // and neither ends the run with a failure. A state answering yes to both
    // would make the wait's outcome depend on the order they are asked in.
    foreach ([...DatabaseStatus::cases(), ...CacheStatus::cases(), ...FilesystemStatus::cases()] as $status) {
        expect($status->isReady() && $status->isSettling())->toBeFalse($status::class.'::'.$status->name);
    }
});

it('waits on every state a resource can leave on its own, and on nothing else', function (): void {
    // Written out rather than derived, because the interesting content is the
    // JUDGEMENT about each state and a derivation would just restate the enum.
    // `unknown` waits: it is Cloud declining to answer about a resource created
    // seconds ago, and the wait is bounded. Archiving does NOT — a cluster on
    // its way to `archived` is not on its way to `available`.
    $settling = fn (array $cases): array => array_column(
        array_values(array_filter($cases, fn ($case): bool => $case->isSettling())),
        'value',
    );

    expect($settling(DatabaseStatus::cases()))
        ->toEqualCanonicalizing(['creating', 'updating', 'restarting', 'upgrading', 'moving', 'restoring', 'unknown'])
        ->and($settling(CacheStatus::cases()))
        ->toEqualCanonicalizing(['creating', 'updating', 'unknown'])
        ->and($settling(FilesystemStatus::cases()))
        ->toEqualCanonicalizing(['creating', 'updating', 'unknown']);
});
