<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Reports;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;

abstract class AbstractReportsQuery extends Data
{

    public function __construct(
        #[Required]
        #[Rule('date')]
        public string $start,
        #[Required]
        #[Rule('date')]
        public string $end,
        #[Sometimes]
        #[Rule('nullable', 'date', 'filled')]
        public ?string $previous_start = null,
        #[Sometimes]
        #[Rule('nullable', 'date', 'filled')]
        public ?string $previous_end = null,
    ) {
    }
}
