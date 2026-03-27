<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Orders;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use TheMarketer\ApiClient\Common\AbstractPayload;

class SaveOrder extends AbstractPayload
{
    /**
     * @param list<array<string, mixed>> $products
     */
    public function __construct(
        #[Required]
        #[Rule('integer', 'filled')]
        public int $number,
        #[Required]
        #[Email]
        public string $email_address,
        #[Required]
        #[Rule('string', 'filled')]
        public string $phone,
        #[Required]
        #[Rule('string', 'filled')]
        public string $firstname,
        #[Required]
        #[Rule('string', 'filled')]
        public string $lastname,
        #[Required]
        #[Rule('string', 'filled')]
        public string $city,
        #[Required]
        #[Rule('string', 'filled')]
        public string $county,
        #[Required]
        #[Rule('string', 'filled')]
        public string $address,
        #[Required]
        #[Rule('integer', 'filled')]
        public int $discount_value,
        #[Required]
        #[Rule('string', 'filled')]
        public string $discount_code,
        #[Required]
        #[Rule('numeric', 'filled')]
        public float $shipping,
        #[Required]
        #[Rule('numeric', 'filled')]
        public float $tax,
        #[Required]
        #[Rule('numeric', 'filled')]
        public float $total_value,
        #[Required]
        #[DataCollectionOf(SaveOrderProductLine::class)]
        #[Rule('array', 'min:1')]
        public array $products,
    ) {
    }

    public function toSaveOrderApiPayload(): array
    {
        $payload = $this->toArray();
        if (isset($payload['email_address']) && is_string($payload['email_address'])) {
            $payload['email_address'] = trim($payload['email_address']);
        }

        return $payload;
    }

    public function toApiPayload(): array
    {
        return $this->toSaveOrderApiPayload();
    }
}
