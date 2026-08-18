<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Console\Commands;

use Illuminate\Console\Command;

final class RefreshApiSpecCommand extends Command
{
    protected $signature = 'cloud:api-spec:refresh {--url=https://cloud.laravel.com/api-docs/api.json : OpenAPI document URL}';

    protected $description = 'Refresh the vendored Laravel Cloud OpenAPI document';

    public function handle(): int
    {
        $url = (string) $this->option('url');
        $path = __DIR__.'/../../../resources/api-spec/api.json';

        $contents = file_get_contents($url);

        if ($contents === false) {
            $this->error("Failed to download Laravel Cloud OpenAPI document from {$url}.");

            return self::FAILURE;
        }

        $json = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
        file_put_contents($path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);

        $this->info("Refreshed {$path}.");

        return self::SUCCESS;
    }
}
