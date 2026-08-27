<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Console\Commands;

use ArtisanBuild\LaravelCloudClient\Support\ApiSpecDrift;
use ArtisanBuild\LaravelCloudClient\Support\DriftFinding;
use ArtisanBuild\LaravelCloudClient\Support\DriftSeverity;
use Illuminate\Console\Command;
use JsonException;

/**
 * Reports how the vendored OpenAPI document differs from live. It does NOT
 * refresh it.
 *
 * That separation is the entire point. The vendored spec is what this project
 * validates every Cloud payload against, so replacing it changes what the code
 * considers correct — a decision a human makes after reading a diff, not a
 * side effect of a cron job. `cloud:api-spec:refresh` is the deliberate act;
 * this command only ever reads.
 *
 * It exists because the previous arrangement was that nothing noticed. The
 * vendored copy sat at its original harvest for months while Cloud removed the
 * `schemas` relationship underneath it, and the drift surfaced as a production
 * outage rather than as a line of output.
 *
 * NOT part of `pest` or `composer ready`, deliberately — see the class-level
 * note in the scheduled workflow. Every developer offline and every PR would
 * otherwise depend on Cloud being up.
 */
final class CheckApiSpecCommand extends Command
{
    /**
     * Distinct from FAILURE (1). "Drift found" and "the check could not run"
     * are different states and a caller must be able to tell them apart: a
     * check that did not run is not a pass, and it is not a finding either.
     */
    private const int COULD_NOT_RUN = 2;

    protected $signature = 'cloud:api-spec:check
        {--url=https://cloud.laravel.com/api-docs/api.json : OpenAPI document URL to compare against}
        {--against= : Compare against a local JSON file instead of fetching the URL}
        {--json : Output the findings as JSON}';

    protected $description = 'Report how the vendored Laravel Cloud OpenAPI document differs from live (never updates it)';

    public function handle(): int
    {
        $vendoredPath = __DIR__.'/../../../resources/api-spec/api.json';

        $vendored = $this->read($vendoredPath, "vendored spec at {$vendoredPath}");

        if ($vendored === null) {
            return self::COULD_NOT_RUN;
        }

        $against = $this->option('against');
        $source = is_string($against) && $against !== '' ? $against : (string) $this->option('url');

        $live = $this->read($source, "reference spec at {$source}");

        if ($live === null) {
            return self::COULD_NOT_RUN;
        }

        $drift = ApiSpecDrift::between($vendored, $live);

        return $this->option('json')
            ? $this->renderJson($drift, $source)
            : $this->render($drift, $source);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function read(string $source, string $describedAs): ?array
    {
        $contents = @file_get_contents($source);

        if ($contents === false) {
            $this->error("Could not read the {$describedAs}.");
            $this->line('The check did not run. This is not the same as "no drift".');

            return null;
        }

        try {
            $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->error("The {$describedAs} is not valid JSON: {$e->getMessage()}");

            return null;
        }

        if (! is_array($decoded)) {
            $this->error("The {$describedAs} is not a JSON object.");

            return null;
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }

    private function render(ApiSpecDrift $drift, string $source): int
    {
        if (! $drift->hasDrift()) {
            $this->info("The vendored Laravel Cloud spec matches {$source}.");

            return self::SUCCESS;
        }

        $removals = $drift->ofSeverity(DriftSeverity::Removal);
        $changes = $drift->ofSeverity(DriftSeverity::Change);
        $additions = $drift->ofSeverity(DriftSeverity::Addition);

        $this->newLine();
        $this->warn('The vendored Laravel Cloud spec has drifted from '.$source.'.');
        $this->newLine();

        $this->section(
            'REMOVED — treat as breaking. Something this project may still send or read is gone.',
            $removals,
        );
        $this->section(
            'CHANGED — a shape or status code moved. Check callers before refreshing.',
            $changes,
        );
        $this->section(
            'Added — new capability. Usually nothing to do.',
            $additions,
        );

        if ($drift->truncated()) {
            $this->newLine();
            $this->warn('Findings were truncated. The document has changed enough to need reading in full.');
        }

        $this->newLine();
        $this->line(sprintf(
            '%d removed, %d changed, %d added.',
            count($removals),
            count($changes),
            count($additions),
        ));
        $this->line('Nothing was updated. Run `cloud:api-spec:refresh` yourself once you have read the above.');

        return self::FAILURE;
    }

    /**
     * @param  list<DriftFinding>  $findings
     */
    private function section(string $heading, array $findings): void
    {
        if ($findings === []) {
            return;
        }

        $this->line($heading);

        foreach ($findings as $finding) {
            $this->line('  '.$finding->line());
        }

        $this->newLine();
    }

    private function renderJson(ApiSpecDrift $drift, string $source): int
    {
        $this->line((string) json_encode([
            'source' => $source,
            'drifted' => $drift->hasDrift(),
            'truncated' => $drift->truncated(),
            'findings' => array_map(fn (DriftFinding $finding): array => [
                'severity' => $finding->severity->value,
                'location' => $finding->location,
                'detail' => $finding->detail,
            ], $drift->findings()),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $drift->hasDrift() ? self::FAILURE : self::SUCCESS;
    }
}
