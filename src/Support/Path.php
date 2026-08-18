<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Support;

use InvalidArgumentException;

final class Path
{
    public static function make(string ...$segments): string
    {
        return '/'.implode('/', array_map(self::segment(...), $segments));
    }

    public static function segment(string $value): string
    {
        if ($value === '' || str_contains($value, '\\') || preg_match('/[\/?#]|\.\.|%2f|%2F/', $value) === 1) {
            throw new InvalidArgumentException('Path segments must be non-empty opaque identifiers.');
        }

        return rawurlencode($value);
    }
}
