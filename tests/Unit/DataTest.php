<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use TheMarketer\ApiClient\DTO\Campaigns\CampaignId;
use TheMarketer\ApiClient\DTO\Credentials\CheckCredentials;

/**
 * Verifică comportamentul de bază al {@see \TheMarketer\ApiClient\Common\Data} prin DTO-uri concrete.
 */
final class DataTest extends PHPUnitTestCase
{
    public function testValidateAndCreateFiltersUnknownKeys(): void
    {
        $dto = CampaignId::validateAndCreate(['id' => '42', 'extra' => 'ignored']);

        $this->assertSame('42', $dto->id);
    }

    public function testToArrayIncludesScalarProperties(): void
    {
        $dto = CheckCredentials::validateAndCreate(['k' => 'a', 'r' => 'b', 'u' => 'c']);

        $this->assertSame(['k' => 'a', 'r' => 'b', 'u' => 'c'], $dto->toArray());
    }
}
