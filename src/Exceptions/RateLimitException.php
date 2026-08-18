<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Exceptions;

/**
 * Exception for rate limit errors (429 responses).
 */
final class RateLimitException extends LaravelCloudException
{
    /**
     * Get the number of seconds to wait before retrying.
     */
    public function getRetryAfter(): int
    {
        if ($this->response === null) {
            return 0;
        }

        $retryAfter = $this->response->header('Retry-After');

        return is_numeric($retryAfter) ? (int) $retryAfter : 0;
    }
}
