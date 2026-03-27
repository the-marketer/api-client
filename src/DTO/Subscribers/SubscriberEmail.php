<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Subscribers;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

/**
 * Adresă de email pentru query (ex. status) sau body JSON (ex. anonymize) — aceeași validare.
 */
class SubscriberEmail extends Data
{
    public function __construct(
        #[Required]
        #[Email]
        public string $email,
    ) {
    }
}
