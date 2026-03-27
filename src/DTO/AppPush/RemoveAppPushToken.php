<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\AppPush;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

/**
 * Payload pentru {@see \NotificationService\Sdk\Internal\AppPushApi::removeToken()}.
 */
class RemoveAppPushToken extends Data
{
    public function __construct(
        #[Rule('required', 'email')]
        public string $email,
        #[Rule('required')]
        public string $type,
    ) {
    }
}
