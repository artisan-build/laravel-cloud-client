<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel Cloud API Token
    |--------------------------------------------------------------------------
    |
    | Your Laravel Cloud API token for authentication. Obtain this token from
    | your Laravel Cloud dashboard organization settings.
    |
    */

    'api_token' => env('LARAVEL_CLOUD_API_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Laravel Cloud API. You typically won't need to
    | change this unless you're using a different API endpoint for testing.
    |
    */

    'base_url' => env('LARAVEL_CLOUD_BASE_URL', 'https://cloud.laravel.com/api'),

    /*
    |--------------------------------------------------------------------------
    | Retry on Rate Limit
    |--------------------------------------------------------------------------
    |
    | When enabled, the SDK will automatically retry requests that receive
    | a 429 (Too Many Requests) response, respecting the Retry-After header.
    |
    */

    'retry_on_rate_limit' => env('LARAVEL_CLOUD_RETRY_ON_RATE_LIMIT', true),

    /*
    |--------------------------------------------------------------------------
    | Maximum Retries
    |--------------------------------------------------------------------------
    |
    | The maximum number of times to retry a request after receiving a
    | rate limit response before giving up and throwing an exception.
    |
    */

    'max_retries' => env('LARAVEL_CLOUD_MAX_RETRIES', 3),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The number of seconds to wait for a response before timing out.
    |
    */

    'timeout' => env('LARAVEL_CLOUD_TIMEOUT', 30),
];
