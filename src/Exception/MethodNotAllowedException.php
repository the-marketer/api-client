<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Exception;

use RuntimeException;
use Throwable;

/**
 * Thrown when the API responds with HTTP 405 (wrong HTTP verb for the route).
 */
class MethodNotAllowedException extends RuntimeException
{
    public function __construct(
        string $message = '',
        int $code = 405,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message !== '' ? $message : 'Method not allowed', $code, $previous);
    }
}
