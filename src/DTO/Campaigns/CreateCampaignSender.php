<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Campaigns;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class CreateCampaignSender extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $name,
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $sender,
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $reply_to,
    ) {}
}
