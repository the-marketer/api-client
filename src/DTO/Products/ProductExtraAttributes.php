<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Products;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

/**
 * Opțional: `extra_attributes[color]` în body-ul `/product/create`.
 */
class ProductExtraAttributes extends Data
{
    public function __construct(
        #[Rule('nullable', 'string')]
        public ?string $color = null,
    ) {
    }
}
