<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

/**
 * Deployment status values in Laravel Cloud.
 */
enum DeploymentStatus: string
{
    case Pending = 'pending';
    case BuildPending = 'build.pending';
    case BuildCreated = 'build.created';
    case BuildQueued = 'build.queued';
    case BuildRunning = 'build.running';
    case BuildSucceeded = 'build.succeeded';
    case BuildFailed = 'build.failed';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
    case DeploymentPending = 'deployment.pending';
    case DeploymentCreated = 'deployment.created';
    case DeploymentQueued = 'deployment.queued';
    case DeploymentRunning = 'deployment.running';
    case DeploymentSucceeded = 'deployment.succeeded';
    case DeploymentFailed = 'deployment.failed';

    /**
     * Check if the deployment is currently in progress.
     */
    public function isInProgress(): bool
    {
        return in_array($this, [
            self::Pending,
            self::BuildPending,
            self::BuildCreated,
            self::BuildQueued,
            self::BuildRunning,
            self::DeploymentPending,
            self::DeploymentCreated,
            self::DeploymentQueued,
            self::DeploymentRunning,
        ], true);
    }

    /**
     * Check if the deployment has completed (either successfully or failed).
     */
    public function isComplete(): bool
    {
        return ! $this->isInProgress();
    }

    /**
     * Check if the deployment completed successfully.
     */
    public function isSuccessful(): bool
    {
        return $this === self::DeploymentSucceeded;
    }
}
