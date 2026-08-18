<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

enum DomainVerificationMethod: string
{
    case PreVerification = 'pre_verification';
    case RealTime = 'real_time';
}
