<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Subscribers;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\Data;

/**
 * Adresă de email pentru query (ex. status) sau body JSON (ex. anonymize) — aceeași validare.
 */
class SubscriberEmail extends Data
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
    ) {
    }
}