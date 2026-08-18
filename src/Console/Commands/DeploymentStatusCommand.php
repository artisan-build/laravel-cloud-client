<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Console\Commands;

use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use Illuminate\Console\Command;

/**
 * Show deployment status.
 */
final class DeploymentStatusCommand extends Command
{
    protected $signature = 'cloud:status {deployment : The deployment ID} {--json : Output as JSON}';

    protected $description = 'Show deployment status';

    public function handle(LaravelCloudClient $client): int
    {
        /** @var string $deploymentId */
        $deploymentId = $this->argument('deployment');

        $response = $client->deployments()->get($deploymentId);

        if ($this->option('json')) {
            $this->line((string) json_encode($response->json(), JSON_PRETTY_PRINT));

            return self::SUCCESS;
        }

        /** @var array{id: string, status: string, commit: string|null, created_at: string}|null $deployment */
        $deployment = $response->json('data');

        if ($deployment === null) {
            $this->error('Deployment not found.');

            return self::FAILURE;
        }

        $this->info("Deployment #{$deployment['id']}");
        $this->line("Status: {$deployment['status']}");

        if (isset($deployment['commit'])) {
            $this->line("Commit: {$deployment['commit']}");
        }

        $this->line("Created: {$deployment['created_at']}");

        return self::SUCCESS;
    }
}
