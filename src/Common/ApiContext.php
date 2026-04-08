<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Common;

use TheMarketer\ApiClient\Gateways\AbstractGateway;
use TheMarketer\ApiClient\Gateways\ApiGateway;
use TheMarketer\ApiClient\Gateways\TrackingGateway;

/**
 * @property-read ApiGateway $rest
 * @property-read TrackingGateway $tracking
 */
class ApiContext
{
    private array $gateways = [];

    public function __construct(
        public readonly Config $config,
        private readonly int $maxRetryAttempts = 1,
    ) {}

    public function __get(string $name): AbstractGateway
    {
        return $this->gateways[$name] ??= match($name) {
            'rest'     => new ApiGateway($this->config, $this->maxRetryAttempts),
            'tracking' => new TrackingGateway($this->config, $this->maxRetryAttempts),
            default    => throw new \InvalidArgumentException("Unknown gateway: $name"),
        };
    }

    public function tracking(): TrackingGateway
    {
        return $this->trackingGateway ??= new TrackingGateway($this->config, $this->maxRetryAttempts);
    }
}
