<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Common;

interface ApiPayloadInterface
{
    public function toApiPayload(): array;
}