<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;

final class ExceptionsTest extends PHPUnitTestCase
{
    public function testApiExceptionStoresMessageAndCode(): void
    {
        $e = new ApiException('msg', 422);

        $this->assertSame('msg', $e->getMessage());
        $this->assertSame(422, $e->getCode());
    }

    public function testValidationExceptionDefaultsTo400(): void
    {
        $e = new ValidationException('bad');

        $this->assertSame(400, $e->getCode());
    }

    public function testUnauthorizedException(): void
    {
        $e = new UnauthorizedException('nope', 401);

        $this->assertSame('nope', $e->getMessage());
        $this->assertSame(401, $e->getCode());
    }

    public function testCustomerNotFoundException(): void
    {
        $e = new CustomerNotFoundException('missing', 404);

        $this->assertSame(404, $e->getCode());
    }

    public function testMethodNotAllowedException(): void
    {
        $e = new MethodNotAllowedException('GET only', 405);

        $this->assertSame(405, $e->getCode());
    }
}
