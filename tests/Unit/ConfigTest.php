<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use TheMarketer\ApiClient\Common\Config;

final class ConfigTest extends TestCase
{
    public function testBaseRestUrlAppendsVersionSegment(): void
    {
        $c = new Config('u1', 'k1', 'https://api.example.com', 'https://track.example.com', 'tk');

        $this->assertSame('https://api.example.com/api/v1/', $c->baseRestUrl());
    }

    public function testAccessorsReturnConstructorValues(): void
    {
        $c = new Config('cid', 'rkey', 'https://rest.test/', 'https://trk.test/', 'trk-key', 'v2');

        $this->assertSame('cid', $c->customerId());
        $this->assertSame('rkey', $c->restKey());
        $this->assertSame('https://rest.test/', $c->restUrl());
        $this->assertSame('https://trk.test/', $c->trackingUrl());
        $this->assertSame('trk-key', $c->trackingKey());
        $this->assertSame('v2', $c->apiVersion());
    }

    public function testBaseRestUrlUsesCustomApiVersion(): void
    {
        $c = new Config('a', 'b', 'https://x.com', 'https://y.com', '', 'v3');

        $this->assertSame('https://x.com/api/v3/', $c->baseRestUrl());
    }
}
