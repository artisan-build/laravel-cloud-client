<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

enum InstanceScalingType: string
{
    case None = 'none';
    case Custom = 'custom';
    case Auto = 'auto';
}
