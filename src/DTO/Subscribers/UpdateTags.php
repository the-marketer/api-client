<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Subscribers;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class UpdateTags extends AbstractPayload
{
    /**
     * @param string $email
     * @param array|null $add_tags
     * @param array|null $remove_tags
     * @param int|null $overwrite_existing
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        public ?array $add_tags = null,
        public ?array $remove_tags = null,
        public ?int $overwrite_existing = null,
    ) {}

    public function toApiPayload(): array
    {
        $query = ['email' => $this->email];
        if ($this->add_tags !== null && $this->add_tags !== []) {
            $query['add_tags'] = $this->add_tags;
        }
        if ($this->remove_tags !== null && $this->remove_tags !== []) {
            $query['remove_tags'] = $this->remove_tags;
        }
        if ($this->overwrite_existing !== null) {
            $query['overwrite_existing'] = $this->overwrite_existing;
        }

        return $query;
    }
}
