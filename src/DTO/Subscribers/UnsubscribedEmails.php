<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Subscribers;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class UnsubscribedEmails extends Data
{
    public function __construct(
        #[Required]
        #[Rule('string', 'filled')]
        public string $date_from,
        #[Required]
        #[Rule('string', 'filled')]
        public string $date_to,
    ) {
    }
}
