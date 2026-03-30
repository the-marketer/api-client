<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Common;

abstract class AbstractApi
{
    public function __construct(
        protected readonly ApiContext $context,
    ) {}
}