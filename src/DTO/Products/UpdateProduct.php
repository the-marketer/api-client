<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Products;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class UpdateProduct extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $id,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $sku,
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type('string')]
        public ?string $name = null,
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type('string')]
        public ?string $description = null,
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type('string')]
        public ?string $url = null,
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type('string')]
        public ?string $main_image = null,
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type('string')]
        public ?string $category = null,
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type('string')]
        public ?string $brand = null,
        #[Assert\Type('float')]
        public ?float $acquisition_price = null,
        #[Assert\Type('float')]
        public ?float $price = null,
        #[Assert\Type('string')]
        public ?string $sale_price = null,
        #[Assert\Type('integer')]
        public ?int $availability = null,
        #[Assert\Type('integer')]
        public ?int $stock = null,
        /** @var list<string>|null */
        #[Assert\Type('array')]
        #[Assert\Count(max: 2)]
        #[Assert\All([
            new Assert\Type('string'),
        ])]
        public ?array $media_gallery = null,
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type('string')]
        public ?string $created_at = null,
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
}
