<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Subscribers;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class RemoveSubscriber extends Data
{
    public function __construct(
        #[Required]
        #[Email]
        public string $email,
        public ?string $channels = null,
    ) {
    }
}
