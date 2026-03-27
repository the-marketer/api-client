<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Campaigns;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class CreateCampaignContent extends Data
{
    /** 500 KiB — aliniat cu backend `max:500*1024`. */
    public const HTML_MAX_LENGTH = 512000;

    public function __construct(
        #[Required]
        #[Rule('string', 'max:512000')]
        public string $html,
    ) {
    }
}
