<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Exceptions;

use Exception;
use Saloon\Http\Response;

/**
 * Base exception for all Laravel Cloud API errors.
 */
class LaravelCloudException extends Exception
{
    protected ?Response $response;

    public function __construct(string $message = '', ?Response $response = null, ?\Throwable $previous = null)
    {
        $this->response = $response;
        $code = $response?->status() ?? 0;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the HTTP response that caused the exception.
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * Get the HTTP status code from the response.
     */
    public function getStatusCode(): int
    {
        return $this->response?->status() ?? 0;
    }

    /**
     * Get the response data as an array.
     *
     * @return array<string, mixed>
     */
    public function getResponseData(): array
    {
        if ($this->response === null) {
            return [];
        }

        /** @var array<string, mixed> $data */
        $data = $this->response->json();

        return $data;
    }
}
