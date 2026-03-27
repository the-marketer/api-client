<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Events;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class SendCustomEvent extends Data
{
    public function __construct(
        #[Rule('required', 'email')]
        public string $email,
        #[Rule('required', 'string')]
        public string $event
    ) {
    }
}
