<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

/**
 * Database types supported by Laravel Cloud.
 */
enum DatabaseType: string
{
    case LaravelMySql84 = 'laravel_mysql_84';
    case LaravelMySql8 = 'laravel_mysql_8';
    case AwsRdsMySql8 = 'aws_rds_mysql_8';
    case AwsRdsPostgres18 = 'aws_rds_postgres_18';
    case NeonServerlessPostgres18 = 'neon_serverless_postgres_18';
    case NeonServerlessPostgres17 = 'neon_serverless_postgres_17';
    case NeonServerlessPostgres16 = 'neon_serverless_postgres_16';

    /**
     * Get the human-readable label for the database type.
     */
    public function label(): string
    {
        return match ($this) {
            self::LaravelMySql84 => 'Laravel MySQL 8.4',
            self::LaravelMySql8 => 'Laravel MySQL 8.0',
            self::AwsRdsMySql8 => 'AWS RDS MySQL 8',
            self::AwsRdsPostgres18 => 'AWS RDS Postgres 18',
            self::NeonServerlessPostgres18 => 'Neon Serverless Postgres 18',
            self::NeonServerlessPostgres17 => 'Neon Serverless Postgres 17',
            self::NeonServerlessPostgres16 => 'Neon Serverless Postgres 16',
        };
    }
}
