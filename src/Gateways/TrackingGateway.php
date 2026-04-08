<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Gateways;

use TheMarketer\ApiClient\Exception\ValidationException;

final class TrackingGateway extends AbstractGateway
{
    protected function assertAuthPresent(): void
    {
        if ($this->config->trackingKey() === '') {
            throw new ValidationException('Tracking key not provided.');
        }
    }

    protected function authQuery(): array
    {
        return [];
    }

    protected function baseUrl(): string
    {
        return rtrim($this->config->trackingUrl(), '/') . '/';
    }
}
