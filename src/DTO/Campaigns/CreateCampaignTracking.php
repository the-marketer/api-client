<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Campaigns;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class CreateCampaignTracking extends Data
{
    public function __construct(
        #[Required]
        #[Rule('string')]
        public string $utm_campaign,
        #[Required]
        #[Rule('string')]
        public string $utm_medium,
        #[Required]
        #[Rule('string')]
        public string $utm_source,
    ) {
    }
}
