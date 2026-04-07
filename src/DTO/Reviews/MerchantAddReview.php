<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Reviews;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class MerchantAddReview extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank]
        #[Assert\Type(type: ['int', 'string'])]
        public string|int $product_id,
        #[Assert\Type('string')]
        public ?string $name = null,
        #[Assert\Type('string')]
        public ?string $date_created = null,
        #[Assert\PositiveOrZero]
        #[Assert\Type('integer')]
        public ?int $rating = null,
        #[Assert\Type('string')]
        public ?string $content = null,
    ) {}

    public static function validateAndCreate(array $data): static
    {
        if (array_key_exists('email', $data) && is_string($data['email'])) {
            $data['email'] = trim($data['email']);
        }

        if (array_key_exists('rating', $data) && $data['rating'] !== null && is_string($data['rating']) && is_numeric($data['rating'])) {
            $data['rating'] = (int) $data['rating'];
        }

        return parent::validateAndCreate($data);
    }

    /**
     * @return array<string, mixed>
     */
    public function toApiPayload(): array
    {
        return self::filterNonEmpty([
            'email' => strtolower(trim($this->email)),
            'product_id' => (string) $this->product_id,
            'name' => $this->name,
            'date_created' => $this->date_created,
            'rating' => $this->rating,
            'content' => $this->content,
        ]);
    }
}
