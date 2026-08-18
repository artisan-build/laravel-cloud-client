<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

enum SourceControlProviderType: string
{
    case GitHub = 'github';
    case GitLab = 'gitlab';
    case Bitbucket = 'bitbucket';
}
