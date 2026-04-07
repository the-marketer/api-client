<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Credentials;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class EnteredAutomation extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\DateTime(format: 'Y-m-d')]
        public string $date,
        #[Assert\Type('integer')]
        #[Assert\Positive]
        public ?int $page = null,
        #[Assert\Type('integer')]
        #[Assert\Range(min: 1, max: 100)]
        public ?int $perPage = null,
    ) {}
}
