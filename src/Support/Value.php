<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Support;

use BackedEnum;

/**
 * Resolves an option that may be either a bundled enum case or a raw string.
 *
 * The enums in this package are a SNAPSHOT of the API's vocabulary taken from
 * the bundled schema, and Laravel Cloud adds sizes, engines and versions
 * without asking. Requiring an enum case at the request boundary would mean a
 * value the live API accepts today cannot be sent until this package is
 * released again — so the request classes accept either, and this is where the
 * two collapse into the one thing the wire actually carries.
 *
 * The API remains the authority on whether the value is real. That is not a
 * weakening: it was always the authority, and a stale local enum only ever
 * moved the rejection earlier and to the wrong place.
 */
final class Value
{
    public static function of(BackedEnum|string $value): string
    {
        return $value instanceof BackedEnum ? (string) $value->value : $value;
    }

    public static function ofNullable(BackedEnum|string|null $value): ?string
    {
        return $value === null ? null : self::of($value);
    }
}
