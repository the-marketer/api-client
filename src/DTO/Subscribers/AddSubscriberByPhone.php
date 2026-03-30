<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Subscribers;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class AddSubscriberByPhone extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        public string $phone,
        #[Assert\NotBlank(allowNull: true)]
        public ?string $firstname = null,
        #[Assert\NotBlank(allowNull: true)]
        public ?string $lastname = null,
    ) {}

    public function toApiPayload(): array
    {
        $body = ['phone' => $this->phone];
        if ($this->firstname !== null && $this->firstname !== '') {
            $body['firstname'] = $this->firstname;
        }

        if ($this->lastname !== null && $this->lastname !== '') {
            $body['lastname'] = $this->lastname;
        }

        return $body;
    }
}