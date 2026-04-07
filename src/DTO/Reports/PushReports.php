<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Reports;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;
use TheMarketer\ApiClient\Enum\SmsPushReportType;
use TheMarketer\ApiClient\Exception\ValidationException;

class PushReports extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        public SmsPushReportType $type,
        #[Assert\NotBlank]
        #[Assert\Date]
        public string $start,
        #[Assert\NotBlank]
        #[Assert\Date]
        public string $end,
        #[Assert\Date]
        public ?string $previous_start = null,
        #[Assert\Date]
        public ?string $previous_end = null,
    ) {}

    public static function validateAndCreate(array $data): static
    {
        $case = SmsPushReportType::tryFrom($data['type']);
        if ($case === null) {
            throw new ValidationException('type: The value you selected is not a valid choice.');
        }
        $data['type'] = $case;

        return parent::validateAndCreate($data);
    }

    /**
     * @return array<string, string>
     */
    public function toApiPayload(): array
    {
        return self::filterNonEmpty([
            'type' => $this->type->value,
            'start' => trim($this->start),
            'end' => trim($this->end),
            'previous_start' => $this->previous_start === null ? null : trim($this->previous_start),
            'previous_end' => $this->previous_end === null ? null : trim($this->previous_end),
        ]);
    }
}
