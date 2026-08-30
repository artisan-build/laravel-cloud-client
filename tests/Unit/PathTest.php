<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\Support\Path;

it('encodes safe opaque path segments', function (): void {
    expect(Path::make('applications', 'app-01k7app000000000000000001'))->toBe('/applications/app-01k7app000000000000000001');
    expect(Path::make('domains', 'custom domain'))->toBe('/domains/custom%20domain');
});

it('rejects unsafe opaque path segments', function (string $segment): void {
    expect(fn (): string => Path::make('applications', $segment))->toThrow(
        InvalidArgumentException::class,
        'Path segments must be non-empty opaque identifiers.',
    );
})->with([
    'empty string' => [''],
    'slash' => ['app/id'],
    'backslash' => ['app\id'],
    'parent traversal' => ['..'],
    'nested traversal' => ['app..id'],
    'encoded lowercase slash' => ['app%2fid'],
    'encoded uppercase slash' => ['app%2Fid'],
    'query marker' => ['app?id=1'],
    'fragment marker' => ['app#fragment'],
]);
