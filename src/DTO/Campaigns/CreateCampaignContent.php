<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Campaigns;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class CreateCampaignContent extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: (500 * 1024))]
        public string $html,
    ) {}
}
