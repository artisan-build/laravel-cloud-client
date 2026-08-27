<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Support;

/**
 * Compares the vendored OpenAPI document against another copy of it — normally
 * the live one — and names what differs.
 *
 * This class NEVER writes the vendored spec and never fetches anything. It is
 * a pure comparison over two decoded documents, which is what makes it
 * testable against fabricated fixtures instead of against Cloud's uptime.
 * Refreshing is a separate, deliberate act (`cloud:api-spec:refresh`) because
 * pulling a new spec silently would change what every payload in this project
 * is validated against without anyone deciding to.
 *
 * The comparison is structural, not textual. A reformatted document is not
 * drift, and `diff` on 850KB of pretty-printed JSON cannot tell the two apart.
 */
final class ApiSpecDrift
{
    /**
     * A runaway guard, not a design limit. A genuinely restructured spec could
     * otherwise produce thousands of lines nobody will read; past this point
     * the honest report is "this needs eyes on the whole document".
     */
    private const int MAX_FINDINGS = 200;

    /**
     * @param  list<DriftFinding>  $findings
     */
    private function __construct(
        private readonly array $findings,
        private readonly bool $truncated,
    ) {}

    /**
     * @param  array<string, mixed>  $vendored
     * @param  array<string, mixed>  $live
     */
    public static function between(array $vendored, array $live): self
    {
        $findings = [];

        self::walk(
            self::section($vendored, 'paths'),
            self::section($live, 'paths'),
            'path',
            $findings,
        );

        self::walk(
            self::section($vendored, 'schemas'),
            self::section($live, 'schemas'),
            'schema',
            $findings,
        );

        usort($findings, fn (DriftFinding $a, DriftFinding $b): int => [$a->severity->rank(), $a->location]
            <=> [$b->severity->rank(), $b->location]);

        $truncated = count($findings) > self::MAX_FINDINGS;

        return new self(array_slice($findings, 0, self::MAX_FINDINGS), $truncated);
    }

    /**
     * @return list<DriftFinding>
     */
    public function findings(): array
    {
        return $this->findings;
    }

    public function hasDrift(): bool
    {
        return $this->findings !== [];
    }

    public function truncated(): bool
    {
        return $this->truncated;
    }

    /**
     * @return list<DriftFinding>
     */
    public function ofSeverity(DriftSeverity $severity): array
    {
        return array_values(array_filter(
            $this->findings,
            fn (DriftFinding $finding): bool => $finding->severity === $severity,
        ));
    }

    /**
     * The two sections worth comparing. Everything else in the document —
     * `info`, `servers`, `tags` — is prose about the API rather than the shape
     * this project validates payloads against, and reporting churn in it would
     * bury the parts that can cause an outage.
     *
     * @param  array<string, mixed>  $spec
     * @return array<string, mixed>
     */
    private static function section(array $spec, string $section): array
    {
        $value = $section === 'paths'
            ? ($spec['paths'] ?? [])
            : (is_array($spec['components'] ?? null) ? ($spec['components']['schemas'] ?? []) : []);

        return is_array($value) ? $value : [];
    }

    /**
     * @param  list<DriftFinding>  $findings
     */
    private static function walk(mixed $old, mixed $new, string $location, array &$findings): void
    {
        if (is_array($old) && is_array($new)) {
            if (array_is_list($old) && array_is_list($new)) {
                self::walkList($old, $new, $location, $findings);

                return;
            }

            self::walkMap($old, $new, $location, $findings);

            return;
        }

        if ($old !== $new) {
            $findings[] = new DriftFinding(
                DriftSeverity::Change,
                $location,
                self::render($old).' => '.self::render($new),
            );
        }
    }

    /**
     * @param  array<string, mixed>  $old
     * @param  array<string, mixed>  $new
     * @param  list<DriftFinding>  $findings
     */
    private static function walkMap(array $old, array $new, string $location, array &$findings): void
    {
        foreach (array_keys($old) as $key) {
            if (! array_key_exists($key, $new)) {
                $findings[] = new DriftFinding(DriftSeverity::Removal, self::join($location, $key));
            }
        }

        foreach (array_keys($new) as $key) {
            if (! array_key_exists($key, $old)) {
                $findings[] = new DriftFinding(DriftSeverity::Addition, self::join($location, $key));

                continue;
            }

            self::walk($old[$key], $new[$key], self::join($location, $key), $findings);
        }
    }

