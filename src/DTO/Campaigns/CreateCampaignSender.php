<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Campaigns;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class CreateCampaignSender extends Data
{
    public function __construct(
        #[Required]
        #[Rule('string')]
        public string $sender_name,
        #[Required]
        #[Email]
        public string $sender_email,
        #[Required]
        #[Email]
        public string $reply_to,
    ) {
    }
}
