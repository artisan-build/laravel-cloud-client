<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Support;

/**
 * How much a single difference between the vendored spec and live is worth
 * someone's attention.
 *
 * The order is the whole point. A spec drifts mostly by growing — Cloud ships
 * endpoints constantly — and a report that lists a new endpoint beside a
 * removed relationship trains the reader to skim past both. Removals are the
 * outage class: the vendored copy kept documenting
 * `DatabaseResource.relationships.schemas` for months after Cloud deleted it,
 * and every reader who checked the bug in #89 against the spec was reassured
 * by it. Changes are the next tier — a status code moving from 204 to 202
 * breaks a caller that compares against the old one. Additions are almost
 * always somebody else's new feature.
 */
enum DriftSeverity: string
{
    case Removal = 'removal';
    case Change = 'change';
    case Addition = 'addition';

    /**
     * Lower sorts first. Findings are ranked by this and nothing else, so the
     * first line of a report is always the most expensive thing in it.
     */
    public function rank(): int
    {
        return match ($this) {
            self::Removal => 0,
            self::Change => 1,
            self::Addition => 2,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Removal => 'REMOVED',
            self::Change => 'CHANGED',
            self::Addition => 'added',
        };
    }
}
