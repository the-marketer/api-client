<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Reports;

use Spatie\LaravelData\Attributes\Validation\Required;
use TheMarketer\ApiClient\Enum\SmsPushReportType;

/**
 * Query pentru rapoartele SMS:
 * {@see \NotificationService\Sdk\Internal\ReportsApi::getSmsCampaigns()} și
 * {@see \NotificationService\Sdk\Internal\ReportsApi::getSmsAutomation()}.
 */
class GetSmsReportsQuery extends AbstractReportsQuery
{
    public function __construct(
        #[Required]
        public SmsPushReportType $type,
        string $start,
        string $end,
        ?string $previous_start = null,
        ?string $previous_end = null,
    ) {
        parent::__construct($start, $end, $previous_start, $previous_end);
    }
}
