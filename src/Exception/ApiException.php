<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Exception;

use RuntimeException;

/**
 * Thrown for API error responses that are not mapped to a more specific exception
 * (e.g. 400, 403, 422, 5xx) with {@see HttpClient::decodeApiErrorMessage()} body text.
 */
class ApiException extends RuntimeException
{
    public function __construct(
        string $message = '',
        int $code = 500,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getHttpStatusCode(): int
    {
        return $this->code !== 0 ? $this->code : 500;
    }
}
