<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Console\Commands;

use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use Illuminate\Console\Command;

/**
 * Set an environment variable.
 */
final class EnvironmentVariableSetCommand extends Command
{
    protected $signature = 'cloud:env:set {environment : The environment ID} {key : The variable key} {value : The variable value} {--json : Output as JSON}';

    protected $description = 'Set an environment variable';

    public function handle(LaravelCloudClient $client): int
    {
        /** @var string $environmentId */
        $environmentId = $this->argument('environment');
        /** @var string $key */
        $key = $this->argument('key');
        /** @var string $value */
        $value = $this->argument('value');

        $response = $client->environments()->createVariable($environmentId, $key, $value);

        if ($this->option('json')) {
            $this->line((string) json_encode($response->json(), JSON_PRETTY_PRINT));

            return self::SUCCESS;
        }

        $this->info("Environment variable '{$key}' set successfully.");

        return self::SUCCESS;
    }
}
