<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Reports;

use Spatie\LaravelData\Attributes\Validation\Required;
use TheMarketer\ApiClient\Enum\AudienceReportType;

/**
 * Query pentru {@see \NotificationService\Sdk\Internal\ReportsApi::getAudience()}.
 */
class GetAudienceReportsQuery extends AbstractReportsQuery
{
    public function __construct(
        #[Required]
        public AudienceReportType $type,
        string $start,
        string $end,
        ?string $previous_start = null,
        ?string $previous_end = null,
    ) {
        parent::__construct($start, $end, $previous_start, $previous_end);
    }
}
