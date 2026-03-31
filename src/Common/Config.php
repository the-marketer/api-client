<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Common;

class Config
{
    private string $customerId;
    private string $restKey;
    private string $apiUrl;
    private string $apiVersion;

    public function __construct(
        string $customerId,
        string $restKey,
        string $apiUrl = 'https://t.themarketer.com',
        string $apiVersion = 'v1',
    )
    {
        $this->customerId = $customerId;
        $this->restKey = $restKey;
        $this->apiUrl = $apiUrl;
        $this->apiVersion = $apiVersion;
    }

    public function apiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * Base URL for API requests (host + `/api/{version}`), e.g. `https://t.themarketer.com/api/v1`.
     */
    public function baseUrl(): string
    {
        return rtrim($this->apiUrl, '/') . '/api/' . $this->apiVersion;
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
}