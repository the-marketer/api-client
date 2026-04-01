<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Campaigns;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class CreateCampaignTracking extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $utm_campaign,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $utm_medium,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $utm_source,
    ) {}
}
