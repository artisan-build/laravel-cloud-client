<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

/**
 * PHP versions supported by Laravel Cloud.
 */
enum PhpVersion: string
{
    case Php81 = '8.1';
    case Php82 = '8.2';
    case Php83 = '8.3';
    case Php84 = '8.4';
}
