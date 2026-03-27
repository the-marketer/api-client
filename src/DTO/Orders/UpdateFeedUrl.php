<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Orders;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;
use TheMarketer\ApiClient\Common\ApiPayloadInterface;

class UpdateFeedUrl extends Data implements ApiPayloadInterface
{
    public function __construct(
        #[Required]
        #[Rule('url')]
        public string $url,
        #[Sometimes]
        #[Rule('in:product,category,brand')]
        public ?string $type = null,
    ) {
    }

    public static function fromUrlAndOptionalType(string $url, ?string $type = null): self
    {
        $input = ['url' => $url];
        if ($type !== null) {
            $input['type'] = $type;
        }

        return self::validateAndCreate($input);
    }

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
