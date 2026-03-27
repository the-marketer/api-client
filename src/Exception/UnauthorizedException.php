<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Exception;

use RuntimeException;

/**
 * Thrown when the API responds with HTTP 401 (e.g. invalid REST key).
 */
class UnauthorizedException extends RuntimeException
{
    public function __construct(
        string $message = 'Unauthorized',
        int $code = 401,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getHttpStatusCode(): int
    {
        return 401;
    }
}
