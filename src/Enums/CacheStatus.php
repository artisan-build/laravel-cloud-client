<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

use ArtisanBuild\LaravelCloudClient\Contracts\ResourceStatus;

/**
 * The lifecycle of a cache, as `GET /caches/{id}` reports it.
 *
 * Created asynchronously, exactly like a database cluster: `creating` is a
 * real state the API models, so a cache can be named before it exists well
 * enough to be attached. See DatabaseStatus for what the three predicates
 * mean and why `unknown` waits.
 */
enum CacheStatus: string implements ResourceStatus
{
    case Creating = 'creating';
    case Updating = 'updating';
    case Available = 'available';
    case Stopped = 'stopped';
    case Deleting = 'deleting';
    case Deleted = 'deleted';
    case Unknown = 'unknown';

    public function isReady(): bool
    {
        return $this === self::Available;
    }

    public function isSettling(): bool
    {
        return in_array($this, [
            self::Creating,
            self::Updating,
            self::Unknown,
        ], true);
    }
}
