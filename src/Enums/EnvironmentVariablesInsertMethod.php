<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

enum EnvironmentVariablesInsertMethod: string
{
    case Append = 'append';
    case Set = 'set';
}
