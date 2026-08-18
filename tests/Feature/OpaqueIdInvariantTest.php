<?php

declare(strict_types=1);

it('does not reintroduce numeric Laravel Cloud resource ids in package fixtures or docs', function (): void {
    $root = realpath(__DIR__.'/../..');

    expect($root)->toBeString();

    $paths = [
        $root.'/README.md',
        ...glob($root.'/tests/Fixtures/Responses/**/*.json'),
    ];

    $violations = [];

    foreach ($paths as $path) {
        $contents = file_get_contents($path);

        expect($contents)->toBeString();

        if (preg_match('/("(?:id|[a-z_]+_id)"\s*:\s*\d+|\b(?:application|environment|instance|process|cache|command|cluster|deployment|domain|bucket|accessKey|filesystemKey)?Id:\s*\d+|\$[A-Za-z0-9_]*Id\s*=\s*\d+|\bint\s+\$[A-Za-z0-9_]*Id\b)/', $contents) === 1) {
            $violations[] = str_replace($root.'/', '', $path);
        }
    }

    expect($violations)->toBe([]);
});
