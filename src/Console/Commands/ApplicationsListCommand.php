<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Console\Commands;

use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use Illuminate\Console\Command;

/**
 * List all applications.
 */
final class ApplicationsListCommand extends Command
{
    protected $signature = 'cloud:applications:list {--json : Output as JSON}';

    protected $description = 'List all Laravel Cloud applications';

    public function handle(LaravelCloudClient $client): int
    {
        $response = $client->applications()->list();

        if ($this->option('json')) {
            $this->line((string) json_encode($response->json(), JSON_PRETTY_PRINT));

            return self::SUCCESS;
        }

        /** @var array<int, array{id: string, name: string, repository: string, branch: string|null}> $applications */
        $applications = $response->json('data') ?? [];

        if ($applications === []) {
            $this->info('No applications found.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Name', 'Repository', 'Branch'],
            array_map(fn (array $app) => [
                $app['id'],
                $app['name'],
                $app['repository'],
                $app['branch'] ?? 'main',
            ], $applications)
        );

        return self::SUCCESS;
    }
}
