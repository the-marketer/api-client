<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Campaigns;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class CreateCampaignSubject extends Data
{
    public function __construct(
        #[Required]
        #[Rule('string')]
        public string $name,
        #[Required]
        #[Rule('string')]
        public string $subject_line,
        #[Required]
        #[Rule('string')]
        public string $preview_text,
    ) {
    }
}
