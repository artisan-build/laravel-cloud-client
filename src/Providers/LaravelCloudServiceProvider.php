<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Providers;

use ArtisanBuild\LaravelCloudClient\Console\Commands\ApplicationsListCommand;
use ArtisanBuild\LaravelCloudClient\Console\Commands\DeployCommand;
use ArtisanBuild\LaravelCloudClient\Console\Commands\DeploymentLogsCommand;
use ArtisanBuild\LaravelCloudClient\Console\Commands\DeploymentStatusCommand;
use ArtisanBuild\LaravelCloudClient\Console\Commands\EnvironmentVariableDeleteCommand;
use ArtisanBuild\LaravelCloudClient\Console\Commands\EnvironmentVariableSetCommand;
use ArtisanBuild\LaravelCloudClient\Console\Commands\EnvironmentVariablesListCommand;
use ArtisanBuild\LaravelCloudClient\Console\Commands\RefreshApiSpecCommand;
use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for the Laravel Cloud SDK.
 */
final class LaravelCloudServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/laravel-cloud-client.php',
            'laravel-cloud-client'
        );

        $this->app->singleton(LaravelCloudClient::class, function () {
            return new LaravelCloudClient;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/laravel-cloud-client.php' => config_path('laravel-cloud-client.php'),
            ], 'laravel-cloud-client-config');

            $this->commands([
                ApplicationsListCommand::class,
                DeployCommand::class,
                EnvironmentVariablesListCommand::class,
                EnvironmentVariableSetCommand::class,
                EnvironmentVariableDeleteCommand::class,
                DeploymentStatusCommand::class,
                DeploymentLogsCommand::class,
                RefreshApiSpecCommand::class,
            ]);
        }
    }
}
