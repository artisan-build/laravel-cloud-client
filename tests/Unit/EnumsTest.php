<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\Enums\CacheSize;
use ArtisanBuild\LaravelCloudClient\Enums\CommandStatus;
use ArtisanBuild\LaravelCloudClient\Enums\DaemonStrategyType;
use ArtisanBuild\LaravelCloudClient\Enums\DaemonType;
use ArtisanBuild\LaravelCloudClient\Enums\DatabaseType;
use ArtisanBuild\LaravelCloudClient\Enums\DeploymentStatus;
use ArtisanBuild\LaravelCloudClient\Enums\DomainVerificationMethod;
use ArtisanBuild\LaravelCloudClient\Enums\EnvironmentType;
use ArtisanBuild\LaravelCloudClient\Enums\EnvironmentVariablesInsertMethod;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceScalingType;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceSize;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceType;
use ArtisanBuild\LaravelCloudClient\Enums\PhpVersion;
use ArtisanBuild\LaravelCloudClient\Enums\SourceControlProviderType;

it('has correct database type values', function () {
    expect(array_map(fn (DatabaseType $type): string => $type->value, DatabaseType::cases()))->toBe([
        'laravel_mysql_84',
        'laravel_mysql_8',
        'aws_rds_mysql_8',
        'aws_rds_postgres_18',
        'neon_serverless_postgres_18',
        'neon_serverless_postgres_17',
        'neon_serverless_postgres_16',
    ]);
});

it('provides database type labels', function () {
    expect(DatabaseType::LaravelMySql84->label())->toBe('Laravel MySQL 8.4');
    expect(DatabaseType::NeonServerlessPostgres18->label())->toBe('Neon Serverless Postgres 18');
});

it('has correct instance type values', function () {
    expect(array_map(fn (InstanceType $type): string => $type->value, InstanceType::cases()))->toBe([
        'service',
        'managed_queue',
    ]);
});

it('provides instance type labels', function () {
    expect(InstanceType::Service->label())->toBe('Service');
    expect(InstanceType::ManagedQueue->label())->toBe('Managed Queue');
});

it('pins documented cache size values and excludes old invalid values', function () {
    expect(array_map(fn (CacheSize $size): string => $size->value, CacheSize::cases()))->toBe([
        '250mb',
        '1gb',
        '2.5gb',
        '5gb',
        '12gb',
        '50gb',
        '100gb',
        '500gb',
        'valkey-flex-250mb',
        'valkey-flex-1gb',
        'valkey-flex-2.5gb',
        'valkey-pro.250mb',
        'valkey-pro.1gb',
        'valkey-pro.2.5gb',
        'valkey-pro.5gb',
        'valkey-pro.12gb',
        'valkey-pro.25gb',
        'valkey-pro.50gb',
        'elasticache.cache.r7g.large',
        'elasticache.cache.r7g.xlarge',
        'elasticache.cache.r7g.2xlarge',
        'elasticache.cache.r7g.4xlarge',
        'elasticache.cache.r7g.8xlarge',
        'elasticache.cache.r7g.12xlarge',
        'elasticache.cache.r7g.16xlarge',
        'elasticache.cache.c7gn.large',
        'elasticache.cache.c7gn.xlarge',
        'elasticache.cache.c7gn.2xlarge',
        'elasticache.cache.c7gn.4xlarge',
        'elasticache.cache.c7gn.8xlarge',
        'elasticache.cache.c7gn.12xlarge',
        'elasticache.cache.c7gn.16xlarge',
        'elasticache.cache.t4g.micro',
        'elasticache.cache.t4g.small',
        'elasticache.cache.t4g.medium',
        'elasticache.cache.m7g.large',
        'elasticache.cache.m7g.xlarge',
        'elasticache.cache.m7g.2xlarge',
        'elasticache.cache.m7g.4xlarge',
        'elasticache.cache.m7g.8xlarge',
        'elasticache.cache.m7g.12xlarge',
        'elasticache.cache.m7g.16xlarge',
        'elasticache.cache.m8g.large',
        'elasticache.cache.m8g.xlarge',
        'elasticache.cache.m8g.2xlarge',
        'elasticache.cache.m8g.4xlarge',
        'elasticache.cache.m8g.8xlarge',
        'elasticache.cache.m8g.12xlarge',
        'elasticache.cache.m8g.16xlarge',
        'elasticache.cache.r8g.large',
        'elasticache.cache.r8g.xlarge',
        'elasticache.cache.r8g.2xlarge',
        'elasticache.cache.r8g.4xlarge',
        'elasticache.cache.r8g.8xlarge',
        'elasticache.cache.r8g.12xlarge',
        'elasticache.cache.r8g.16xlarge',
        'elasticache.cache.c8gn.large',
        'elasticache.cache.c8gn.xlarge',
        'elasticache.cache.c8gn.2xlarge',
        'elasticache.cache.c8gn.4xlarge',
        'elasticache.cache.c8gn.8xlarge',
        'elasticache.cache.c8gn.12xlarge',
        'elasticache.cache.c8gn.16xlarge',
    ]);
});

