<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Campaigns;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

/**
 * Identificator campanie pentru rute `/{id}/...` (ex. {@see \NotificationService\Sdk\Internal\CampaignsApi::getEmailReport()}).
 */
class CampaignId extends Data
{
    public function __construct(
        #[Required]
        #[Rule('string', 'filled')]
        public string $id,
    ) {
    }
}
