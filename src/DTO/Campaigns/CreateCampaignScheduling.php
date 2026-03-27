<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Campaigns;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class CreateCampaignScheduling extends Data
{
    public function __construct(
        #[Required]
        #[Rule('date_format:Y-m-d H:i')]
        public string $send_at,
        #[Required]
        #[Rule('integer', 'in:0,1')]
        public int $use_optimal_time,
        #[Required]
        #[Rule('in:opening,buying')]
        public string $optimize_for,
    ) {
    }
}
