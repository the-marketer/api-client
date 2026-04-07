<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Products;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class CreateProduct extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $id,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $sku,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $name,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $description,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $url,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $main_image,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $category,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $brand,
        #[Assert\NotBlank]
        #[Assert\Type('float')]
        public float $acquisition_price,
        #[Assert\NotBlank]
        #[Assert\Type('float')]
        public float $price,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $sale_price,
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        public int $availability,
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        public int $stock,
        #[Assert\Count(exactly: 2)]
        #[Assert\All([
            new Assert\NotBlank(),
            new Assert\Type('string'),
        ])]
        public array $media_gallery,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $created_at,
        /** @var array<string, string>|null */
        #[Assert\Type('array')]
        #[Assert\All([
            new Assert\Type('string'),
        ])]
        public ?array $extra_attributes = null,
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type('string')]
        public ?string $sale_price_start_date = null,
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type('string')]
        public ?string $sale_price_end_date = null,
    ) {}

    public function toApiPayload(): array
    {
        $payload = parent::toApiPayload();

        if ($this->sale_price_start_date === null) {
            unset($payload['sale_price_start_date']);
        }
        if ($this->sale_price_end_date === null) {
            unset($payload['sale_price_end_date']);
        }

        return $payload;
    }
}
