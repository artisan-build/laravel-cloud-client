<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Support;

use ArtisanBuild\LaravelCloudClient\Enums\DaemonType;
use InvalidArgumentException;

/**
 * One entry in an instance's `background_processes` array.
 *
 * A typed constructor rather than a hand-built array, because the shape has
 * three rules the wire will not remind you of and Cloud only reports as a 422
 * against a resource the customer is already paying for:
 *
 *  - `type` and `processes` are BOTH required on every entry. The spec's
 *    top-level `required` array does not say so — the requirement lives on
 *    `background_processes.items.required`.
 *  - `command` is required for `custom` and "not applicable" for `worker`.
 *    Sending one with a worker is a rejection, not a harmless extra.
 *  - `processes` is an integer 1-10.
 *
 * The optional `config` knobs the schema also allows (`tries`, `sleep`,
 * `rest`, `timeout`, `force`, `backoff`) are deliberately NOT expressible
 * here: nothing in this package needs them, and a guessed value is worse than
 * an absent one because the platform's own default is the informed choice.
 * Whoever adds `backoff` should know before they do that SQS refuses a
 * `DelaySeconds` above 900, the same ceiling the app-side job backoffs are
 * capped at.
 */
final readonly class BackgroundProcess
{
    /**
     * @param  array<string, string>  $config
     */
    private function __construct(
        public DaemonType $type,
        public int $processes,
        public ?string $command,
        public array $config,
    ) {}

    /**
     * A queue worker.
     *
     * `connection` is the queue connection the worker consumes, and for a
     * managed queue the schema is explicit that it "must use 'cloud'" — hence
     * the default. `queue` is the queue NAME, and it has to agree with the
     * name the deployed application dispatches to.
     */
    public static function worker(
        int $processes = 1,
        string $connection = 'cloud',
        string $queue = 'default',
    ): self {
        return new self(
            type: DaemonType::Worker,
            processes: self::validProcesses($processes),
            // "Not applicable for 'worker' type" — and an inapplicable key is
            // a 422 here, not something Cloud quietly drops.
            command: null,
            config: [
                'connection' => self::nonEmpty($connection, 'connection'),
                'queue' => self::nonEmpty($queue, 'queue'),
            ],
        );
    }

    /**
     * An arbitrary command run as a daemon.
     *
     * Nothing in Scalpels provisions one of these — a long-running worker
     * process is a standing prohibition there — but the shape belongs with the
     * worker it is the alternative to, so that the `command`/`type` pairing is
     * enforced in one place rather than at each call site.
     */
    public static function custom(string $command, int $processes = 1): self
    {
        return new self(
            type: DaemonType::Custom,
            processes: self::validProcesses($processes),
            command: self::nonEmpty($command, 'command'),
            config: [],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $entry = [
            'type' => $this->type->value,
            'processes' => $this->processes,
        ];

        if ($this->command !== null) {
            $entry['command'] = $this->command;
        }

        if ($this->config !== []) {
            $entry['config'] = $this->config;
        }

        return $entry;
    }

    private static function validProcesses(int $processes): int
    {
        if ($processes < 1 || $processes > 10) {
            throw new InvalidArgumentException('A background process count must be between 1 and 10.');
        }

        return $processes;
    }

    private static function nonEmpty(string $value, string $field): string
    {
        if (trim($value) === '') {
            throw new InvalidArgumentException(sprintf('A background process %s must not be empty.', $field));
        }

        return $value;
    }
}
