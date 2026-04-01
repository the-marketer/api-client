<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Campaigns;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class CampaignId extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        public string $id,
    ) {}
}
