<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Transactionals;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use TheMarketer\ApiClient\Common\AbstractPayload;

class SendEmail extends AbstractPayload
{
    public function __construct(
        #[Required]
        #[Rule('email:rfc')]
        public string $to,
        #[Required]
        public string $subject,
        #[Required]
        public string $body,
        #[Rule('nullable', 'string')]
        public ?string $from = null,
        #[Rule('nullable', 'email')]
        public ?string $reply_to = null,
        #[Rule('sometimes', 'required', 'array')]
        public ?array $attachments = null,
    ) {
    }
    
    public function toApiPayload(): array
    {
        $body = [
            'to' => trim($this->to),
            'subject' => $this->subject,
            'body' => $this->body,
        ];

        if ($this->from !== null && trim($this->from) !== '') {
            $body['from'] = trim($this->from);
        }

        if ($this->reply_to !== null && trim($this->reply_to) !== '') {
            $body['reply_to'] = trim($this->reply_to);
        }

        if ($this->attachments !== null && $this->attachments !== []) {
            $body['attachments'] = $this->attachments;
        }

        return $body;
    }
}
