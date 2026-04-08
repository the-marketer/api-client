<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Transactionals;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class SendEmailsBulk extends AbstractPayload
{
    /**
     * @param list<SendEmail> $emails
     */
    public function __construct(
        #[Assert\Count(min: 1, minMessage: 'emails must not be empty.')]
        public array $emails,
    ) {}

    public static function validateAndCreate(array $data): static
    {
        $rows = array_map(
            fn(array $item) => SendEmail::validateAndCreate($item),
            $data['emails'] ?? [],
        );

        $instance = new static($rows);
        $instance->validate();

        return $instance;
    }

    /**
     * @return array{emails: list<array<string, mixed>>}
     */
    public function toApiPayload(): array
    {
        return [
            'emails' => array_map(
                static fn(SendEmail $row): array => $row->toApiPayload(),
                $this->emails,
            ),
        ];
    }
}
