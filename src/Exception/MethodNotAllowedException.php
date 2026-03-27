<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Exception;

use RuntimeException;

/**
 * Thrown when the API responds with HTTP 405 (wrong HTTP verb for the route).
 */
class MethodNotAllowedException extends RuntimeException
{
    public function __construct(
        string $message = 'Method not allowed',
        int $code = 405,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getHttpStatusCode(): int
    {
        return 405;
    }
}
