<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Credentials;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;
use TheMarketer\ApiClient\Common\AbstractPayload;

class GetDeliveryLogsQuery extends AbstractPayload
{
    public function __construct(
        #[Required]
        #[Rule('email:rfc,dns')]
        public string $email,
        #[Sometimes]
        #[Rule('nullable', 'integer', 'min:1', 'max:100')]
        public ?int $per_page = null,
        #[Sometimes]
        #[Rule('nullable', 'integer', 'min:1')]
        public ?int $page = null,
        #[Sometimes]
        #[Rule('nullable', 'string', 'date')]
        public ?string $start = null,
        #[Sometimes]
        #[Rule('nullable', 'string', 'date')]
        public ?string $end = null,
    ) {
    }
    
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