it('pins documented instance size values', function () {
    expect(array_map(fn (InstanceSize $size): string => $size->value, InstanceSize::cases()))->toBe([
        'flex-512mb',
        'flex-1gb',
        'flex-2gb',
        'flex.c-1vcpu-256mb',
        'flex.g-1vcpu-512mb',
        'flex.m-1vcpu-1gb',
        'flex.c-2vcpu-512mb',
        'flex.g-2vcpu-1gb',
        'flex.m-2vcpu-2gb',
        'flex.c-4vcpu-1gb',
        'flex.g-4vcpu-2gb',
        'flex.m-4vcpu-4gb',
        'flex.c-8vcpu-2gb',
        'flex.g-8vcpu-4gb',
        'flex.m-8vcpu-8gb',
        'pro.c-1vcpu-1gb',
        'pro.g-1vcpu-2gb',
        'pro.m-1vcpu-4gb',
        'pro.c-2vcpu-2gb',
        'pro.g-2vcpu-4gb',
        'pro.m-2vcpu-8gb',
        'pro.c-4vcpu-4gb',
        'pro.g-4vcpu-8gb',
        'pro.m-4vcpu-16gb',
        'pro.c-8vcpu-8gb',
        'pro.g-8vcpu-16gb',
        'pro.m-8vcpu-32gb',
        'dedicated.c-1vcpu-2gb',
        'dedicated.g-1vcpu-4gb',
        'dedicated.m-1vcpu-8gb',
        'dedicated.c-2vcpu-4gb',
        'dedicated.g-2vcpu-8gb',
        'dedicated.m-2vcpu-16gb',
        'dedicated.c-4vcpu-8gb',
        'dedicated.g-4vcpu-16gb',
        'dedicated.m-4vcpu-32gb',
        'dedicated.c-8vcpu-16gb',
        'dedicated.g-8vcpu-32gb',
        'dedicated.m-8vcpu-64gb',
        'mq-pro-256mb',
        'mq-pro-512mb',
        'mq-pro-1gb',
        'mq-pro-2gb',
        'mq-pro-4gb',
        'mq-pro-8gb',
        'mq-dedicated-256mb',
        'mq-dedicated-512mb',
        'mq-dedicated-1gb',
        'mq-dedicated-2gb',
        'mq-dedicated-4gb',
        'mq-dedicated-8gb',
        'mq-dedicated-16gb',
        'mq.flex.256mb',
        'mq.flex.512mb',
        'mq.flex.1gb',
        'mq.flex.2gb',
        'mq.pro.256mb',
        'mq.pro.512mb',
        'mq.pro.1gb',
        'mq.pro.2gb',
        'mq.pro.4gb',
        'mq.pro.8gb',
        'mq.dedicated.flex.256mb',
        'mq.dedicated.flex.512mb',
        'mq.dedicated.flex.1gb',
        'mq.dedicated.flex.2gb',
        'mq.dedicated.pro.256mb',
        'mq.dedicated.pro.512mb',
        'mq.dedicated.pro.1gb',
        'mq.dedicated.pro.2gb',
        'mq.dedicated.pro.4gb',
        'mq.dedicated.pro.8gb',
        'mq.dedicated.pro.16gb',
    ]);
});

