<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Reviews;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use TheMarketer\ApiClient\Common\AbstractPayload;

class ProductReviewsQuery extends AbstractPayload
{
    public function __construct(
        #[Rule('nullable', 'integer', 'min:1')]
        public ?int $t = null,
        #[Rule('nullable', 'integer', 'min:1')]
        public ?int $page = null,
        #[Rule('nullable', 'integer', 'min:1', 'max:1000000')]
        public ?int $perPage = null,
    ) {
    }

    public function toApiPayload(): array
    {
        $q = [];

        if ($this->page !== null) {
            $q['page'] = $this->page;
        }

        if ($this->perPage !== null) {
            $q['perPage'] = $this->perPage;
        }

        if ($this->t !== null) {
            $q['t'] = $this->t;
        }

        return $q;
    }
}
