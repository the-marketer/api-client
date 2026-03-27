<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Reviews;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use TheMarketer\ApiClient\Common\AbstractPayload;

class MerchantAddReview extends AbstractPayload
{
    public function __construct(
        #[Required]
        #[Email]
        public string $email,
        #[Rule('required')]
        public string|int $product_id,
        #[Rule('nullable', 'string')]
        public ?string $name = null,
        #[Rule('nullable', 'string')]
        public ?string $date_created = null,
        #[Rule('nullable', 'integer', 'min:0')]
        public ?int $rating = null,
        #[Rule('nullable', 'string')]
        public ?string $content = null,
    ) {
    }
    
    public function toApiPayload(): array{
        $body = [
            'email' => strtolower(trim($this->email)),
            'product_id' => (string) $this->product_id,
        ];

        if ($this->name !== null && $this->name !== '') {
            $body['name'] = $this->name;
        }

        if ($this->date_created !== null && $this->date_created !== '') {
            $body['date_created'] = $this->date_created;
        }

        if ($this->rating !== null) {
            $body['rating'] = $this->rating;
        }

        if ($this->content !== null && $this->content !== '') {
            $body['content'] = $this->content;
        }

        return $body;
    }
}
