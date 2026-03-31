<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Common;

abstract class AbstractPayload extends Data implements ApiPayloadInterface
{
    public function toApiPayload(): array
    {
        return $this->toArray();
    }

    protected static function filterNonEmpty(array $data): array
    {
        return array_filter($data, static fn($v) => $v !== null && $v !== '');
    }
}