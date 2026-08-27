<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

use ArtisanBuild\LaravelCloudClient\Contracts\ResourceStatus;

/**
 * The lifecycle of an object-storage bucket, as `GET /buckets/{id}` reports
 * it.
 *
 * The bucket rather than its ACCESS KEY, which is what actually gets attached:
 * `FilesystemKeyResource` carries no status at all, so the bucket the key
 * belongs to is the only thing in this corner of the API with a lifecycle to
 * wait on. See DatabaseStatus for what the three predicates mean.
 */
enum FilesystemStatus: string implements ResourceStatus
{
    case Creating = 'creating';
    case Updating = 'updating';
    case Available = 'available';
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
