<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Subscribers;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

#[Assert\Expression(
    "(this.email !== null and this.email !== '') or (this.phone !== null and this.phone !== '')",
    message: 'Either email or phone is required.'
)]
class DeleteSubscriber extends AbstractPayload
{
    public function __construct(
        #[Assert\Email]
        public ?string $email = null,
        public ?string $phone = null,
    ) {}

    public function toApiPayload(): array
    {
        return array_filter(
            [
                'email' => $this->email !== null ? trim($this->email) : null,
                'phone' => $this->phone !== null ? trim($this->phone) : null,
            ],
            static fn($v) => $v !== null && $v !== '',
        );
    }
}