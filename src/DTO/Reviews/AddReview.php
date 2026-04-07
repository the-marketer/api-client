<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Reviews;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class AddReview extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $order_id,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $review_date,
        #[Assert\Type('string')]
        public ?string $order_rating = null,
        #[Assert\Type('string')]
        public ?string $order_review = null,
        #[Assert\Type('array')]
        public ?array $product_rating = null,
        #[Assert\Type('array')]
        public ?array $product_review = null,
        #[Assert\Type('array')]
        public ?array $media_files = null,
    ) {}
}
