<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Console\Commands;

use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use Illuminate\Console\Command;

/**
 * Delete an environment variable.
 */
final class EnvironmentVariableDeleteCommand extends Command
{
    protected $signature = 'cloud:env:delete {environment : The environment ID} {key : The variable key} {--json : Output as JSON}';

    protected $description = 'Delete an environment variable';

    public function handle(LaravelCloudClient $client): int
    {
        /** @var string $environmentId */
        $environmentId = $this->argument('environment');
        /** @var string $key */
        $key = $this->argument('key');

        $response = $client->environments()->deleteVariable($environmentId, $key);

        if ($this->option('json')) {
            $this->line((string) json_encode(['success' => $response->status() === 204], JSON_PRETTY_PRINT));

            return self::SUCCESS;
        }

        $this->info("Environment variable '{$key}' deleted successfully.");

        return self::SUCCESS;
    }
}
