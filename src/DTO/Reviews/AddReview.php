<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Reviews;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

/** @phpstan-type NestedProductFields array<int|string, array<int|string, string>> */
class AddReview extends Data
{
    /**
     * @param  NestedProductFields|null  $product_rating  ex. product_rating[0][3333]
     * @param  NestedProductFields|null  $product_review  ex. product_review[0][3333]
     * @param  NestedProductFields|null  $media_files     ex. media_files[0][3333]
     */
    public function __construct(
        #[Required]
        #[Rule('string')]
        public string $order_id,
        #[Required]
        #[Rule('string')]
        public string $review_date,
        #[Rule('nullable', 'string')]
        public ?string $order_rating = null,
        #[Rule('nullable', 'string')]
        public ?string $order_review = null,
        #[Rule('nullable', 'array')]
        public ?array $product_rating = null,
        #[Rule('nullable', 'array')]
        public ?array $product_review = null,
        #[Rule('nullable', 'array')]
        public ?array $media_files = null,
    ) {
    }
}
