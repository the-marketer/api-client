<?php

namespace TheMarketer\ApiClient\Common;

interface ApiPayloadInterface
{
    public function toApiPayload(): array;
}