<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

enum DaemonType: string
{
    case Worker = 'worker';
    case Custom = 'custom';
}
