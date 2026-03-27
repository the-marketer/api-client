<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Orders;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class SaveOrderProductLine extends Data
{
    public function __construct(
        #[Required]
        #[IntegerType]
        public int $product_id,
        #[Required]
        #[Numeric]
        public float $price,
        #[Required]
        #[IntegerType]
        public int $quantity,
        #[Required]
        #[Rule('string', 'filled')]
        public string $variation_sku,
    ) {
    }
}
