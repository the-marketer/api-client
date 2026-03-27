<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Exception;

use RuntimeException;

/**
 * Thrown when required fields are missing or invalid before an HTTP request is sent.
 * Uses HTTP status code 400 by default.
 */
class ValidationException extends RuntimeException
{
    public function __construct(
        string $message = '',
        int $code = 400,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getHttpStatusCode(): int
    {
        return $this->code !== 0 ? $this->code : 400;
    }
}
