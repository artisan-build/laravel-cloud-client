<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Tests\Support;

use RuntimeException;

/**
 * Thrown when a test in this package tries to reach Laravel Cloud for real.
 *
 * A distinct class so the control case can assert this exact type. A probe that
 * cannot be told apart from an ordinary failure proves nothing.
 */
final class LiveCallAttempted extends RuntimeException
{
    public static function to(string $url): self
    {
        // The PATH only. A guard that printed the whole URL would be the very
        // leak the application layer works to prevent.
        return new self('A test attempted a live Laravel Cloud request to '.(parse_url($url, PHP_URL_PATH) ?: '/').'.');
    }
}
