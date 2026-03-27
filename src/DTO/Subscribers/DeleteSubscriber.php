<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Subscribers;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\RequiredWithout;
use Spatie\LaravelData\Data;

class DeleteSubscriber extends Data
{
    public function __construct(
        #[Email]
        #[RequiredWithout('phone')]
        public ?string $email = null,
        #[RequiredWithout('email')]
        public ?string $phone = null,
    ) {
    }
}
