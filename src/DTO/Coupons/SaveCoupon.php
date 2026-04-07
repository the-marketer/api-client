<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Coupons;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class SaveCoupon extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $code,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $type,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $value,
        #[Assert\NotBlank]
        #[Assert\Date]
        public string $expiration_date,
        #[Assert\Type('string')]
        public ?string $email = null,
    ) {}
}
