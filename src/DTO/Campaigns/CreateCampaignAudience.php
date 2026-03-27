<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Campaigns;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class CreateCampaignAudience extends Data
{
    public function __construct(
        #[Required]
        #[Rule('in:all')]
        public string $audience_type,
        #[Required]
        #[Rule('boolean')]
        public bool $smart_sending,
    ) {
    }
}
