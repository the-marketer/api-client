<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Orders;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class SaveOrder extends AbstractPayload
{
    /**
     * @param  list<SaveOrderProductLine>  $products
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public int $number,
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email_address,
        #[Assert\NotBlank]
        public string $phone,
        #[Assert\NotBlank]
        public string $firstname,
        #[Assert\NotBlank]
        public string $lastname,
        #[Assert\NotBlank]
        public string $city,
        #[Assert\NotBlank]
        public string $county,
        #[Assert\NotBlank]
        public string $address,
        #[Assert\NotBlank]
        #[Assert\Type('int')]
        public int $discount_value,
        #[Assert\NotBlank]
        public string $discount_code,
        #[Assert\NotBlank]
        #[Assert\Type('float')]
        public float $shipping,
        #[Assert\NotBlank]
        #[Assert\Type('float')]
        public float $tax,
        #[Assert\NotBlank]
        #[Assert\Type('float')]
        public float $total_value,
        #[Assert\Count(min: 1, minMessage: 'products must not be empty.')]
        #[Assert\Valid]
        public array $products,
    ) {}

    public static function validateAndCreate(array $data): static
    {
        if (isset($data['email_address']) && is_string($data['email_address'])) {
            $data['email_address'] = trim($data['email_address']);
        }
        $rawProducts = $data['products'] ?? [];
        if (!is_array($rawProducts)) {
            $rawProducts = [];
        }
        $products = array_map(
            static fn(array $item): SaveOrderProductLine => SaveOrderProductLine::validateAndCreate($item),
            $rawProducts,
        );
        $data['products'] = $products;

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
        foreach (['number', 'discount_value'] as $key) {
            if (!array_key_exists($key, $data)) {
                continue;
            }
            $v = $data[$key];
            if (is_string($v) && is_numeric($v)) {
                $data[$key] = (int) $v;
            } elseif (is_float($v)) {
                $data[$key] = (int) $v;
            }
        }
        foreach (['shipping', 'tax', 'total_value'] as $key) {
            if (!array_key_exists($key, $data)) {
                continue;
            }
            $v = $data[$key];
            if (is_string($v) && is_numeric($v)) {
                $data[$key] = (float) $v;
            } elseif (is_int($v)) {
                $data[$key] = (float) $v;
            }
        }

        return $data;
    }

    /**
     * Corp JSON pentru {@see \NotificationService\Sdk\Internal\OrdersApi::saveOrder()} — aceleași chei ca input-ul API,
     * cu `products` ca listă de array-uri (linii) și email tăiat la capete.
     *
     * @return array<string, mixed>
     */
    public function toApiPayload(): array
    {
        $payload = $this->toArray();
        if (isset($payload['email_address']) && is_string($payload['email_address'])) {
            $payload['email_address'] = trim($payload['email_address']);
        }

        return $payload;
    }
}
