<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Exception;

use RuntimeException;
use Throwable;

/**
 * Thrown when the API responds with 404 for a subscriber/customer lookup (e.g. status_subscriber).
 */
class CustomerNotFoundException extends RuntimeException
{
    public function __construct(
        string $message = '',
        int $code = 404,
        ?Throwable $previous = null,
    )
    {
        parent::__construct($message !== '' ? $message : 'Customer not found', $code, $previous);
    }
}
