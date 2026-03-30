<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Loyalty;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\Data;

/**
 * Payload pentru {@see \NotificationService\Sdk\Internal\LoyaltyApi::managePoints()}.
 */
class ManageLoyaltyPoints extends Data
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank]
        #[Assert\Choice(['increase', 'decrease'])]
        public string $action,
        #[Assert\NotBlank]
        #[Assert\Positive]
        public int $points,
    ) {
    }
}