<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Subscribers;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class UnsubscribedEmails extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Date(message: 'date_from must be a valid date (YYYY-MM-DD).')]
        public string $date_from,
        #[Assert\NotBlank]
        #[Assert\Date(message: 'date_to must be a valid date (YYYY-MM-DD).')]
        public string $date_to,
    ) {}
}