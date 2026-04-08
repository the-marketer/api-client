<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\AppPush;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class RemoveMobilePushToken extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['ios', 'android'])]
        public string $type,
    ) {
    }
}
