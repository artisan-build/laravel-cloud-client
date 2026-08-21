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
            // `build.succeeded` is a MIDPOINT, not an end: the build finished
            // and the deployment phase has yet to run. It was missing here,
            // which made isComplete() true for it — so a poll that happened to
            // sample this status read a healthy deployment as finished-and-not-
            // successful. App\Cloud\Provisioner turns that into a thrown
            // "Laravel Cloud reported the deployment as build.succeeded" and
            // fails a deployment that was progressing normally.
            self::BuildSucceeded,
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
