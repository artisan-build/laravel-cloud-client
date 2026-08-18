<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Exceptions;

/**
 * Exception for authentication failures (401 responses or missing token).
 */
final class AuthenticationException extends LaravelCloudException {}
