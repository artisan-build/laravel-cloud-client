<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Console\Commands;

use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use Illuminate\Console\Command;

/**
 * Trigger a deployment for an environment.
 */
final class DeployCommand extends Command
{
    protected $signature = 'cloud:deploy {environment : The environment ID} {--json : Output as JSON}';

    protected $description = 'Trigger a deployment for an environment';

    public function handle(LaravelCloudClient $client): int
    {
        /** @var string $environmentId */
        $environmentId = $this->argument('environment');

        $response = $client->deployments()->trigger($environmentId);

        if ($this->option('json')) {
            $this->line((string) json_encode($response->json(), JSON_PRETTY_PRINT));

            return self::SUCCESS;
        }

        /** @var array{id: string, status: string}|null $deployment */
        $deployment = $response->json('data');

        if ($deployment === null) {
            $this->error('Failed to trigger deployment.');

            return self::FAILURE;
        }

        $this->info("Deployment {$deployment['id']} triggered successfully.");
        $this->line("Status: {$deployment['status']}");

        return self::SUCCESS;
    }
}
