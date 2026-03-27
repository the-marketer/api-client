<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\AppPush;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

/**
 */
class SetAppPushToken extends Data
{
    public function __construct(
        #[Rule('required', 'email')]
        public string $email,
        #[Rule('required')]
        public string $token,
        #[Required]
        #[Rule('in:ios,android')]
        public string $type,
    ) {
    }
}
