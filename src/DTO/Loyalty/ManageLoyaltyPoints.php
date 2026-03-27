<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Loyalty;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

/**
 * Payload pentru {@see \NotificationService\Sdk\Internal\LoyaltyApi::managePoints()}.
 */
class ManageLoyaltyPoints extends Data
{
    public function __construct(
        #[Required]
        #[Email]
        public string $email,
        #[Required]
        #[Rule('in:increase,decrease')]
        public string $action,
        #[Required]
        #[Rule('integer', 'min:1')]
        public int $points,
    ) {
    }
}