it('pins documented supporting enum values', function () {
    expect(array_map(fn (InstanceScalingType $type): string => $type->value, InstanceScalingType::cases()))->toBe(['none', 'custom', 'auto']);
    expect(array_map(fn (SourceControlProviderType $type): string => $type->value, SourceControlProviderType::cases()))->toBe(['github', 'gitlab', 'bitbucket']);
    expect(array_map(fn (EnvironmentVariablesInsertMethod $method): string => $method->value, EnvironmentVariablesInsertMethod::cases()))->toBe(['append', 'set']);
    expect(array_map(fn (DomainVerificationMethod $method): string => $method->value, DomainVerificationMethod::cases()))->toBe(['pre_verification', 'real_time']);
});

it('pins documented command and daemon enum values', function () {
    expect(array_map(fn (CommandStatus $status): string => $status->value, CommandStatus::cases()))->toBe([
        'pending',
        'command.created',
        'command.running',
        'command.failure',
        'command.success',
    ]);
    expect(array_map(fn (DaemonType $type): string => $type->value, DaemonType::cases()))->toBe(['worker', 'custom']);
    expect(array_map(fn (DaemonStrategyType $type): string => $type->value, DaemonStrategyType::cases()))->toBe(['none', 'growth_rate', 'queue_size']);
});

it('has correct deployment status values', function () {
    expect(array_map(fn (DeploymentStatus $status): string => $status->value, DeploymentStatus::cases()))->toBe([
        'pending',
        'build.pending',
        'build.created',
        'build.queued',
        'build.running',
        'build.succeeded',
        'build.failed',
        'cancelled',
        'failed',
        'deployment.pending',
        'deployment.created',
        'deployment.queued',
        'deployment.running',
        'deployment.succeeded',
        'deployment.failed',
    ]);
});

it('checks if deployment is in progress', function () {
    expect(DeploymentStatus::Pending->isInProgress())->toBeTrue();
    expect(DeploymentStatus::DeploymentRunning->isInProgress())->toBeTrue();
    expect(DeploymentStatus::DeploymentSucceeded->isInProgress())->toBeFalse();
    expect(DeploymentStatus::Failed->isInProgress())->toBeFalse();
});

it('checks if deployment is complete', function () {
    expect(DeploymentStatus::Pending->isComplete())->toBeFalse();
    expect(DeploymentStatus::DeploymentRunning->isComplete())->toBeFalse();
    expect(DeploymentStatus::DeploymentSucceeded->isComplete())->toBeTrue();
    expect(DeploymentStatus::Failed->isComplete())->toBeTrue();
});

it('checks if deployment is successful', function () {
    expect(DeploymentStatus::Pending->isSuccessful())->toBeFalse();
    expect(DeploymentStatus::DeploymentRunning->isSuccessful())->toBeFalse();
    expect(DeploymentStatus::DeploymentSucceeded->isSuccessful())->toBeTrue();
    expect(DeploymentStatus::Failed->isSuccessful())->toBeFalse();
});

it('has correct environment type values', function () {
    expect(EnvironmentType::Production->value)->toBe('production');
    expect(EnvironmentType::Staging->value)->toBe('staging');
    expect(EnvironmentType::Development->value)->toBe('development');
});

it('has correct php version values', function () {
    expect(PhpVersion::Php81->value)->toBe('8.1');
    expect(PhpVersion::Php82->value)->toBe('8.2');
    expect(PhpVersion::Php83->value)->toBe('8.3');
    expect(PhpVersion::Php84->value)->toBe('8.4');
});
