<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Validation\ValidationException;
use TheMarketer\ApiClient\DTO\Subscribers\DeleteSubscriber;

final class DeleteSubscriberTest extends TestCase
{
    public function testFailsWhenBothMissing(): void
    {
        $this->expectException(ValidationException::class);

        DeleteSubscriber::validateAndCreate([
            'email' => null,
            'phone' => null,
        ]);
    }

    public function testOkWithOnlyEmailKey(): void
    {
        $dto = DeleteSubscriber::validateAndCreate(['email' => 'a@b.com']);

        $this->assertSame('a@b.com', $dto->email);
        $this->assertNull($dto->phone);
    }

    public function testOkWithOnlyPhoneKey(): void
    {
        $dto = DeleteSubscriber::validateAndCreate(['phone' => '+40123456789']);

        $this->assertNull($dto->email);
        $this->assertSame('+40123456789', $dto->phone);
    }

    public function testOkWithEmailOnly(): void
    {
        $dto = DeleteSubscriber::validateAndCreate([
            'email' => 'a@b.com',
            'phone' => null,
        ]);

        $this->assertSame('a@b.com', $dto->email);
        $this->assertNull($dto->phone);
    }

    public function testOkWithPhoneOnly(): void
    {
        $dto = DeleteSubscriber::validateAndCreate([
            'email' => null,
            'phone' => '+40123456789',
        ]);

        $this->assertNull($dto->email);
        $this->assertSame('+40123456789', $dto->phone);
    }

    public function testOkWithBoth(): void
    {
        $dto = DeleteSubscriber::validateAndCreate([
            'email' => 'a@b.com',
            'phone' => '+40123456789',
        ]);

        $this->assertSame('a@b.com', $dto->email);
        $this->assertSame('+40123456789', $dto->phone);
    }
}
