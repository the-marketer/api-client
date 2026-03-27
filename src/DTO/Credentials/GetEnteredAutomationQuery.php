<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Credentials;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;
use TheMarketer\ApiClient\Common\AbstractPayload;

class GetEnteredAutomationQuery extends AbstractPayload
{
    public function __construct(
        #[Required]
        #[Rule('date_format:Y-m-d')]
        public string $date,
        #[Sometimes]
        #[Rule('nullable', 'integer', 'min:1')]
        public ?int $page = null,
        #[Sometimes]
        #[Rule('nullable', 'integer', 'min:1', 'max:100')]
        public ?int $perPage = null,
    ) {
    }

    public function toApiPayload(): array
    {
        $query = ['date' => $this->date];
        if ($this->page !== null) {
            $query['page'] = $this->page;
        }
        if ($this->perPage !== null) {
            $query['perPage'] = $this->perPage;
        }

        return $query;
    }
}
