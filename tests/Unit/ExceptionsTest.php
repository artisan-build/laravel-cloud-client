<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\Exceptions\ApiException;
use ArtisanBuild\LaravelCloudClient\Exceptions\AuthenticationException;
use ArtisanBuild\LaravelCloudClient\Exceptions\NotFoundException;
use ArtisanBuild\LaravelCloudClient\Exceptions\RateLimitException;
use ArtisanBuild\LaravelCloudClient\Exceptions\ValidationException;
use Saloon\Http\Response;

it('creates api exception with response', function () {
    $response = Mockery::mock(Response::class);
    $response->shouldReceive('status')->andReturn(500);

    $exception = new ApiException('Server error', $response);

    expect($exception->getMessage())->toBe('Server error');
    expect($exception->getResponse())->toBe($response);
});

it('extracts status code from api exception', function () {
    $response = Mockery::mock(Response::class);
    $response->shouldReceive('status')->andReturn(500);

    $exception = new ApiException('Server error', $response);

    expect($exception->getStatusCode())->toBe(500);
});

it('extracts response data from api exception', function () {
    $response = Mockery::mock(Response::class);
    $response->shouldReceive('status')->andReturn(422);
    $response->shouldReceive('json')->andReturn(['message' => 'Error', 'errors' => ['field' => ['Required']]]);

    $exception = new ApiException('Error', $response);

    expect($exception->getResponseData())->toBe(['message' => 'Error', 'errors' => ['field' => ['Required']]]);
});

it('creates authentication exception', function () {
    $response = Mockery::mock(Response::class);
    $response->shouldReceive('status')->andReturn(401);

    $exception = new AuthenticationException('Unauthenticated', $response);

    expect($exception->getMessage())->toBe('Unauthenticated');
    expect($exception->getStatusCode())->toBe(401);
});

it('creates rate limit exception with retry after', function () {
    $response = Mockery::mock(Response::class);
    $response->shouldReceive('status')->andReturn(429);
    $response->shouldReceive('header')->with('Retry-After')->andReturn('60');

    $exception = new RateLimitException('Too many requests', $response);

    expect($exception->getRetryAfter())->toBe(60);
});

it('creates validation exception', function () {
    $response = Mockery::mock(Response::class);
    $response->shouldReceive('status')->andReturn(422);
    $response->shouldReceive('json')->andReturn([
        'message' => 'Validation failed',
        'errors' => ['name' => ['Name is required']],
    ]);

    $exception = new ValidationException('Validation failed', $response);

    expect($exception->getMessage())->toBe('Validation failed');
    expect($exception->getStatusCode())->toBe(422);
    expect($exception->getResponseData()['errors'])->toBe(['name' => ['Name is required']]);
});

it('creates not found exception', function () {
    $response = Mockery::mock(Response::class);
    $response->shouldReceive('status')->andReturn(404);

    $exception = new NotFoundException('Not found', $response);

    expect($exception->getMessage())->toBe('Not found');
    expect($exception->getStatusCode())->toBe(404);
});
