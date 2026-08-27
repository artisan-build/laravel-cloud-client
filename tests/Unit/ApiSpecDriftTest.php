<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\Support\ApiSpecDrift;
use ArtisanBuild\LaravelCloudClient\Support\DriftFinding;
use ArtisanBuild\LaravelCloudClient\Support\DriftSeverity;

/*
 * The drift check is the thing that was missing when the vendored spec sat
 * stale for months, so its detection logic is what has to be proven — not the
 * fact that Cloud answers HTTP.
 *
 * Every test here runs against `tests/Fixtures/ApiSpec/{old,new}.json`, a
 * fabricated pair built to contain one of each thing that can happen: a
 * removed path, a removed schema, a removed property (the real
 * `DatabaseResource.relationships.schemas`), a removed enum member, a status
 * code that moved, a changed scalar, and additions. Nothing here touches the
 * network, so this suite says the same thing offline, on a plane, and while
 * Laravel Cloud is down.
 */

/**
 * @return array<string, mixed>
 */
function driftFixture(string $name): array
{
    /** @var array<string, mixed> $decoded */
    $decoded = json_decode(
        (string) file_get_contents(__DIR__.'/../Fixtures/ApiSpec/'.$name.'.json'),
        true,
        512,
        JSON_THROW_ON_ERROR,
    );

    return $decoded;
}

function driftBetweenFixtures(): ApiSpecDrift
{
    return ApiSpecDrift::between(driftFixture('old'), driftFixture('new'));
}

/**
 * @return list<string>
 */
function driftLocations(ApiSpecDrift $drift, DriftSeverity $severity): array
{
    return array_map(
        fn (DriftFinding $finding): string => $finding->location,
        $drift->ofSeverity($severity),
    );
}

it('reports no drift when the two documents are identical', function (): void {
    $drift = ApiSpecDrift::between(driftFixture('old'), driftFixture('old'));

    expect($drift->hasDrift())->toBeFalse()
        ->and($drift->findings())->toBe([]);
});

it('finds drift between the fabricated old and new specs', function (): void {
    expect(driftBetweenFixtures()->hasDrift())->toBeTrue();
});

it('names a removed path', function (): void {
    expect(driftLocations(driftBetweenFixtures(), DriftSeverity::Removal))
        ->toContain('path /retired-endpoint');
});

it('names a removed schema', function (): void {
    expect(driftLocations(driftBetweenFixtures(), DriftSeverity::Removal))
        ->toContain('schema RetiredResource');
});

/*
 * The one that cost a production outage. The vendored spec kept documenting
 * this relationship long after Cloud deleted it, and every reader who checked
 * the createDatabase bug against the spec was reassured by it.
 *
 * The location must read as `DatabaseResource.relationships.schemas` — the
 * `properties` segments OpenAPI puts between each level are dropped from the
 * display, because a finding nobody reads is worth the same as no finding.
 */
it('names a removed schema property in one actionable line', function (): void {
    expect(driftLocations(driftBetweenFixtures(), DriftSeverity::Removal))
        ->toContain('schema DatabaseResource.relationships.schemas');
});

it('names a removed status code', function (): void {
    expect(driftLocations(driftBetweenFixtures(), DriftSeverity::Removal))
        ->toContain('path /instances/{instance}/failed-jobs/{jobId}.delete.responses.204');
});

it('names an added path, schema and property', function (): void {
    expect(driftLocations(driftBetweenFixtures(), DriftSeverity::Addition))
        ->toContain('path /secrets')
        ->toContain('schema SecretResource')
        ->toContain('schema DatabaseResource.relationships.secrets');
});

it('reports a changed scalar with both the old and the new value', function (): void {
    $changed = collect(driftBetweenFixtures()->ofSeverity(DriftSeverity::Change))
        ->firstWhere(fn (DriftFinding $finding): bool => $finding->location === 'path /databases/clusters.get.description');

    expect($changed)->not->toBeNull()
        ->and($changed->detail)->toContain('The `schemas` relationship is deprecated.')
        ->and($changed->detail)->toContain('=>');
});

/*
 * `parameters` is a list of objects identified by `name`. Compared as a plain
 * set, changing one member's enum reports the whole member dropped and a whole
 * new one gained — two findings at the wrong severity, with the actual change
 * truncated out of both. Pairing by identity first is what turns it into the
 * single line that matters.
 */
it('pairs list members by identity instead of reporting a modified member as a removal and an addition', function (): void {
    $drift = driftBetweenFixtures();

    expect(driftLocations($drift, DriftSeverity::Removal))
        ->toContain('path /databases/clusters.get.parameters[include].schema.items.enum')
        ->not->toContain('path /databases/clusters.get.parameters');

    $enum = collect($drift->ofSeverity(DriftSeverity::Removal))
        ->firstWhere(fn (DriftFinding $finding): bool => $finding->location === 'path /databases/clusters.get.parameters[include].schema.items.enum');

    expect($enum->detail)->toBe('dropped "schemas"');
});

it('ranks every removal before every change, and every change before every addition', function (): void {
    $ranks = array_map(
        fn (DriftFinding $finding): int => $finding->severity->rank(),
        driftBetweenFixtures()->findings(),
    );

    $sorted = $ranks;
    sort($sorted);

    expect($ranks)->toBe($sorted)
        ->and($ranks)->toContain(DriftSeverity::Removal->rank())
        ->and($ranks)->toContain(DriftSeverity::Change->rank())
        ->and($ranks)->toContain(DriftSeverity::Addition->rank());
});

/*
 * Reformatting is not drift. `diff` on 850KB of pretty-printed JSON cannot
 * tell a reindented document from a changed one, which is exactly why this
 * compares structure instead of text.
 */
it('ignores key order and scalar list order', function (): void {
    $old = [
        'paths' => ['/a' => ['get' => ['tags' => ['one', 'two']]]],
        'components' => ['schemas' => ['A' => ['type' => 'object', 'title' => 'A']]],
    ];

    $new = [
        'components' => ['schemas' => ['A' => ['title' => 'A', 'type' => 'object']]],
        'paths' => ['/a' => ['get' => ['tags' => ['two', 'one']]]],
    ];

    expect(ApiSpecDrift::between($old, $new)->hasDrift())->toBeFalse();
});

it('treats a document with no paths or schemas as wholly removed rather than as a match', function (): void {
    $drift = ApiSpecDrift::between(driftFixture('old'), []);

    expect($drift->hasDrift())->toBeTrue()
        ->and(driftLocations($drift, DriftSeverity::Removal))
        ->toContain('path /retired-endpoint')
        ->toContain('schema RetiredResource');
});
