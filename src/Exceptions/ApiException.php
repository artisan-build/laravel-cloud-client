<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Exceptions;

/**
 * Exception for general API errors (5xx responses and unhandled 4xx).
 */
final class ApiException extends LaravelCloudException {}
