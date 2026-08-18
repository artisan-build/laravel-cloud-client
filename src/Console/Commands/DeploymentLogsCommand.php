<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Console\Commands;

use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use Illuminate\Console\Command;

/**
 * Show deployment logs.
 */
final class DeploymentLogsCommand extends Command
{
    protected $signature = 'cloud:logs {deployment : The deployment ID} {--json : Output as JSON}';

    protected $description = 'Show deployment logs';

    public function handle(LaravelCloudClient $client): int
    {
        /** @var string $deploymentId */
        $deploymentId = $this->argument('deployment');

        $response = $client->deployments()->logs($deploymentId);

        if ($this->option('json')) {
            $this->line((string) json_encode($response->json(), JSON_PRETTY_PRINT));

            return self::SUCCESS;
        }

        /** @var array{logs?: string}|null $data */
        $data = $response->json('data');

        if ($data === null || ! isset($data['logs'])) {
            $this->info('No logs available.');

            return self::SUCCESS;
        }

        $this->line($data['logs']);

        return self::SUCCESS;
    }
}
