<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Common;

class Config
{
    private string $customerId;
    private string $restKey;
    private string $restUrl;
    private string $trackingKey;
    private string $trackingUrl;
    private string $apiVersion;

    public function __construct(
        string $customerId,
        string $restKey,
        string $restUrl = 'https://t.themarketer.com',
        string $trackingUrl = 'https://t.themarketer.com',
        string $trackingKey = '',
        string $apiVersion = 'v1',
    )
    {
        $this->customerId = $customerId;
        $this->restKey = $restKey;
        $this->restUrl = $restUrl;
        $this->trackingUrl = $trackingUrl;
        $this->trackingKey = $trackingKey;
        $this->apiVersion = $apiVersion;
    }

    public function apiVersion(): string
    {
        return $this->apiVersion;
    }

    public function customerId(): string
    {
        return $this->customerId;
    }

    public function restKey(): string
    {
        return $this->restKey;
    }

    public function restUrl(): string
    {
        return $this->restUrl;
    }

    public function baseRestUrl(): string
    {
        return rtrim($this->restUrl, '/') . '/api/' . $this->apiVersion . '/';
    }

    public function trackingKey(): string
    {
        return $this->trackingKey;
    }

    public function trackingUrl(): string
    {
        return $this->trackingUrl;
    }
}
