<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

enum DaemonStrategyType: string
{
    case None = 'none';
    case GrowthRate = 'growth_rate';
    case QueueSize = 'queue_size';
}
