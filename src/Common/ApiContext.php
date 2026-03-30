<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Common;

use TheMarketer\ApiClient\ApiGateway;

class ApiContext
{
    public function __construct(
        public readonly ApiGateway $http,
        public readonly Config $config,
    ) {
    }
}