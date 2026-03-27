<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Subscribers;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class AddSubscriberBulk extends Data
{
    /**
     * @param list<SubscriberRow> $subscribers
     */
    public function __construct(
        #[Required]
        #[DataCollectionOf(SubscriberRow::class)]
        #[Rule('array', 'min:1')]
        public array $subscribers,
    ) {
    }
}
