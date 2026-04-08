<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Gateways;

use TheMarketer\ApiClient\Exception\ValidationException;

final class ApiGateway extends AbstractGateway
{
    protected function assertAuthPresent(): void
    {
        if ($this->config->customerId() === '') {
            throw new ValidationException('Customer ID not provided.');
        }

        if ($this->config->restKey() === '') {
            throw new ValidationException('Rest key not provided.');
        }
    }

    protected function authQuery(): array
    {
        return [
            'k' => $this->config->restKey(),
            'u' => $this->config->customerId(),
        ];
    }

    protected function baseUrl(): string
    {
        return $this->config->baseRestUrl();
    }
}
