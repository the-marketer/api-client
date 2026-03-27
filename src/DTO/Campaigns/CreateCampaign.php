<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Campaigns;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use TheMarketer\ApiClient\Common\AbstractPayload;

/**
 * Payload pentru {@see \NotificationService\Sdk\Internal\CampaignsApi::create()}.
 */
class CreateCampaign extends AbstractPayload
{
    public function __construct(
        #[Required]
        #[Rule('in:sms,email,push')]
        public string $type,
        #[Required]
        #[Rule('in:ecommerce,regular,plaintext')]
        public string $mode,
        #[Required]
        public CreateCampaignSender $sender,
        #[Required]
        public CreateCampaignAudience $audience,
        #[Required]
        public CreateCampaignSubject $subject,
        #[Required]
        public CreateCampaignContent $content,
        #[Required]
        public CreateCampaignScheduling $scheduling,
        #[Required]
        public CreateCampaignTracking $tracking,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toCampaignsApiPayload(): array
    {
        return $this->toApiPayload();
    }
}
