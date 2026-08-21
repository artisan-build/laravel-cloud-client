<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

/**
 * Instance types available in Laravel Cloud.
 *
 * All four the published API enumerates, and the two that were missing are not
 * academic: `app` is what Cloud actually calls an environment's application
 * instance, and it is the one carrying `uses_scheduler`. Code that knew only
 * about `service` read every real environment as having no scheduler.
 */
enum InstanceType: string
{
    /** The environment's application instance — the one the scheduler flag lives on. */
    case App = 'app';

    /** A long-running service instance. Carries `uses_scheduler` too. */
    case Service = 'service';

    /** A self-managed queue worker. Not something this project provisions. */
    case Queue = 'queue';

    case ManagedQueue = 'managed_queue';

    /**
     * Get the human-readable label for the instance type.
     */
    public function label(): string
    {
        return match ($this) {
            self::App => 'App',
            self::Service => 'Service',
            self::Queue => 'Queue',
            self::ManagedQueue => 'Managed Queue',
        };
    }

    /**
     * Whether an instance of this type is the one an environment's application
     * code runs on, and therefore the one whose `uses_scheduler` flag decides
     * whether the scheduler is running.
     *
     * Both spellings, because Cloud reports `app` for the instance it creates
     * with an environment and `service` elsewhere, and a reader that accepts
     * only one of them is wrong about half the accounts it looks at.
     */
    public function runsApplicationCode(): bool
    {
        return $this === self::App || $this === self::Service;
    }
}
