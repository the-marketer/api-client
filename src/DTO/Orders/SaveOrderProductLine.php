<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Orders;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class SaveOrderProductLine extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public int $product_id,
        #[Assert\NotBlank]
        #[Assert\Type('float')]
        public float $price,
        #[Assert\NotBlank]
        #[Assert\Type('int')]
        public int $quantity,
        #[Assert\NotBlank]
        public string $variation_sku,
    ) {}

    public static function validateAndCreate(array $data): static
    {
        $data = self::normalizeNumericScalars($data);
        $instance = new static(...$data);
        $instance->validate();

        return $instance;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function normalizeNumericScalars(array $data): array
    {
        if (array_key_exists('product_id', $data) && is_numeric($data['product_id'])) {
            $data['product_id'] = (int) $data['product_id'];
        }
        if (array_key_exists('quantity', $data) && is_numeric($data['quantity'])) {
            $data['quantity'] = (int) $data['quantity'];
        }
        if (array_key_exists('price', $data) && is_numeric($data['price'])) {
            $data['price'] = (float) $data['price'];
        }

        return $data;
    }

    public function toApiPayload(): array
    {
        return []; // TODO
    }
}
