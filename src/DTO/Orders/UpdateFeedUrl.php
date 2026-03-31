<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Orders;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class UpdateFeedUrl extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Url]
        public string $url,
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Choice(choices: ['product', 'category', 'brand'])]
        public ?string $type = null,
    ) {}


    /**
     * @return array<string, string>
     */
    public function toApiPayload(): array
    {
        $body = ['url' => $this->url];
        if ($this->type !== null) {
            $body['type'] = $this->type;
        }

        return $body;
    }
}
