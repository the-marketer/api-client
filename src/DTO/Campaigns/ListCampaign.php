<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Campaigns;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;
use TheMarketer\ApiClient\Exception\ValidationException;

class ListCampaign extends AbstractPayload
{
    public function __construct(
        #[Assert\Type('string')]
        public ?string $filters = null,
        #[Assert\Type('string')]
        public ?string $search = null,
        #[Assert\Type('string')]
        public ?string $type = null,
        #[Assert\Type('string')]
        public ?string $start_date = null,
        #[Assert\Type('string')]
        public ?string $page = null,
        #[Assert\Type('string')]
        public ?string $limit = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toApiPayload(): array
    {
        return self::filterNonEmpty([
            'filters' => $this->filters,
            'search' => $this->search,
            'type' => $this->type,
            'start_date' => $this->start_date,
            'page' => $this->page,
            'limit' => $this->limit,
        ]);
    }
}
