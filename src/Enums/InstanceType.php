<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

/**
 * Instance types available in Laravel Cloud.
 */
enum InstanceType: string
{
    case Service = 'service';
    case ManagedQueue = 'managed_queue';

    /**
     * Get the human-readable label for the instance type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Service => 'Service',
            self::ManagedQueue => 'Managed Queue',
        };
    }
}
