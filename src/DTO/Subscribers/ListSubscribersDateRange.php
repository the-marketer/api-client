<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Subscribers;

use TheMarketer\ApiClient\Common\AbstractPayload;

class ListSubscribersDateRange extends AbstractPayload
{
    public function __construct(
        public ?string $date_from = null,
        public ?string $date_to = null,
    ) {}

    public function toApiPayload(): array
    {
        return self::filterNonEmpty(['date_from' => $this->date_from, 'date_to' => $this->date_to]);
    }
}
