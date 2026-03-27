<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\MerchantPro;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use TheMarketer\ApiClient\Common\AbstractPayload;

class MerchantProSettings extends AbstractPayload
{
    public function __construct(
        #[Rule('nullable', 'string')]
        public ?string $product_feed_url = null,
        #[Rule('nullable', 'string')]
        public ?string $inventory_feed_url = null,
        #[Rule('nullable', 'string')]
        public ?string $order_feed_url = null,
        #[Rule('nullable', 'string')]
        public ?string $api_key = null,
        #[Rule('nullable', 'string')]
        public ?string $api_password = null,
    ) {
    }

    public function toApiPayload(): array
    {
        $body = [];
        foreach (
            [
                'product_feed_url',
                'inventory_feed_url',
                'order_feed_url',
                'api_key',
                'api_password',
            ] as $field
        ) {
            $value = $this->{$field};
            if ($value !== null && trim($value) !== '') {
                $body[$field] = trim($value);
            }
        }

        return $body;
    }
}
