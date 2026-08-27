<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

use ArtisanBuild\LaravelCloudClient\Contracts\ResourceStatus;

/**
 * The lifecycle of a database CLUSTER, as `GET /databases/clusters/{id}`
 * reports it.
 *
 * A cluster is created ASYNCHRONOUSLY: the POST returns an identifier while
 * the cluster is still `creating`, and anything that names it before it
 * reaches `available` is rejected. That is the whole reason this enum exists.
 *
 * The three-way split matters more than the values. `isReady()` is the only
 * state in which the cluster can be attached to an environment; `isSettling()`
 * is every state it can leave on its own, and is therefore worth waiting on;
 * anything that is neither is a state waiting cannot fix, so a caller should
 * stop rather than sit out its ceiling.
 */
enum DatabaseStatus: string implements ResourceStatus
{
    case Creating = 'creating';
    case Updating = 'updating';
    case Restarting = 'restarting';
    case Upgrading = 'upgrading';
    case Moving = 'moving';
    case Available = 'available';
    case Stopped = 'stopped';
    case Restoring = 'restoring';
    case RestoreFailed = 'restore_failed';
    case Disabled = 'disabled';
    case SnapshottingBeforeArchiving = 'snapshotting_before_archiving';
    case Archiving = 'archiving';
    case Archived = 'archived';
    case Deleting = 'deleting';
    case Deleted = 'deleted';
    case Unknown = 'unknown';

    /**
     * Usable — and therefore attachable.
     */
    public function isReady(): bool
    {
        return $this === self::Available;
    }

    /**
     * On its way somewhere, and able to reach `available` without anyone
     * intervening.
     *
     * `unknown` is counted in deliberately. It is Cloud declining to answer
     * rather than an answer, and treating a momentary "I don't know" about a
     * cluster that was created seconds ago as a hard failure would turn a
     * reporting blip into a failed install. Waiting is bounded, so the cost of
     * being wrong is a timeout that names the cluster.
     *
     * Every archiving state is EXCLUDED even though it is transient: a cluster
     * on its way to `archived` is not on its way to `available`.
     */
    public function isSettling(): bool
    {
        return in_array($this, [
            self::Creating,
            self::Updating,
            self::Restarting,
            self::Upgrading,
            self::Moving,
            self::Restoring,
            self::Unknown,
        ], true);
    }
}
