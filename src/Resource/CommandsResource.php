<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Resource;

use ArtisanBuild\LaravelCloudClient\Exceptions\ApiException;
use ArtisanBuild\LaravelCloudClient\Exceptions\AuthenticationException;
use ArtisanBuild\LaravelCloudClient\Exceptions\NotFoundException;
use ArtisanBuild\LaravelCloudClient\Exceptions\RateLimitException;
use ArtisanBuild\LaravelCloudClient\Exceptions\ValidationException;
use ArtisanBuild\LaravelCloudClient\Requests\Commands\CancelCommand;
use ArtisanBuild\LaravelCloudClient\Requests\Commands\ExecuteCommand;
use ArtisanBuild\LaravelCloudClient\Requests\Commands\GetCommand;
use ArtisanBuild\LaravelCloudClient\Requests\Commands\GetCommandOutput;
use ArtisanBuild\LaravelCloudClient\Requests\Commands\ListCommands;
use ArtisanBuild\LaravelCloudClient\Resource;
use Saloon\Http\Response;

/**
 * Resource for managing commands.
 */
final class CommandsResource extends Resource
{
    /**
     * List recent commands for an environment.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function list(string $environmentId): Response
    {
        return $this->send(new ListCommands(environmentId: $environmentId));
    }

    /**
     * Execute an Artisan command on an environment.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function execute(string $environmentId, string $command): Response
    {
        return $this->send(new ExecuteCommand(
            environmentId: $environmentId,
            command: $command,
        ));
    }

    /**
     * Get a specific command by ID.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function get(string $commandId): Response
    {
        return $this->send(new GetCommand(commandId: $commandId));
    }

    /**
     * Get the output of a command.
     *
     * No published Laravel Cloud operation corresponds to this call; it is unverified against the API and must not be relied upon.
     * The published command operations are list-commands, run-command, and get-command; poll get-command for command state/output.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function output(string $commandId): Response
    {
        return $this->send(new GetCommandOutput(commandId: $commandId));
    }

    /**
     * Cancel a running command.
     *
     * No published Laravel Cloud operation corresponds to this call; it is unverified against the API and must not be relied upon.
     * The published command operations are list-commands, run-command, and get-command.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function cancel(string $commandId): Response
    {
        return $this->send(new CancelCommand(commandId: $commandId));
    }
}
