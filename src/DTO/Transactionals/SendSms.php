<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Transactionals;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class SendSms extends Data
{
    public function __construct(
        #[Required]
        #[Rule('string', 'min:1')]
        public string $to,
        #[Required]
        #[Rule('string', 'min:1')]
        public string $content,
    ) {
    }
}
