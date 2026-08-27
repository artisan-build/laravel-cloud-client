<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Support;

/**
 * One difference between the vendored spec and live, named precisely enough to
 * act on without opening either document.
 *
 * "The spec has changed" sends someone hunting through 850KB.
 * "DatabaseResource.relationships.schemas REMOVED" is a decision in one line.
 */
final readonly class DriftFinding
{
    public function __construct(
        public DriftSeverity $severity,
        public string $location,
        public ?string $detail = null,
    ) {}

    public function line(): string
    {
        $line = "{$this->severity->label()}  {$this->location}";

        return $this->detail === null ? $line : "{$line}  ({$this->detail})";
    }
}
