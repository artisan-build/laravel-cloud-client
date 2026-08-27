<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Contracts;

use BackedEnum;

/**
 * The lifecycle of a resource Laravel Cloud builds ASYNCHRONOUSLY.
 *
 * A database cluster, a cache and a bucket each answer the same two questions
 * with a different vocabulary, and a caller waiting for one to become usable
 * asks exactly those two — never which state it is in. This interface is what
 * lets it ask ONCE per attempt: without it a caller holds a closure per
 * question and reads the API twice per poll, and two reads can disagree.
 *
 * It extends BackedEnum because every one of these vocabularies IS a backed
 * enum — which is also what lets a caller put the raw status value into a
 * failure message without reaching past this interface.
 */
interface ResourceStatus extends BackedEnum
{
    /**
     * Usable — and therefore attachable to an environment.
     */
    public function isReady(): bool;

    /**
     * On its way somewhere, and able to become ready without anyone
     * intervening. False for a state that waiting cannot fix, so a caller can
     * stop rather than sit out its whole ceiling.
     */
    public function isSettling(): bool;
}
