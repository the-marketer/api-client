<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Credentials;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;

class GetReferralLinkQuery extends Data
{
    public function __construct(
        #[Sometimes]
        #[Rule('nullable', 'string', 'min:1', 'email')]
        public ?string $email = null,
    ) {
    }
}
