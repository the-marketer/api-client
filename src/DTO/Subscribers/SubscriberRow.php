<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Subscribers;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class SubscriberRow extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        public ?string $add_tags = null,
        public ?string $firstname = null,
        public ?string $lastname = null,
        public ?string $phone = null,
        public ?string $city = null,
        public ?string $country = null,
        public ?string $birthday = null,
        public ?string $channels = null,
        public ?array $attributes = null,
    ) {}

    public function toApiPayload(): array
    {
        $body = ['email' => trim($this->email)];

        foreach ([
                     'firstname' => $this->firstname,
                     'lastname' => $this->lastname,
                     'add_tags' => $this->add_tags,
                     'phone' => $this->phone,
                     'city' => $this->city,
                     'country' => $this->country,
                     'birthday' => $this->birthday,
                     'channels' => $this->channels,
                 ] as $key => $value) {
            if ($value !== null && $value !== '') {
                $body[$key] = $value;
            }
        }

        if ($this->attributes !== null && $this->attributes !== []) {
            $body['attributes'] = $this->attributes;
        }

        return $body;
    }
}
