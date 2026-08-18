<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Console\Commands;

use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use Illuminate\Console\Command;

/**
 * List environment variables for an environment.
 */
final class EnvironmentVariablesListCommand extends Command
{
    protected $signature = 'cloud:env:list {environment : The environment ID} {--json : Output as JSON}';

    protected $description = 'List environment variables for an environment';

    public function handle(LaravelCloudClient $client): int
    {
        /** @var string $environmentId */
        $environmentId = $this->argument('environment');

        $response = $client->environments()->listVariables($environmentId);

        if ($this->option('json')) {
            $this->line((string) json_encode($response->json(), JSON_PRETTY_PRINT));

            return self::SUCCESS;
        }

        /** @var array<int, array{key: string, value: string}> $variables */
        $variables = $response->json('data') ?? [];

        if ($variables === []) {
            $this->info('No environment variables found.');

            return self::SUCCESS;
        }

        $this->table(
            ['Key', 'Value'],
            array_map(fn (array $var) => [
                $var['key'],
                $var['value'],
            ], $variables)
        );

        return self::SUCCESS;
    }
}
