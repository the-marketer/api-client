<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\MerchantPro;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class MerchantProSettings extends AbstractPayload
{
    public function __construct(
        #[Assert\Type('string')]
        public ?string $product_feed_url = null,
        #[Assert\Type('string')]
        public ?string $inventory_feed_url = null,
        #[Assert\Type('string')]
        public ?string $order_feed_url = null,
        #[Assert\Type('string')]
        public ?string $api_key = null,
        #[Assert\Type('string')]
        public ?string $api_password = null,
    ) {}

    public static function validateAndCreate(array $data): static
    {
        foreach (
            [
                'product_feed_url',
                'inventory_feed_url',
                'order_feed_url',
                'api_key',
                'api_password',
            ] as $key
        ) {
            if (!array_key_exists($key, $data) || !is_string($data[$key])) {
                continue;
            }
            $trimmed = trim($data[$key]);
            $data[$key] = $trimmed === '' ? null : $trimmed;
        }

        return parent::validateAndCreate($data);
    }

    /**
     * @return array<string, string>
     */
    public function toApiPayload(): array
    {
        return self::filterNonEmpty([
            'product_feed_url' => $this->product_feed_url === null ? null : trim($this->product_feed_url),
            'inventory_feed_url' => $this->inventory_feed_url === null ? null : trim($this->inventory_feed_url),
            'order_feed_url' => $this->order_feed_url === null ? null : trim($this->order_feed_url),
            'api_key' => $this->api_key === null ? null : trim($this->api_key),
            'api_password' => $this->api_password === null ? null : trim($this->api_password),
        ]);
    }
}
