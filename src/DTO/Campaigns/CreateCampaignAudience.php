<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Campaigns;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class CreateCampaignAudience extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['all'])]
        public string $audience_type,
        #[Assert\Type('bool')]
        public bool $smart_sending,
    ) {}
}
