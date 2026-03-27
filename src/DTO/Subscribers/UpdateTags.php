<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Subscribers;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use TheMarketer\ApiClient\Common\AbstractPayload;

class UpdateTags extends AbstractPayload
{
    /**
     * @param list<string|int>|null $add_tags
     * @param list<string|int>|null $remove_tags
     */
    public function __construct(
        #[Required]
        #[Rule('string', 'filled')]
        public string $email,
        #[Rule('nullable', 'array')]
        public ?array $add_tags = null,
        #[Rule('nullable', 'array')]
        public ?array $remove_tags = null,
        #[Rule('nullable', 'integer')]
        public ?int $overwrite_existing = null,
    ) {
    }

    public function toApiPayload(): array
    {
        $query = ['email' => $this->email];
        if ($this->add_tags !== []) {
            $query['add_tags'] = $this->add_tags;
        }
        if ($this->remove_tags !== []) {
            $query['remove_tags'] = $this->remove_tags;
        }
        if ($this->overwrite_existing !== null) {
            $query['overwrite_existing'] = $this->overwrite_existing;
        }

        return $query;
    }
}
