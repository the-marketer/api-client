<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Subscribers;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class RemoveSubscriber extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        public ?string $channels = null,
    ) {
    }

    public function toApiPayload(): array
    {
        return array_filter(
            ['email' => $this->email, 'channels' => $this->channels],
            static fn ($v) => $v !== null && $v !== '',
        );
    }
}
