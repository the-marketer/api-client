<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Subscribers;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;
use TheMarketer\ApiClient\Common\AbstractPayload;

/**
 * Query opțional pentru {@see \NotificationService\Sdk\Internal\SubscribersApi::listUnsubscribed()}
 * și {@see \NotificationService\Sdk\Internal\SubscribersApi::listSubscribed()}.
 */
class ListSubscribersDateRange extends AbstractPayload
{
    public function __construct(
        #[Sometimes]
        #[Rule('nullable', 'string')]
        public ?string $date_from = null,
        #[Sometimes]
        #[Rule('nullable', 'string')]
        public ?string $date_to = null,
    ) {
    }

    public function toListSubscribersDataRangeApiPayload (): array
    {
        return array_filter(
            ['date_from' => $this->date_from, 'date_to' => $this->date_to],
            static fn ($v) => $v !== null && $v !== '',
        );
    }
}
