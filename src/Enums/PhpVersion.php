<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Enums;

/**
 * PHP versions supported by Laravel Cloud.
 *
 * The backing values are the API's own representations, including the `:1`
 * runtime-revision suffix, and are taken verbatim from the bundled schema at
 * `resources/api-spec/api.json` (`components.schemas.PhpVersion`). They are
 * NOT bare version numbers: sending `8.4` where the API expects `8.4:1` is
 * rejected, and the mistake is invisible until a real provisioning call.
 *
 * `PhpVersionMatchesApiSpecTest` asserts these cases against that schema, so
 * the next time Cloud adds or retires a runtime the drift fails the suite
 * rather than being re-derived by hand.
 */
enum PhpVersion: string
{
    case Php82 = '8.2:1';
    case Php83 = '8.3:1';
    case Php84 = '8.4:1';
    case Php85 = '8.5:1';

    /**
     * The human version number, without the runtime-revision suffix. Use this
     * for display; never send it to the API.
     */
    public function label(): string
    {
        return match ($this) {
            self::Php82 => '8.2',
            self::Php83 => '8.3',
            self::Php84 => '8.4',
            self::Php85 => '8.5',
        };
    }

    /**
     * Resolve from a bare version number such as `8.4`, which is what humans
     * and composer constraints write. Returns null for a version Cloud cannot
     * run — including `8.1`, which the API no longer offers at all.
     */
    public static function tryFromLabel(string $version): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->label() === $version) {
                return $case;
            }
        }

        return null;
    }
}
