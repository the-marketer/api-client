<?php

namespace TheMarketer\ApiClient\Common;

use Spatie\LaravelData\Data;

abstract class AbstractPayload extends Data implements ApiPayloadInterface
{
    public function toApiPayload(): array
    {
        return $this->toArray();
    }
}