<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Orders;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class UpdateOrderStatus extends Data
{
    public function __construct(
        #[Required]
        #[Rule('string', 'filled')]
        public string $order_number,
        #[Required]
        #[Rule('string', 'filled')]
        public string $order_status,
    ) {
    }
}
