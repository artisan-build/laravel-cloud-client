<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

/**
 * Environment types in Laravel Cloud.
 */
enum EnvironmentType: string
{
    case Production = 'production';
    case Staging = 'staging';
    case Development = 'development';
}