    /**
     * OpenAPI carries two very different kinds of list, and treating them the
     * same is what makes a drift report unreadable.
     *
     * Lists of OBJECTS are keyed collections wearing a list's clothes:
     * `parameters` is identified by `name`, `anyOf` by `$ref`. A plain set
     * difference over those reports one MODIFIED member as a removal plus an
     * addition — which is how a `include` enum quietly changing from
     * `schemas` to `databases` gets filed at removal severity twice and its
     * actual content truncated out of the line. So members are paired by
     * identity first and the pairs recursed into; only genuinely unpaired
     * members are removals or additions.
     *
     * Lists of SCALARS (`enum`, `required`) are compared as sets, because
     * OpenAPI carries no meaning in their order and reporting a reshuffle
     * would bury every real finding.
     *
     * @param  list<mixed>  $old
     * @param  list<mixed>  $new
     * @param  list<DriftFinding>  $findings
     */
    private static function walkList(array $old, array $new, string $location, array &$findings): void
    {
        $key = self::identityKey($old, $new);

        if ($key !== null) {
            self::walkKeyedList($old, $new, $key, $location, $findings);

            return;
        }

        $encode = static fn (mixed $item): string => (string) json_encode($item, JSON_UNESCAPED_SLASHES);

        $oldMembers = array_map($encode, $old);
        $newMembers = array_map($encode, $new);

        foreach (array_diff($oldMembers, $newMembers) as $member) {
            $findings[] = new DriftFinding(DriftSeverity::Removal, $location, 'dropped '.self::truncate($member));
        }

        foreach (array_diff($newMembers, $oldMembers) as $member) {
            $findings[] = new DriftFinding(DriftSeverity::Addition, $location, 'gained '.self::truncate($member));
        }
    }

    /**
     * @param  list<mixed>  $old
     * @param  list<mixed>  $new
     * @param  list<DriftFinding>  $findings
     */
    private static function walkKeyedList(array $old, array $new, string $key, string $location, array &$findings): void
    {
        $index = static function (array $items) use ($key): array {
            $indexed = [];

            foreach ($items as $item) {
                /** @var array<string, mixed> $item */
                $indexed[(string) $item[$key]] = $item;
            }

            return $indexed;
        };

        $oldIndexed = $index($old);
        $newIndexed = $index($new);

        foreach (array_keys($oldIndexed) as $identity) {
            if (! array_key_exists($identity, $newIndexed)) {
                $findings[] = new DriftFinding(DriftSeverity::Removal, "{$location}[{$identity}]");
            }
        }

        foreach ($newIndexed as $identity => $item) {
            if (! array_key_exists($identity, $oldIndexed)) {
                $findings[] = new DriftFinding(DriftSeverity::Addition, "{$location}[{$identity}]");

                continue;
            }

            self::walk($oldIndexed[$identity], $item, "{$location}[{$identity}]", $findings);
        }
    }

    /**
     * The key both lists are identified by, or null when they are not keyed
     * collections. A candidate qualifies only when EVERY member on both sides
     * carries it as a scalar and no side repeats a value — anything less and
     * pairing would silently drop or merge members, which is worse than the
     * set difference it replaces.
     *
     * @param  list<mixed>  $old
     * @param  list<mixed>  $new
     */
    private static function identityKey(array $old, array $new): ?string
    {
        $members = [...$old, ...$new];

        if ($members === []) {
            return null;
        }

        foreach (['name', '$ref', 'operationId'] as $candidate) {
            foreach ([$old, $new] as $side) {
                $seen = [];

                foreach ($side as $member) {
                    if (! is_array($member) || ! is_scalar($member[$candidate] ?? null)) {
                        continue 3;
                    }

                    $seen[] = (string) $member[$candidate];
                }

                if (count($seen) !== count(array_unique($seen))) {
                    continue 2;
                }
            }

            return $candidate;
        }

        return null;
    }

    /**
     * `properties` is structural noise in every OpenAPI path and appears in
     * roughly half the segments. Dropping it from the DISPLAY turns
     * `DatabaseResource.properties.relationships.properties.schemas` into
     * `DatabaseResource.relationships.schemas` — which is the difference
     * between a line someone reads and a line someone skips.
     */
    private static function join(string $location, string|int $key): string
    {
        if ($key === 'properties') {
            return $location;
        }

        return $location === 'path' || $location === 'schema'
            ? "{$location} {$key}"
            : "{$location}.{$key}";
    }

    private static function render(mixed $value): string
    {
        return self::truncate((string) json_encode($value, JSON_UNESCAPED_SLASHES));
    }

    private static function truncate(string $value): string
    {
        return mb_strlen($value) > 120 ? mb_substr($value, 0, 120).'…' : $value;
    }
}
