<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Subscribers;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class AddSubscriberBulk extends AbstractPayload
{
    /**
     * @param list<SubscriberRow> $subscribers
     */
    public function __construct(
        #[Assert\Count(min: 1, minMessage: 'subscribers must not be empty.')]
        #[Assert\All([new Assert\Valid()])]
        public array $subscribers,
    ) {}

    public static function validateAndCreate(array $data): static
    {
        $rows = array_map(
            fn(array $item) => SubscriberRow::validateAndCreate($item),
            $data['subscribers'] ?? [],
        );

        $instance = new static($rows);
        $instance->validate();

        return $instance;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function toApiPayload(): array
    {
        return array_map(
            static fn(SubscriberRow $row): array => $row->toApiPayload(),
            $this->subscribers,
        );
    }
}