<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Credentials;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class ReferralLink extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Email]
        public ?string $email = null,
    ) {}
}
