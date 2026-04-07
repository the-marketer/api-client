<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Transactionals;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class SendSms extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        public string $to,
        #[Assert\NotBlank]
        public string $content,
    ) {}
}
