<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;

/*
 * Every test here passes `--against` a local file, so the command never
 * resolves its `--url` and never reaches the network. That is the same
 * property the check is designed around: the ordinary suite must say the same
 * thing while Laravel Cloud is down.
 */

function vendoredSpecPath(): string
{
    return __DIR__.'/../../resources/api-spec/api.json';
}

function driftFixturePath(string $name): string
{
    return __DIR__.'/../Fixtures/ApiSpec/'.$name.'.json';
}

/**
 * Runs the check and hands back the exit code with the raw output.
 *
 * Deliberately not `$this->artisan(...)->expectsOutputToContain(...)`: that
 * helper matches line by line and CONSUMES each line it matches, so two
 * expectations that both live on one rendered line report the second as
 * missing when it is plainly there. Reading the buffer avoids asserting on an
 * artefact of the assertion helper.
 *
 * @param  array<string, mixed>  $options
 * @return array{int, string}
 */
function runSpecCheck(array $options): array
{
    $status = Artisan::call('cloud:api-spec:check', $options);

    return [$status, Artisan::output()];
}

it('exits zero and says so when the vendored spec matches the reference', function (): void {
    [$status, $output] = runSpecCheck(['--against' => vendoredSpecPath()]);

    expect($status)->toBe(0)
        ->and($output)->toContain('matches');
});

it('exits one and names the drift when the reference differs', function (): void {
    [$status, $output] = runSpecCheck(['--against' => driftFixturePath('old')]);

    expect($status)->toBe(1)
        ->and($output)->toContain('has drifted')
        ->and($output)->toContain('REMOVED');
});

/*
 * A check that could not run is not a pass and is not a finding. If this
 * returned SUCCESS on an unreachable reference, a scheduled run would go green
 * every time Cloud had an outage — which is precisely the silence this whole
 * exercise exists to remove. Exit 2 keeps "could not run" distinguishable from
 * exit 1, "drifted".
 */
it('exits two, distinctly from drift, when the reference cannot be read', function (): void {
    [$status, $output] = runSpecCheck(['--against' => '/no/such/spec.json']);

    expect($status)->toBe(2)
        ->and($output)->toContain('did not run');
});

it('exits two when the reference is not valid JSON', function (): void {
    $notJson = tempnam(sys_get_temp_dir(), 'spec').'.json';
    file_put_contents($notJson, '<html>an outage page, not a spec</html>');

    try {
        [$status, $output] = runSpecCheck(['--against' => $notJson]);

        expect($status)->toBe(2)
            ->and($output)->toContain('not valid JSON');
    } finally {
        @unlink($notJson);
    }
});

/*
 * THE load-bearing guarantee. Silently pulling a new spec would change what
 * every Cloud payload in this project is validated against without anyone
 * deciding to — so the check reports and stops, and refreshing stays a
 * deliberate act someone performs after reading the diff.
 */
it('never writes the vendored spec, even when it finds drift', function (): void {
    $before = (string) md5_file(vendoredSpecPath());

    [$status] = runSpecCheck(['--against' => driftFixturePath('old')]);

    expect($status)->toBe(1)
        ->and(md5_file(vendoredSpecPath()))->toBe($before);
});

it('tells the reader that nothing was updated and who has to act', function (): void {
    [$status, $output] = runSpecCheck(['--against' => driftFixturePath('old')]);

    expect($status)->toBe(1)
        ->and($output)->toContain('Nothing was updated')
        ->and($output)->toContain('cloud:api-spec:refresh');
});

it('emits machine-readable findings with --json', function (): void {
    [$status, $output] = runSpecCheck([
        '--against' => driftFixturePath('old'),
        '--json' => true,
    ]);

    /** @var array<string, mixed> $decoded */
    $decoded = json_decode($output, true, 512, JSON_THROW_ON_ERROR);

    expect($status)->toBe(1)
        ->and($decoded['drifted'])->toBeTrue()
        ->and($decoded['findings'][0]['severity'])->toBe('removal')
        ->and($decoded['findings'][0]['location'])->toStartWith('path ');
});
