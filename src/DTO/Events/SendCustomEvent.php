<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Events;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class SendCustomEvent extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $event
    ) {
    }
}
