<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Campaigns;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;

/**
 * Query opțional pentru {@see \NotificationService\Sdk\Internal\CampaignsApi::getLatestCampaign()}.
 */
class GetLatestCampaignQuery extends Data
{
    public function __construct(
        #[Sometimes]
        #[Rule('nullable', 'integer', 'min:1')]
        public ?int $limit = null,
    ) {
    }
}
