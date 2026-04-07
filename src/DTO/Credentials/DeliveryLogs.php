<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Credentials;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class DeliveryLogs extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        #[Assert\Type('integer')]
        #[Assert\Range(min: 1, max: 100)]
        public ?int $per_page = null,
        #[Assert\Type('integer')]
        #[Assert\Positive]
        public ?int $page = null,
        #[Assert\Date]
        public ?string $start = null,
        #[Assert\Date]
        public ?string $end = null,
    ) {}
    
    public function toApiPayload(): array
    {
        $query = ['email' => $this->email];
        if ($this->per_page !== null) {
            $query['per_page'] = $this->per_page;
        }
        if ($this->page !== null) {
            $query['page'] = $this->page;
        }
        if ($this->start !== null) {
            $query['start'] = $this->start;
        }
        if ($this->end !== null) {
            $query['end'] = $this->end;
        }

        return $query;
    }
}
